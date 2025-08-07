<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SysTravelRate extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'sys_travel_rates';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true; // ใช้ created_at / updated_at

    protected $fillable = [
        'start_km',
        'end_km',
        'flat_rate',
        'rate_per_km',
    ];
}
