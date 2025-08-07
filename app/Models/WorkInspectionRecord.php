<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkInspectionRecord extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_secondary'; // เปลี่ยนตามชื่อ connection ของคุณ
    protected $table = 'WorkInspectionRecord';
    protected $primaryKey = 'WIRID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'WorkID',
        'StationID',
        'DispenserID',
        'NozzleID',
        'NozzleNumber',
        'MitterBegin',
        'MitterEnd',
        'Status',
        'created_by',
        'updated_by',
        'MMQ_1L',
        'MMQ_5L',
        'MPE_5L',
        'Repeat5L_1',
        'Repeat5L_2',
        'Repeat5L_3',
        'VR_1',
        'VR_2',
        'VR_3',
        'VR_4',
        'VR_5',
        'SNS_True',
        'SNS_False',
        'KarudaNumber',
        'SealNumber',
        'ExpirationDate',
        'KFactor'
    ];

    // 🔁 ตัวอย่างความสัมพันธ์
    public function work()
    {
        return $this->belongsTo(Work::class, 'WorkID');
    }

    public function nozzle()
    {
        return $this->belongsTo(Nozzle::class, 'NozzleID');
    }

    public function dispenser()
    {
        return $this->belongsTo(Dispenser::class, 'DispenserID');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'StationID');
    }
    
}
