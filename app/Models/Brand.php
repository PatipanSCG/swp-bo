<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    // ชื่อตารางในฐานข้อมูล
    protected $table = 'brand';

    // Primary key
    protected $primaryKey = 'BrandID';

    // เปิด timestamps
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    // ฟิลด์ที่สามารถบันทึกลงฐานข้อมูลได้
    protected $fillable = [
        'BrandName',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ตัวอย่าง relationship (ถ้ามี)
    public function stations()
    {
        return $this->hasMany(Station::class, 'BrandID');
    }

    public function modals()
    {
        return $this->hasMany(Modal::class, 'BrandID');
    }
}
