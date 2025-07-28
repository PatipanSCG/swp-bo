<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comunicatae extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'comunicatae'; // ชื่อตาราง
    protected $primaryKey = 'ComunicataeID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'StationID',
        'CustomerID',
        'UserID',
        'ComunicataeTypeID',
        'ComunicataeDetail',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์

    public function station()
    {
        return $this->belongsTo(Station::class, 'StationID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID');
    }

    public function type()
    {
        return $this->belongsTo(ComunicataeType::class, 'ComunicataeTypeID');
    }
}
