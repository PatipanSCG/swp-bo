<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    protected $connection = 'sqlsrv_secondary';
    protected $table = 'FuelTypeID';
    protected $primaryKey = 'FuelTypeID';
        // เปิดใช้งาน timestamps ถ้าคุณมี created_at / updated_at
    public $timestamps = true;

    // ถ้าใช้ชื่อฟิลด์ที่แตกต่างจาก created_at / updated_at ให้เพิ่ม
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // ถ้า primary key ไม่ใช่ auto-increment ให้ตั้ง false (ไม่จำเป็นในกรณีนี้)
    public $incrementing = true;

    // กำหนดชนิดข้อมูลของ primary key
    protected $keyType = 'int';

    protected $fillable = ['FuelTypeName',  'Status'];

    public function nozzles()
    {
        return $this->hasMany(Nozzle::class, 'FuelTypeID', 'FuelTypeID');
    }
}
