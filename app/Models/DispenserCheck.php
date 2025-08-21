<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DispenserCheck extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_secondary';
    protected $table = 'DispenserChecks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'WorkID',
        'StationID',
        'DispenserID',
        'inspector_name',
        'check_date',
        'total_items',
        'completed_items',
        'normal_items',
        'problem_items',
        'status',
        'remarks'
    ];

    protected $casts = [
        'WorkID' => 'integer',
        'StationID' => 'integer',
        'DispenserID' => 'integer',
        'total_items' => 'integer',
        'completed_items' => 'integer',
        'normal_items' => 'integer',
        'problem_items' => 'integer',
        'check_date' => 'datetime',
    ];

    /**
     * ความสัมพันธ์กับ Dispenser
     */
    public function dispenser()
    {
        return $this->belongsTo(Dispenser::class, 'DispenserID', 'DispenserID');
    }

    /**
     * ความสัมพันธ์กับ DispenserCheckDetail
     */
    public function details()
    {
        return $this->hasMany(DispenserCheckDetail::class, 'dispenser_check_id','id');
    }

    /**
     * ดูรายละเอียดพร้อม check items
     */
    public function detailsWithItems()
    {
        return $this->details()->with('checkItem');
    }

    /**
     * คำนวณเปอร์เซ็นต์ความสำเร็จ
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->total_items == 0) return 0;
        return round(($this->completed_items / $this->total_items) * 100, 2);
    }

    /**
     * ตรวจสอบว่าตรวจเช็คครบทุกข้อแล้วหรือไม่
     */
    public function isCompleted()
    {
        return $this->completed_items >= $this->total_items;
    }

    /**
     * อัพเดทสถิติ
     */
    public function updateStats()
    {
        $details = $this->details;
        
        $this->update([
            'completed_items' => $details->count(),
            'normal_items' => $details->where('result', 'normal')->count(),
            'problem_items' => $details->where('result', 'problem')->count(),
            'status' => $details->count() >= $this->total_items ? 'completed' : 'draft'
        ]);
    }

    /**
     * Scope สำหรับ filter ตาม work
     */
    public function scopeByWork($query, $workId)
    {
        return $query->where('WorkID', $workId);
    }

    /**
     * Scope สำหรับ filter ตาม station
     */
    public function scopeByStation($query, $stationId)
    {
        return $query->where('StationID', $stationId);
    }
}
