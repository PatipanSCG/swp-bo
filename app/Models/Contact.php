<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'contact'; // ชื่อตารางในฐานข้อมูล
    protected $primaryKey = 'ContactID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'StationID',
        'CustomerID',
        'ContactName',
        'ContactEmail',
        'ContactPhone',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์ (optional)
    public function station()
    {
        return $this->belongsTo(Station::class, 'StationID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID');
    }
}
