<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysTechnicianTeam extends Model
{
       protected $connection = 'sqlsrv_secondary';
    protected $table = 'sys_technician_teams';

    protected $fillable = ['name', 'description'];

 
    public function members()
{
    return $this->hasMany(SysTechnicianTeamMember::class, 'team_id');
}
}
