<?php
namespace App\Models;

use App\Core\BaseModel;

class CsRequestGroupModel extends BaseModel {

    protected $table = 'cs_request_group';

    protected $fillable = [
        'group_code',
        'category',
        'action_date',
        'cs_body',
        'request_count',
        'reg_id',
        'reg_pk',
        'reg_name',
    ];

}
