<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispenser extends Model
{
    protected $connection = 'sqlsrv_secondary';
    protected $table = 'Dispenser';
    protected $primaryKey = 'DispenserID';
        // เปิดใช้งาน timestamps ถ้าคุณมี created_at / updated_at
    public $timestamps = true;

    // ถ้าใช้ชื่อฟิลด์ที่แตกต่างจาก created_at / updated_at ให้เพิ่ม
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // ถ้า primary key ไม่ใช่ auto-increment ให้ตั้ง false (ไม่จำเป็นในกรณีนี้)
    public $incrementing = true;

    // กำหนดชนิดข้อมูลของ primary key
    protected $keyType = 'int';
 protected $fillable = [
        'StationID',
        'BrandID',
        'Model',
        'LastCalibationDate',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์

    public function station()
    {
        return $this->belongsTo(Station::class, 'StationID');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'BrandID');
    }
 

    public function nozzles()
    {
        return $this->hasMany(Nozzle::class,  'DispenserID', 'DispenserID');
    }

    public function inspectionRecords()
    {
        return $this->hasMany(WorkInspectionRecord::class, 'DispenserID');
    }

    public function workNozzles()
    {
        return $this->hasMany(WorkNozzle::class, 'DispenserID');
    }
    public function checks()
    {
        return $this->hasMany(DispenserCheck::class, 'DispenserID', 'DispenserID');
    }

    /**
     * ดูการตรวจเช็คล่าสุด
     */
    public function latestCheck()
    {
        return $this->hasOne(DispenserCheck::class, 'DispenserID', 'DispenserID')
                    ->latest('check_date');
    }

    /**
     * ตรวจสอบว่ามีการตรวจเช็คใน work นี้แล้วหรือไม่
     */
    public function hasCheckInWork($workId)
    {
        return $this->checks()
                    ->where('WorkID', $workId)
                    ->where('status', 'completed')
                    ->exists();
    }
}