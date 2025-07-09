<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // ชื่อตารางในฐานข้อมูล
    protected $table = 'mst_user';

    // กำหนด Primary Key
    protected $primaryKey = 'RowKey';

    // กรณี Primary Key เป็น UUID (string) และไม่ Auto Increment
    public $incrementing = false;
    protected $keyType = 'string';

    // ฟิลด์ที่อนุญาตให้แก้ไขได้
    protected $fillable = [
        'CompanyKey',
        'CreateDate',
        'DeptKey',
        'Email',
        'EmpCode',
        'EntryDate',
        'HeadAppvKey',
        'HeadKey',
        'InternalPhone',
        'Mobile',
        'NameEN',
        'NameTH',
        'NickName',
        'Note',
        'Password',
        'PasswordOrg',
        'PasswordText',
        'Position',
        'RowKey',
        'RowStatus',
        'UpdateDate',
        'UserName',
    ];

    // ซ่อนฟิลด์สำคัญไม่ให้แสดงใน JSON
    protected $hidden = [
        'Password',
        'PasswordText',
        'PasswordOrg',
    ];

    /**
     * Override ให้ Laravel รู้ว่าฟิลด์ Password อยู่ในคอลัมน์ 'Password'
     */
    public function getAuthPassword()
    {
        return $this->PasswordText;
    }
}
