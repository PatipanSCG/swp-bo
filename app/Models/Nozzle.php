<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nozzle extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'nozzle';
    protected $primaryKey = 'NozzleID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'DispenserID',
        'NozzleNumber',
        'FuleTypeID',
        'FlowRate',
        'LastCalibationDate',
        'MMQ',
        'Qmax',
        'Qmin',
        'SN',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์

    public function dispenser()
    {
        return $this->belongsTo(Dispenser::class, 'DispenserID');
    }

    public function fuleType()
    {
        return $this->belongsTo(FuelType::class, 'FuleTypeID', 'FuleTypeID');
    }

    public function inspectionRecords()
    {
        return $this->hasMany(WorkInspectionRecord::class, 'NozzleID');
    }

    public function workNozzles()
    {
        return $this->hasMany(WorkNozzle::class, 'NozzleID');
    }
    public function inspectionRecordForWork($workID)
{
    return $this->hasOne(WorkInspectionRecord::class, 'NozzleID', 'NozzleID')
                ->where('WorkID', $workID);
}
}
