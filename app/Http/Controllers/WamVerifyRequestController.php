<?php

namespace App\Http\Controllers;

use App\Models\WamVerifyRequest;
use App\Models\WIRActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\WamAuth;


class WamVerifyRequestController extends Controller
{
    /**
     * Get and sync data from WAM API
     */
    public function index(WamAuth $wamAuth): JsonResponse
    {
        try {
            // Get token from WAM service
            $token = $wamAuth->login();

            // Fetch data from API
            $apiResponse = $wamAuth->getVerifyRequests($token, [
                'page' => 1,
                'limit' => 100,
            ]);

            if (!isset($apiResponse['data']) || !is_array($apiResponse['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API response format',
                    'data' => []
                ], 400);
            }

            // Use secondary database connection transaction
            DB::connection('sqlsrv_secondary')->beginTransaction();

            try {
                $result = $this->bulkUpsertVerifyRequests($apiResponse['data']);

                DB::connection('sqlsrv_secondary')->commit();

                // Log activity
                $this->logActivity('WAM Verify Requests Synchronized', [
                    'total_processed' => $result['total_processed'],
                    'upserted_count' => $result['upserted_count'],
                    'errors_count' => count($result['errors'])
                ]);

                // Get updated data from database
                $verifyRequests = WamVerifyRequest::orderBy('updated_at', 'desc')
                    ->limit(100)
                    ->get();

                return response()->json([
                    'success' => true,
                    'message' => 'Data synchronized successfully',
                    'sync_result' => $result,
                    'data' => $verifyRequests
                ]);
            } catch (\Exception $e) {
                DB::connection('sqlsrv_secondary')->rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync WAM verify requests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Bulk upsert verify requests data
     */
    private function bulkUpsertVerifyRequests($apiDataArray): array
    {
        $upsertedCount = 0;
        $errors = [];
        $updatedRecords = [];
        $newRecords = [];

        foreach ($apiDataArray as $apiData) {
            try {
                $mappedData = $this->mapApiDataToDatabase($apiData);

                // Check if record exists
                $existingRecord = WamVerifyRequest::where('sheet_no', $mappedData['sheet_no'])->first();

                if ($existingRecord) {
                    // Update only if data has changed
                    $hasChanges = false;
                    foreach ($mappedData as $key => $value) {
                        if ($existingRecord->$key != $value) {
                            $hasChanges = true;
                            break;
                        }
                    }

                    if ($hasChanges) {
                        $existingRecord->update($mappedData);
                        $updatedRecords[] = $existingRecord->sheet_no;
                    }
                } else {
                    // Create new record
                    WamVerifyRequest::create($mappedData);
                    $newRecords[] = $mappedData['sheet_no'];
                }

                $upsertedCount++;
            } catch (\Exception $e) {
                $errors[] = [
                    'sheet_no' => $apiData['SheetNo'] ?? 'Unknown',
                    'error' => $e->getMessage()
                ];

                Log::warning('Error upserting verify request', [
                    'sheet_no' => $apiData['SheetNo'] ?? 'Unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'total_processed' => count($apiDataArray),
            'upserted_count' => $upsertedCount,
            'new_records' => count($newRecords),
            'updated_records' => count($updatedRecords),
            'new_records_list' => $newRecords,
            'updated_records_list' => $updatedRecords,
            'errors' => $errors
        ];
    }

    /**
     * Map API data to database format
     */
    private function mapApiDataToDatabase($apiData): array
    {
        return [
            'sheet_no' => $apiData['SheetNo'] ?? null,
            'total_request' => $apiData['TotalRequest'] ?? 0,
            'have_inspector' => $apiData['haveInspector'] ?? false,
            'merchan_name' => $apiData['MerchanName'] ?? null,
            'receive_business_type' => $apiData['ReceiveBusinessType'] ?? null,
            'status' => $apiData['Status'] ?? 'pending',
            'create_time' => isset($apiData['CreateTime'])
                ? Carbon::parse($apiData['CreateTime'])
                : null,
            'vb_first_name' => $apiData['vbFirstName'] ?? null,
            'vb_last_name' => $apiData['vbLastName'] ?? null,
            'vb_merchant_name' => $apiData['vbMerchantName'] ?? null,
            'vb_id_card' => $apiData['vbIDCard'] ?? null,
            'prov_id' => $apiData['ProvId'] ?? null,
            'vb_branch_name' => $apiData['vbBranchName'] ?? null,
            'date_of_request_start' => isset($apiData['dateOfRequestStart']) && $apiData['dateOfRequestStart']
                ? Carbon::parse($apiData['dateOfRequestStart'])
                : null,
            'vb_prov_name' => $apiData['vbProvName'] ?? null,
            'customer_name' => $apiData['CustomerName'] ?? null,
            'row_num' => $apiData['RowNum'] ?? null
        ];
    }

    /**
     * Get data from database only (with filters and pagination)
     */
    public function getFromDatabase(Request $request): JsonResponse
    {
        try {
            $query = WamVerifyRequest::query();

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('prov_id')) {
                $query->where('prov_id', $request->prov_id);
            }

            if ($request->filled('have_inspector')) {
                $query->where('have_inspector', $request->boolean('have_inspector'));
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('sheet_no', 'like', "%{$search}%")
                        ->orWhere('merchan_name', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('vb_first_name', 'like', "%{$search}%")
                        ->orWhere('vb_last_name', 'like', "%{$search}%");
                });
            }

            // Date filters
            if ($request->filled('date_from')) {
                $query->where('create_time', '>=', Carbon::parse($request->date_from));
            }

            if ($request->filled('date_to')) {
                $query->where('create_time', '<=', Carbon::parse($request->date_to)->endOfDay());
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'updated_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 20);

            if ($request->boolean('paginate', true)) {
                $verifyRequests = $query->paginate($perPage);
            } else {
                $verifyRequests = $query->get();
            }

            return response()->json([
                'success' => true,
                'data' => $verifyRequests
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get verify requests from database', [
                'error' => $e->getMessage(),
                'request_params' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Manual sync method
     */
    public function syncData(WamAuth $wamAuth): JsonResponse
    {
        return $this->index($wamAuth);
    }

    /**
     * Get single record
     */
    public function show($id): JsonResponse
    {
        try {
            $verifyRequest = WamVerifyRequest::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $verifyRequest
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'data' => null
            ], 404);
        }
    }

    /**
     * Get statistics/summary
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $stats = [
                'total_requests' => WamVerifyRequest::count(),
                'pending_requests' => WamVerifyRequest::where('status', 'pending')->count(),
                'completed_requests' => WamVerifyRequest::where('status', 'completed')->count(),
                'with_inspector' => WamVerifyRequest::where('have_inspector', true)->count(),
                'without_inspector' => WamVerifyRequest::where('have_inspector', false)->count(),
                'by_province' => WamVerifyRequest::selectRaw('vb_prov_name, COUNT(*) as count')
                    ->whereNotNull('vb_prov_name')
                    ->groupBy('vb_prov_name')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'recent_updates' => WamVerifyRequest::orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'sheet_no', 'merchan_name', 'status', 'updated_at'])
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Log activity to WIRActivity table
     */
    private function logActivity($detail, $additionalData = []): void
    {
        try {
            WIRActivity::create([
                'Detail' => $detail . (!empty($additionalData) ? ' - ' . json_encode($additionalData) : ''),
                'UserID' => auth()->id() ?? 0, // Use authenticated user ID or 0 for system
                'Status' => 'completed',
                'created_by' => auth()->id() ?? 0,
                'updated_by' => auth()->id() ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log activity', [
                'detail' => $detail,
                'error' => $e->getMessage()
            ]);
        }
    }
}
