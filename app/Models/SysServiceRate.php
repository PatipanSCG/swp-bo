<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SysServiceRate extends Model
{
     protected $connection = 'sqlsrv_secondary';
    use HasFactory;


    protected $table = 'sys_service_rates'; // ชื่อตาราง
    protected $primaryKey = 'id';        // คีย์หลัก
    public $incrementing = true;         // id เป็น auto increment
    public $timestamps = true;           // ใช้ created_at / updated_at

    protected $fillable = [
        'rate_type',
        'start_nozzle',
        'end_nozzle',
        'rate_per_nozzle',
        'flat_rate',
    ];
}
