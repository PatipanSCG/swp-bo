<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkNozzle extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'worknozzle';
    protected $primaryKey = 'WorkNozzleID';

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
        'WIRID',
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

    public function inspectionRecord()
    {
        return $this->belongsTo(WorkInspectionRecord::class, 'WIRID');
    }
}
