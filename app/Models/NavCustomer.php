<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavCustomer extends Model
{
    // ชื่อตารางใน NAV (มี $ อยู่ในชื่อ ต้องใช้ [] ครอบ)
    protected $table = 'SUPER CENTRAL GAS$Customer';

    // ใช้ connection ที่ตั้งไว้ใน config/database.php
    protected $connection = 'sqlsrv_NAV';

    // ปิด timestamps (ถ้าไม่ได้ใช้ created_at, updated_at)
    public $timestamps = false;

    // ถ้าไม่มี primary key หรือ primary key ไม่ใช่ id ต้องระบุ
    protected $primaryKey = 'No_';

    // ถ้า primary key ไม่ใช่ int ให้ตั้งเป็น false
    public $incrementing = false;
    protected $keyType = 'string';

    // เลือกได้ว่าจะ whitelist หรือ blacklist columns (optional)
    protected $fillable = [
        'No_', 'Name', 'City', 'Phone No_', 'E-Mail',
        // ... เพิ่ม fields ที่ต้องการใช้งาน
    ];
}
