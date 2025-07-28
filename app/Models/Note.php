<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $table = 'note';
    protected $primaryKey = 'NoteID';

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'WorkID',
        'ComunicataeID',
        'Detail',
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

    public function comunicatae()
    {
        return $this->belongsTo(Comunicatae::class, 'ComunicataeID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
