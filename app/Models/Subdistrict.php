<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subdistrict extends Model
{
    protected $connection = 'sqlsrv_secondary';

    protected $table = 'sys_subdistricts'; // ชื่อตาราง

    protected $primaryKey = 'Id'; // Primary key

    public $timestamps = false; // ไม่มี created_at / updated_at

    protected $fillable = [
        'Code',
        'NameInThai',
        'NameInEnglish',
        'Latitude',
        'Longitude',
        'DistrictId',
        'ZipCode',
    ];

    // ความสัมพันธ์กับ District (อำเภอ)
    public function district()
    {
        return $this->belongsTo(District::class, 'DistrictId');
    }
}