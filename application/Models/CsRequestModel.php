<?php
namespace App\Models;

use App\Core\BaseModel;

class CsRequestModel extends BaseModel {

    protected $table = 'cs_request';
    //protected $primaryKey = 'idx';  //기본값 idx

    protected $fillable = [
        'order_no',
        'order_date',
        'mem_no',
        'mem_id',
        'group_nm',
        'cs_status',
        'cs_body',
        'reg_id',
        'reg_pk',
        'reg_name',
    ];

}