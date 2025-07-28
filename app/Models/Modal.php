<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Modal extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    // ชื่อตารางในฐานข้อมูล
    protected $table = 'modal';

    // Primary key
    protected $primaryKey = 'ModalID';

    // ใช้ timestamps ถ้ามี created_at, updated_at
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    // ฟิลด์ที่อนุญาตให้ mass-assignment
    protected $fillable = [
        'BrandID',
        'ModelName',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์กับ Brand (optional)
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'BrandID');
    }
}
