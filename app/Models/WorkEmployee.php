<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkEmployee extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'workemployee';
    protected $primaryKey = 'WorkEmployeeID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'WorkID',
        'UserID',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์

    public function work()
    {
        return $this->belongsTo(Work::class, 'WorkID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
