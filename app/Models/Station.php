<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Station extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'stations';
    protected $primaryKey = 'StationID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'StationName',
        'TaxID',
        'BrandID',
        'Address',
        'Province',
        'Distric',
        'Subdistric',
        'Postcode',
        'last',
        'long',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'BrandID');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'StationID');
    }

    public function works()
    {
        return $this->hasMany(Work::class, 'StationID');
    }

    public function dispensers()
    {
        return $this->hasMany(Dispenser::class, 'StationID');
    }

    public function comunicataes()
    {
        return $this->hasMany(Comunicatae::class, 'StationID');
    }

    public function inspections()
    {
        return $this->hasMany(WorkInspectionRecord::class, 'StationID');
    }

    public function workNozzles()
    {
        return $this->hasMany(WorkNozzle::class, 'StationID');
    }
    public function province()
    {
        return $this->belongsTo(Province::class, 'Province', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'Distric', 'code');
    }

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class, 'Subdistric', 'code');
    }
    
    public function nozzle()
    {
        return $this->hasManyThrough(Nozzle::class, Dispenser::class, 'StationID', 'DispenserID');
    }
}
