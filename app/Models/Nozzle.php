<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nozzle extends Model
{
    protected $connection = 'sqlsrv_secondary';
    protected $table = 'nozzle';
    protected $primaryKey = 'NozzleID';
    public $timestamps = false;

    protected $fillable = [
        'DispenserID', 'NozzleNumber', 'FuelTypeID', 'FlowRate', 'Status'
    ];

    public function dispenser()
    {
        return $this->belongsTo(Dispenser::class, 'DispenserID', 'DispenserID');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'FuelTypeID', 'FuelTypeID');
    }
}