<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $connection = 'sqlsrv_secondary';

    protected $table = 'sys_districts'; // ชื่อตารางใน SQL Server

    protected $primaryKey = 'Id'; // ชื่อ Primary Key

    public $timestamps = false; // ไม่มี created_at, updated_at

    protected $fillable = [
        'Code',
        'NameInThai',
        'NameInEnglish',
        'ProvinceId',
    ];

    // ความสัมพันธ์กับ Province (ถ้ามี)
    public function province()
    {
        return $this->belongsTo(Province::class, 'ProvinceId');
    }
     public function subdistricts()
    {
        return $this->hasMany(Subdistrict::class, 'DistrictId');
    }
}