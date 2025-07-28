<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UserSystem extends Model
{
    use Notifiable;

    protected $connection = 'sqlsrv_secondary'; // เชื่อม SQL Server
    protected $table = 'users';
    protected $primaryKey = 'RowKey';
    public $incrementing = false;
    protected $keyType = 'string';
 public $timestamps = false;
    protected $fillable = [
        'UserID',
        'User_RowKey',
        'UserName',
        'NameTH',
        'NameEN',
        'Position',
        'CreatedAt',
        'UpdatedAt',
        'RoleID'
    ];

    protected $hidden = ['Password'];

    public function getAuthPassword()
    {
        return $this->Password;
    }
}
