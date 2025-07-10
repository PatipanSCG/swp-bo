<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UserSystem extends Model
{
     use Notifiable;

    protected $connection = 'sqlsrv_secondary'; // เชื่อม SQL Server
    protected $table = 'mst_user';
    protected $primaryKey = 'RowKey';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'UserName', 'Password', 'NameTH', 'Email', 'RowStatus', 'role'
    ];

    protected $hidden = ['Password'];

    public function getAuthPassword()
    {
        return $this->Password;
    }
}
