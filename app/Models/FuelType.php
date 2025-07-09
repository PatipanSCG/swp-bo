<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    protected $connection = 'sqlsrv_secondary';
    protected $table = 'fuel_type';
    protected $primaryKey = 'FuelTypeID';
    public $timestamps = false;

    protected $fillable = ['FuelTypeName', 'OctaneLevel', 'Status'];

    public function nozzles()
    {
        return $this->hasMany(Nozzle::class, 'FuelTypeID', 'FuelTypeID');
    }
}
