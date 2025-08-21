<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Station;
use App\Models\Customer;
use App\Services\MPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with(['station', 'customer'])
            ->orderBy('QuotationID', 'desc')
            ->get();
        
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $stations = Station::all();
        $customers = Customer::all();
        
        return view('quotations.create', compact('stations', 'customers'));
    }

    public function store(Request $request)
    {
        try {
            DB::connection('sqlsrv_secondary')->beginTransaction();
            
            // แปลงค่า vat_enabled ให้เป็น boolean ก่อน validation
            $request->merge([
                'vat_enabled' => filter_var($request->vat_enabled, FILTER_VALIDATE_BOOLEAN)
            ]);
            
            // Validation
            $request->validate([
                'station_id' => 'required|exists:sqlsrv_secondary.stations,StationID',
                'customer_id' => 'nullable|exists:sqlsrv_secondary.customers,CustomerID',
                'items' => 'required|array',
                'service_total' => 'required|numeric',
                'travel_total' => 'required|numeric',
                'subtotal' => 'required|numeric',
                'vat_enabled' => 'nullable|boolean',
                'vat_amount' => 'nullable|numeric',
                'final_total' => 'required|numeric'
            ]);

            // Get station and customer data
            $station = Station::with(['subdistrict', 'district', 'province'])->find($request->station_id);
            $customer = null;
            
            if ($request->customer_id) {
                $customer = Customer::with(['subdistrict', 'district', 'province'])->find($request->customer_id);
            }

            // Calculate discount
            $discountAmount = 0;
            if ($request->discount_type && $request->discount_value > 0) {
                $baseAmount = 0;
                switch ($request->discount_type) {
                    case 'service':
                        $baseAmount = $request->service_total;
                        break;
                    case 'travel':
                        $baseAmount = $request->travel_total;
                        break;
                    case 'total':
                        $baseAmount = $request->subtotal;
                        break;
                }

                if ($request->discount_unit === 'percent') {
                    $discountAmount = ($request->discount_value / 100) * $baseAmount;
                } else {
                    $discountAmount = min($request->discount_value, $baseAmount);
                }
            }

            // Calculate amounts with VAT
            $subtotalAfterDiscount = $request->subtotal - $discountAmount;
            $vatAmount = 0;
            $grandTotal = $subtotalAfterDiscount;

            // คำนวณ VAT หากเปิดใช้งาน
            $vatEnabled = $request->vat_enabled;
            if ($vatEnabled) {
                $vatAmount = $subtotalAfterDiscount * 0.07;
                $grandTotal = $subtotalAfterDiscount + $vatAmount;
            }

            // Prepare customer information
            $customerName = '';
            $taxID = '';
            $address = '';

            if ($customer) {
                $customerName = $customer->CustomerName;
                $taxID = $customer->TaxID;
                $address = $customer->Address . ' ' . 
                          ($customer->subdistrict->NameInThai ?? '') . ' ' .
                          ($customer->district->NameInThai ?? '') . ' ' .
                          ($customer->province->NameInThai ?? '') . ' ' .
                          ($customer->subdistrict->ZipCode ?? '');
            } else {
                $customerName = $station->StationName;
                $taxID = $station->TaxID;
                $address = $station->Address . ' ' . 
                          ($station->subdistrict->NameInThai ?? '') . ' ' .
                          ($station->district->NameInThai ?? '') . ' ' .
                          ($station->province->NameInThai ?? '') . ' ' .
                          ($station->subdistrict->ZipCode ?? '');
            }

            // Create quotation
            $quotation = Quotation::create([
                'QuotationNo' => Quotation::generateQuotationNo(),
                'DocNo' => Quotation::generateDocNo(),
                'StationID' => $request->station_id,
                'CustomerID' => $request->customer_id,
                'CustomerName' => $customerName,
                'TaxID' => $taxID,
                'Address' => $address,
                'Items' => $request->items,
                'ServiceTotal' => $request->service_total,
                'TravelTotal' => $request->travel_total,
                'Subtotal' => $request->subtotal,
                'DiscountType' => $request->discount_type,
                'DiscountUnit' => $request->discount_unit ?: 'baht',
                'DiscountValue' => $request->discount_value ?: 0,
                'DiscountAmount' => $discountAmount,
                'SubtotalAfterDiscount' => $subtotalAfterDiscount,
                'VatEnabled' => $vatEnabled,
                'VatAmount' => $vatAmount,
                'GrandTotal' => $grandTotal,
                'Note' => $request->note,
                'Status' => 'draft',
                'IssuedBy' => Auth::user()->name ?? 'System',
                'IssuedDate' => now(),
                'Version' => 0,
                'CreditLimit' => 2000000,
                'PaymentTerm' => 30,
                'PaymentMethod' => '30 Days',
                'Salesperson' => Auth::user()->name ?? 'System',
                'SalespersonPhone' => '092-261-5378',
                'SalespersonEmail' => Auth::user()->email ?? 'example@scggroup.com',
                'JobNo' => 'NON'
            ]);

            Log::info('Quotation created successfully', ['quotation_id' => $quotation->QuotationID]);

            // Generate PDF
            try {
                $pdfPath = $this->generatePDF($quotation);
                
                if ($pdfPath) {
                    $quotation->update(['PDFPath' => $pdfPath]);
                    Log::info('PDF generated successfully', ['pdf_path' => $pdfPath]);
                } else {
                    Log::error('PDF generation failed - pdfPath is null');
                }
                
            } catch (Exception $pdfException) {
                Log::error('PDF generation error', [
                    'error' => $pdfException->getMessage(),
                    'trace' => $pdfException->getTraceAsString()
                ]);
                
                // ไม่ให้ fail ทั้งหมดเพราะ PDF
                $pdfPath = null;
            }

            DB::connection('sqlsrv_secondary')->commit();

            return response()->json([
                'success' => true,
                'message' => 'บันทึกใบเสนอราคาเรียบร้อยแล้ว',
                'quotation_id' => $quotation->QuotationID,
                'doc_no' => $quotation->DocNo,
                'quotation_no' => $quotation->QuotationNo,
                'pdf_url' => $pdfPath ? asset('storage/' . $pdfPath) : null
            ]);

        } catch (Exception $e) {
            DB::connection('sqlsrv_secondary')->rollBack();
            
            Log::error('Quotation store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['station', 'customer']);
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $stations = Station::all();
        $customers = Customer::all();
        $quotation->load(['station', 'customer']);
        
        return view('quotations.edit', compact('quotation', 'stations', 'customers'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        try {
            DB::connection('sqlsrv_secondary')->beginTransaction();

            // Create new version
            $quotation->update(['Version' => $quotation->Version + 1]);

            // Update quotation logic similar to store method
            // ... (implement similar to store method)

            DB::connection('sqlsrv_secondary')->commit();

            return response()->json([
                'success' => true,
                'message' => 'อัปเดตใบเสนอราคาเรียบร้อยแล้ว'
            ]);

        } catch (Exception $e) {
            DB::connection('sqlsrv_secondary')->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Quotation $quotation)
    {
        try {
            // Delete PDF file if exists
            if ($quotation->PDFPath) {
                $filePath = storage_path('app/public/' . $quotation->PDFPath);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $quotation->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบใบเสนอราคาเรียบร้อยแล้ว'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPDF(Quotation $quotation)
    {
        if (!$quotation->PDFPath || !file_exists(storage_path('app/public/' . $quotation->PDFPath))) {
            // Regenerate PDF if not exists
            $pdfPath = $this->generatePDF($quotation);
            if ($pdfPath) {
                $quotation->update(['PDFPath' => $pdfPath]);
            } else {
                return response()->json(['error' => 'ไม่สามารถสร้างไฟล์ PDF ได้'], 500);
            }
        }

        return response()->download(storage_path('app/public/' . $quotation->PDFPath));
    }

    private function generatePDF(Quotation $quotation)
    {
        try {
            Log::info('Starting PDF generation with mPDF', ['quotation_id' => $quotation->QuotationID]);
            
            // Load relationships
            $quotation->load(['station', 'customer']);

            // ตรวจสอบว่า view file มีอยู่หรือไม่
            $viewPath = resource_path('views/quotations/pdf.blade.php');
            if (!file_exists($viewPath)) {
                Log::error('PDF view file not found', ['path' => $viewPath]);
                return null;
            }

            Log::info('Generating PDF using mPDF', ['view' => 'quotations.pdf']);

            // สร้าง PDF ด้วย mPDF
            $mpdfService = new MPdfService();
            $mpdfService->loadView('quotations.pdf', compact('quotation'));
            
            // Create directory if not exists
            $directory = 'quotations';
            $fullDirectory = storage_path('app/public/' . $directory);
            
            if (!file_exists($fullDirectory)) {
                if (!mkdir($fullDirectory, 0755, true)) {
                    Log::error('Cannot create directory', ['directory' => $fullDirectory]);
                    return null;
                }
                Log::info('Created directory', ['directory' => $fullDirectory]);
            }
            
            // Check if directory is writable
            if (!is_writable($fullDirectory)) {
                Log::error('Directory is not writable', ['directory' => $fullDirectory]);
                return null;
            }
            
            // Generate filename
            $filename = $quotation->DocNo . '_' . time() . '.pdf';
            $filepath = $directory . '/' . $filename;
            $fullPath = storage_path('app/public/' . $filepath);
            
            Log::info('Saving PDF with mPDF', ['full_path' => $fullPath]);
            
            // Save PDF
            $mpdfService->save($fullPath);
            // Verify file was created
            if (file_exists($fullPath)) {
                $fileSize = filesize($fullPath);
                Log::info('PDF saved successfully with mPDF', [
                    'file_path' => $fullPath,
                    'file_size' => $fileSize . ' bytes'
                ]);
                
                return $filepath;
            } else {
                Log::error('PDF file not found after save attempt', ['full_path' => $fullPath]);
                return null;
            }
            
        } catch (Exception $e) {
            Log::error('PDF generation exception with mPDF', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    // API Methods for DataTables
    public function apiIndex(Request $request)
    {
        $query = Quotation::with(['station', 'customer']);

        // Apply filters if needed
        if ($request->has('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $quotations = $query->orderBy('QuotationID', 'desc')->get();

        return response()->json([
            'data' => $quotations->map(function ($quotation) {
                return [
                    'QuotationID' => $quotation->QuotationID,
                    'QuotationNo' => $quotation->QuotationNo,
                    'DocNo' => $quotation->DocNo,
                    'CustomerName' => $quotation->CustomerName,
                    'StationName' => $quotation->station->StationName ?? '',
                    'GrandTotal' => number_format($quotation->GrandTotal, 2),
                    'Status' => $quotation->Status,
                    'IssuedDate' => $quotation->IssuedDate ? $quotation->IssuedDate->format('d/m/Y') : '',
                    'created_at' => $quotation->created_at->format('d/m/Y H:i'),
                    'actions' => view('quotations.partials.actions', compact('quotation'))->render()
                ];
            })
        ]);
    }

    // Debug method สำหรับทดสอบ PDF
    public function testPDF(Request $request)
    {
        try {
            $quotationId = $request->get('id', 12); // ใช้ ID ล่าสุด
            $quotation = Quotation::with(['station', 'customer'])->find($quotationId);
            
            if (!$quotation) {
                return response()->json(['error' => 'Quotation not found'], 404);
            }

            // ทดสอบ mPDF แบบง่าย
            try {
                $mpdfService = new \App\Services\MPdfService();
                Log::info('mPDF Service created successfully');
                
                $html = '<h1>ทดสอบภาษาไทย</h1><p>สวัสดีครับ นี่คือการทดสอบ mPDF</p>';
                $mpdfService->loadHTML($html);
                
                $testPath = storage_path('app/public/test.pdf');
                $mpdfService->save($testPath);
                
                if (file_exists($testPath)) {
                    Log::info('Simple PDF test successful');
                    
                    // ทดสอบ PDF จริง
                    $pdfPath = $this->generatePDF($quotation);
                    
                    return response()->json([
                        'success' => true,
                        'test_pdf' => asset('storage/test.pdf'),
                        'quotation_pdf_path' => $pdfPath,
                        'quotation_pdf_url' => $pdfPath ? asset('storage/' . $pdfPath) : null
                    ]);
                } else {
                    return response()->json(['error' => 'Simple PDF test failed'], 500);
                }
                
            } catch (Exception $mpdfError) {
                Log::error('mPDF Error: ' . $mpdfError->getMessage());
                return response()->json([
                    'error' => 'mPDF Error: ' . $mpdfError->getMessage(),
                    'trace' => $mpdfError->getTraceAsString()
                ], 500);
            }
            
        } catch (Exception $e) {
            Log::error('Test PDF Error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}