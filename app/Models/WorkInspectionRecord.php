<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkInspectionRecord extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'workinspectionrecord';
    protected $primaryKey = 'WIRID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'WorkID',
        'StationID',
        'DispenserID',
        'NozzleID',
        'NozzleNumber',
        'MitterBegin',
        'MitterEnd',
        'BeforADJ_5L',
        'BeforADJ_20L',
        'AfterADJ_5L_1',
        'AfterADJ_5L_2',
        'AfterADJ_5L_3',
        'AfterADJ_5L_MPE',
        'AfterADJ_20L_1',
        'AfterADJ_20L_2',
        'AfterADJ_20L_3',
        'AfterADJ_20L_MPE',
        'Condition_MPE',
        'Condition_DevitionValue',
        'Condition_DevitionRange',
        'Condition_Retest20L',
        'Summary_Correct',
        'Summary_Wrong',
        'Standard',
        'Pliers',
        'CerMark_Pliers',
        'CerMark_Seal',
        'ExpirationDate',
        'K_factor',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์

    public function work()
    {
        return $this->belongsTo(Work::class, 'WorkID');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'StationID');
    }

    public function dispenser()
    {
        return $this->belongsTo(Dispenser::class, 'DispenserID');
    }

    public function nozzle()
    {
        return $this->belongsTo(Nozzle::class, 'NozzleID');
    }
}
