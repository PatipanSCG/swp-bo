<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $connection = 'sqlsrv_secondary';
    protected $table = 'sys_provinces'; // ชื่อตาราง

    protected $primaryKey = 'Id'; // primary key

    public $timestamps = false; // ไม่มี created_at / updated_at

    protected $fillable = [
        'Code',
        'NameInThai',
        'NameInEnglish',
        'Region',
    ];

    // ความสัมพันธ์กับ Districts (อำเภอ)
    public function districts()
    {
        return $this->hasMany(District::class, 'ProvinceId');
    }
}
