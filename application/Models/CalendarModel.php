<?php
namespace App\Models;

use App\Core\BaseModel;

class CalendarModel extends BaseModel {

	protected $table = 'calendar';
	//protected $primaryKey = 'idx';  //기본값 idx

	protected $fillable = [
        'subject',
        'open',
        'target_idx',
        'mode',
        'kind',
        'state',
        'date_s',
        'date_e',
        'data',
        'targrt_idx',      // 컬럼명이 실제로 이렇게 되어있어서 그대로 둠(오타 컬럼)
        'target_mb',
        'memo',
        'reg',
        'comment_count',
    ];

}