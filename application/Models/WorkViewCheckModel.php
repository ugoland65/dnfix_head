<?php
namespace App\Models;

use App\Core\BaseModel;

class WorkViewCheckModel extends BaseModel {

	protected $table = 'work_view_check';
	//protected $primaryKey = 'idx';  //기본값 idx

    protected $fillable = [
        'mode',
        'tidx',
        'mb_idx',
        'reg',
        'reg_date',
    ];

	/**
	 * 관리자 릴레이션
	 */
	public function admin()
	{
		return $this->hasOne(AdminModel::class, 'idx', 'mb_idx')->select('idx', 'ad_nick', 'ad_name', 'ad_image');
	}

}