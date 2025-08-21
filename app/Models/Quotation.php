<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Quotation extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_secondary';
    protected $table = 'quotations';
    protected $primaryKey = 'QuotationID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'QuotationNo',
        'DocNo',
        'StationID',
        'CustomerID',
        'CustomerName',
        'TaxID',
        'Address',
        'Items',
        'ServiceTotal',
        'TravelTotal',
        'Subtotal',
        'DiscountType',
        'DiscountUnit',
        'DiscountValue',
        'DiscountAmount',
        'SubtotalAfterDiscount',
        'VatEnabled',
        'VatAmount',
        'GrandTotal',
        'Note',
        'Status',
        'IssuedBy',
        'IssuedDate',
        'ValidUntil',
        'Version',
        'CreditLimit',
        'PaymentTerm',
        'PaymentMethod',
        'Salesperson',
        'SalespersonPhone',
        'SalespersonEmail',
        'JobNo',
        'PDFPath',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'Items' => 'array',
        'ServiceTotal' => 'decimal:2',
        'TravelTotal' => 'decimal:2',
        'Subtotal' => 'decimal:2',
        'DiscountValue' => 'decimal:2',
        'DiscountAmount' => 'decimal:2',
        'SubtotalAfterDiscount' => 'decimal:2',
        'VatEnabled' => 'boolean',
        'VatAmount' => 'decimal:2',
        'GrandTotal' => 'decimal:2',
        'CreditLimit' => 'decimal:2',
        'PaymentTerm' => 'integer',
        'Version' => 'integer',
        'IssuedDate' => 'datetime',
        'ValidUntil' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'Status' => 'draft',
        'DiscountUnit' => 'baht',
        'DiscountValue' => 0.00,
        'DiscountAmount' => 0.00,
        'VatEnabled' => true,
        'VatAmount' => 0.00,
        'Version' => 0,
        'CreditLimit' => 2000000.00,
        'PaymentTerm' => 30,
        'PaymentMethod' => '30 Days',
        'JobNo' => 'NON'
    ];

    // ความสัมพันธ์
    public function station()
    {
        return $this->belongsTo(Station::class, 'StationID', 'StationID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    // Accessors
    public function getFormattedGrandTotalAttribute()
    {
        return number_format($this->GrandTotal, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->Subtotal, 2);
    }

    public function getFormattedVatAmountAttribute()
    {
        return number_format($this->VatAmount, 2);
    }

    public function getFormattedDiscountAmountAttribute()
    {
        return number_format($this->DiscountAmount, 2);
    }

    public function getIssuedDateFormattedAttribute()
    {
        return $this->IssuedDate ? $this->IssuedDate->format('d/m/Y') : null;
    }

    public function getValidUntilFormattedAttribute()
    {
        return $this->ValidUntil ? $this->ValidUntil->format('d/m/Y') : null;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'sent' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            'expired' => 'warning'
        ];

        return $badges[$this->Status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'draft' => 'ร่าง',
            'sent' => 'ส่งแล้ว',
            'approved' => 'อนุมัติ',
            'rejected' => 'ปฏิเสธ',
            'expired' => 'หมดอายุ'
        ];

        return $texts[$this->Status] ?? 'ไม่ทราบ';
    }

    // Mutators
    public function setItemsAttribute($value)
    {
        $this->attributes['Items'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setIssuedDateAttribute($value)
    {
        $this->attributes['IssuedDate'] = $value ? Carbon::parse($value) : now();
    }

    public function setVatEnabledAttribute($value)
    {
        $this->attributes['VatEnabled'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    // Static Methods สำหรับสร้างเลขที่เอกสาร
    public static function generateQuotationNo()
    {
        $year = date('Y');
        $month = date('m');
        
        // หาเลขที่ล่าสุดในเดือนนี้
        $lastQuotation = static::where('QuotationNo', 'like', "QT{$year}{$month}%")
            ->orderBy('QuotationNo', 'desc')
            ->first();

        if ($lastQuotation) {
            // ดึงเลข running จากเลขที่ล่าสุด
            $lastNumber = intval(substr($lastQuotation->QuotationNo, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "QT{$year}{$month}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateDocNo()
    {
        $year = date('Y');
        
        // หาเลขที่ล่าสุดในปีนี้
        $lastDoc = static::where('DocNo', 'like', "DOC{$year}%")
            ->orderBy('DocNo', 'desc')
            ->first();

        if ($lastDoc) {
            // ดึงเลข running จากเลขที่ล่าสุด
            $lastNumber = intval(substr($lastDoc->DocNo, -6));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "DOC{$year}" . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('Status', $status);
    }

    public function scopeByStation($query, $stationId)
    {
        return $query->where('StationID', $stationId);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('CustomerID', $customerId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('IssuedDate', [$startDate, $endDate]);
    }

    public function scopeWithVat($query)
    {
        return $query->where('VatEnabled', true);
    }

    public function scopeWithoutVat($query)
    {
        return $query->where('VatEnabled', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('IssuedDate', 'desc');
    }

    // Helper Methods
    public function calculateVat()
    {
        if (!$this->VatEnabled) {
            return 0;
        }
        
        $subtotalAfterDiscount = $this->Subtotal - $this->DiscountAmount;
        return $subtotalAfterDiscount * 0.07;
    }

    public function calculateGrandTotal()
    {
        $subtotalAfterDiscount = $this->Subtotal - $this->DiscountAmount;
        $vatAmount = $this->VatEnabled ? $this->calculateVat() : 0;
        
        return $subtotalAfterDiscount + $vatAmount;
    }

    public function recalculateAmounts()
    {
        $this->SubtotalAfterDiscount = $this->Subtotal - $this->DiscountAmount;
        $this->VatAmount = $this->calculateVat();
        $this->GrandTotal = $this->calculateGrandTotal();
        
        return $this;
    }

    public function isExpired()
    {
        return $this->ValidUntil && $this->ValidUntil->isPast();
    }

    public function canBeEdited()
    {
        return in_array($this->Status, ['draft', 'sent']);
    }

    public function canBeApproved()
    {
        return $this->Status === 'sent' && !$this->isExpired();
    }

    // Method สำหรับแปลงตัวเลขเป็นภาษาไทย
    public function numberToThaiText($number)
    {
        $number = number_format($number, 2, '.', '');
        $parts = explode('.', $number);
        $integer = $parts[0];
        $decimal = $parts[1];

        $thaiNumber = [
            '0' => 'ศูนย์', '1' => 'หนึ่ง', '2' => 'สอง', '3' => 'สาม', '4' => 'สี่',
            '5' => 'ห้า', '6' => 'หก', '7' => 'เจ็ด', '8' => 'แปด', '9' => 'เก้า'
        ];

        $thaiPlace = [
            '', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'
        ];

        $result = '';
        $len = strlen($integer);

        if ($integer == 0) {
            $result = 'ศูนย์';
        } else {
            for ($i = 0; $i < $len; $i++) {
                $digit = $integer[$i];
                $place = $len - $i - 1;

                if ($digit != '0') {
                    if ($place == 1 && $digit == '1' && $len > 1) {
                        $result .= 'สิบ';
                    } elseif ($place == 1 && $digit == '2' && $len > 1) {
                        $result .= 'ยี่สิบ';
                    } elseif ($place == 0 && $digit == '1' && $len > 1 && $integer[$i-1] != '0') {
                        $result .= 'เอ็ด';
                    } else {
                        $result .= $thaiNumber[$digit];
                        if ($place > 0) {
                            $result .= $thaiPlace[$place];
                        }
                    }
                }
            }
        }

        $result .= 'บาท';

        // จัดการทศนิยม
        if ($decimal != '00') {
            if ($decimal[0] != '0') {
                if ($decimal[0] == '1') {
                    $result .= 'สิบ';
                } elseif ($decimal[0] == '2') {
                    $result .= 'ยี่สิบ';
                } else {
                    $result .= $thaiNumber[$decimal[0]] . 'สิบ';
                }
            }

            if ($decimal[1] != '0') {
                if ($decimal[1] == '1' && $decimal[0] != '0') {
                    $result .= 'เอ็ด';
                } else {
                    $result .= $thaiNumber[$decimal[1]];
                }
            }

            $result .= 'สตางค์';
        } else {
            $result .= 'ถ้วน';
        }

        return $result;
    }

    // Wrapper methods สำหรับใช้ใน View
    public function getGrandTotalInThaiText()
    {
        return $this->numberToThaiText($this->GrandTotal);
    }

    public function getSubtotalInThaiText()
    {
        return $this->numberToThaiText($this->Subtotal);
    }

    public function getVatAmountInThaiText()
    {
        return $this->numberToThaiText($this->VatAmount);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            if (empty($quotation->QuotationNo)) {
                $quotation->QuotationNo = static::generateQuotationNo();
            }
            
            if (empty($quotation->DocNo)) {
                $quotation->DocNo = static::generateDocNo();
            }

            if (empty($quotation->IssuedDate)) {
                $quotation->IssuedDate = now();
            }

            // คำนวณยอดรวมใหม่
            $quotation->recalculateAmounts();
        });

        static::updating(function ($quotation) {
            // คำนวณยอดรวมใหม่เมื่อมีการอัปเดต
            $quotation->recalculateAmounts();
        });
    }
}