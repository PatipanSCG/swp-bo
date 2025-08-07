<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    // ชื่อตาราง (ถ้าไม่ได้ตั้งชื่อเป็น 'customers' อยู่แล้ว)
    protected $table = 'customers';

    // Primary key
    protected $primaryKey = 'CustomerID';

    // เปิดใช้งาน timestamps ถ้าคุณมี created_at / updated_at
    public $timestamps = true;

    // ถ้าใช้ชื่อฟิลด์ที่แตกต่างจาก created_at / updated_at ให้เพิ่ม
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // ถ้า primary key ไม่ใช่ auto-increment ให้ตั้ง false (ไม่จำเป็นในกรณีนี้)
    public $incrementing = true;

    // กำหนดชนิดข้อมูลของ primary key
    protected $keyType = 'int';

    // กำหนด fillable fields
    protected $fillable = [
        'CustomerType',
        'CustomerName',
        'TaxID',
        'Address',
        'Province',
        'Distric',
        'Subdistric',
        'Postcode',
        'Telphone',
        'Email',
        'Phone',
        'Status',
        'created_by',
        'updated_by',
    ];
   
    public static function customerTypeList()
    {
        return [
            1 => 'ห้างหุ้นส่วนสามัญ',
            2 => 'ห้างหุ้นส่วนจำกัด',
            3 => 'บริษัทจำกัด',
            4 => 'บริษัทมหาชนจำกัด',
            5 => 'สหกรณ์',
            6 => 'มูลนิธิ',
            7 => 'สมาคม',
            8 => 'องค์กรของรัฐ',
            9 => 'นิติบุคคลต่างประเทศ',
        ];
    }
    public function getCustomerTypeNameAttribute()
    {
        return self::customerTypeList()[$this->CustomerType] ?? 'ไม่ระบุ';
    }
     public function province()
    {
        return $this->belongsTo(Province::class, 'Province', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'Distric', 'code');
    }

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class, 'Subdistric', 'code');
    }

}
