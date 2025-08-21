<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DispenserCheckDetail extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_secondary';
    protected $table = 'DispenserCheckDetails';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dispenser_check_id',
        'check_item_id',
        'result',
        'problem_description',
        'inspector_notes',
        'checked_at'
    ];

    protected $casts = [
        'dispenser_check_id' => 'integer',
        'check_item_id' => 'integer',
        'checked_at' => 'datetime',
    ];

    /**
     * ความสัมพันธ์กับ DispenserCheck
     */
    public function dispenserCheck()
    {
        return $this->belongsTo(DispenserCheck::class, 'dispenser_check_id');
    }

    /**
     * ความสัมพันธ์กับ CheckItem
     */
    public function checkItem()
    {
        return $this->belongsTo(CheckItem::class, 'check_item_id');
    }

    /**
     * ความสัมพันธ์กับ DispenserCheckImage
     */
    public function images()
    {
        return $this->hasMany(DispenserCheckImage::class, 'dispenser_check_detail_id');
    }

    /**
     * ตรวจสอบว่าเป็น normal หรือ problem
     */
    public function isNormal()
    {
        return $this->result === 'normal';
    }

    public function isProblem()
    {
        return $this->result === 'problem';
    }

    /**
     * Scope สำหรับ filter ตามผลลัพธ์
     */
    public function scopeNormal($query)
    {
        return $query->where('result', 'normal');
    }

    public function scopeProblem($query)
    {
        return $query->where('result', 'problem');
    }

    /**
     * Auto update parent stats when saved
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($detail) {
            $detail->dispenserCheck->updateStats();
        });

        static::deleted(function ($detail) {
            $detail->dispenserCheck->updateStats();
        });
    }
}
