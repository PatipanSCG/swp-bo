<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComunicataeType extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'ComunicataeType'; // ชื่อตารางในฐานข้อมูล
    protected $primaryKey = 'ComunicataeTypeID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ComunicataeName',
        'Status',
        'created_by',
        'updated_by',
    ];

    // 🔁 ความสัมพันธ์: 1 ประเภท ติดต่อได้หลายครั้ง
    public function comunicataes()
    {
        return $this->hasMany(Comunicatae::class, 'ComunicataeTypeID');
    }
}
