<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Work extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'work';
    protected $primaryKey = 'WorkID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'StationID',
        'CustomerID',
        'UserCreate',
        'AppointmentDate',
        'distance',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์ (Relations)
    public function station()
    {
        return $this->belongsTo(Station::class, 'StationID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID');
    }

    public function employees()
    {
        return $this->hasMany(WorkEmployee::class, 'WorkID');
    }

    public function nozzles()
    {
        return $this->hasMany(WorkNozzle::class, 'WorkID');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'WorkID');
    }

    public function inspections()
    {
        return $this->hasMany(WorkInspectionRecord::class, 'WorkID');
    }
}
