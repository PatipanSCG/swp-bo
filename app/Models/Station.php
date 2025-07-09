<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $connection = 'sqlsrv_secondary';
    protected $table = 'station';
    protected $primaryKey = 'StationID';
    public $timestamps = false;

    protected $fillable = [
        'StationCode', 'StationName', 'Location', 'Province', 'ContactPerson', 'Phone', 'Status',
    ];

    public function dispensers()
    {
        return $this->hasMany(Dispenser::class, 'StationID', 'StationID');
    }
}
