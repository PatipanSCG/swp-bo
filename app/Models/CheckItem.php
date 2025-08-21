<?php
// app/Models/CheckItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckItem extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_secondary';
    protected $table = 'CheckItems';
    protected $primaryKey = 'id';

    protected $fillable = [
        'item_number',
        'title',
        'equipment',
        'is_active'
    ];

    protected $casts = [
        'item_number' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope สำหรับรายการที่ active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * ความสัมพันธ์กับ DispenserCheckDetail
     */
    public function checkDetails()
    {
        return $this->hasMany(DispenserCheckDetail::class, 'check_item_id');
    }
}
