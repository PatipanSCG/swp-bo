<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WIRActivity extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'wiractivity'; // ชื่อตารางในฐานข้อมูล
    protected $primaryKey = 'WIRAID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Detail',
        'UserID',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์กับ User (ถ้าคุณใช้ Laravel Auth)
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
