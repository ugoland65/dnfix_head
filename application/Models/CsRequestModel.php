<?php
namespace App\Models;

use App\Core\BaseModel;

class CsRequestModel extends BaseModel {

    protected $table = 'cs_request';
    //protected $primaryKey = 'idx';  //기본값 idx

    protected $fillable = [
        'category',
        'order_no',
        'order_date',
        'payment_date',
        'action_date', //DATETIME | 출고일자
        'mem_no',
        'mem_id',
        'mem_name',
        'mem_phone',
        'receiver_name',
        'receiver_phone',
        'group_nm',
        'cs_status',
        'cs_body',
        'reg_id',
        'reg_pk',
        'reg_name',
        'comment_count',
        'processor_id',
        'processor_pk',
        'processor_name',
        'process_action',
        'processor_date',
    ];

}