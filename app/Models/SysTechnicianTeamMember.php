<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysTechnicianTeamMember extends Model
{
       protected $connection = 'sqlsrv_secondary';
    protected $table = 'sys_technician_team_members';

    protected $fillable = ['user_id', 'team_id'];

   public function user()
{
    return $this->belongsTo(UserSystem::class, 'user_id', 'UserID');
}

    public function team()
    {
        return $this->belongsTo(SysTechnicianTeam::class, 'team_id');
    }
}
