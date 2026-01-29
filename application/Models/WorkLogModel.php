<?php
namespace App\Models;

use App\Core\BaseModel;

class WorkLogModel extends BaseModel {

	protected $table = 'work_log';
	//protected $primaryKey = 'idx';  //기본값 idx

	protected $fillable = [
		'subject',
		'state',
		'body',
		'category',
		'reg_idx',
		'target_mb',
		'reg',
		'reg_date',
		'file',
		'view_check',
		'cmt_s_count',
		'cmt_b_count',
	];

}