<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{    protected $connection = 'sqlsrv_secondary';
    use HasFactory;
    protected $table = 'promotions';
    protected $primaryKey = 'id';
    public $timestamps = true; // ใช้ created_at, updated_at

    protected $fillable = [
        'type',
        'detail',
        'quantity',
        'unit_price',
        'status',
    ];

    // Accessor: total_value คำนวณอัตโนมัติ
    public function getTotalValueAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}
