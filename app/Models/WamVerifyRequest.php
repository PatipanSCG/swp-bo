<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WamVerifyRequest extends Model
{
    protected $connection = 'sqlsrv_secondary';
    use HasFactory;

    protected $fillable = [
        'sheet_no','total_request','have_inspector','merchan_name',
        'receive_business_type','status','create_time','vb_first_name',
        'vb_last_name','vb_merchant_name','vb_id_card','prov_id',
        'vb_branch_name','date_of_request_start','vb_prov_name',
        'customer_name','row_num',
    ];

    protected $casts = [
        'create_time' => 'datetime',
        'date_of_request_start' => 'datetime',
        'have_inspector' => 'boolean',
        'total_request' => 'integer',
        'row_num' => 'integer',
    ];
}
