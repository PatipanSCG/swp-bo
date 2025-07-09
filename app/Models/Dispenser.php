<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispenser extends Model
{
    protected $connection = 'sqlsrv_secondary';
    protected $table = 'dispenser';
    protected $primaryKey = 'DispenserID';
    public $timestamps = false;

    protected $fillable = [
        'StationID', 'DispenserCode', 'FuelType', 'NozzleCount', 'Brand', 'LastInspectionDate', 'PurchaseDate', 'InstallationDate', 'Status'
    ];

    public function station()
    {
        return $this->belongsTo(Station::class, 'StationID', 'StationID');
    }

    public function nozzles()
    {
        return $this->hasMany(Nozzle::class, 'DispenserID', 'DispenserID');
    }
}