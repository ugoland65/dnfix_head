<?php
namespace App\Models;

use App\Core\BaseModel;

class AdminModel extends BaseModel 
{

	protected $table = 'admin';
	protected $primaryKey = 'idx';  //기본값 idx

	protected $fillable = [
        'ad_id',
        'ad_pw',
        'ad_employee_id', // 사번
        'ad_role', // 직책
        'ad_title', // 직함
        'ad_nick',
        'ad_name',
        'ad_name_en',
        'ad_level',
        'ad_department', // 부서
        'ad_work_status', // 재직상태
        'ad_job_type', // 고용형태
        'ad_birth',
        'ad_joining',
        'ad_data',
        'ad_google', // 구글 아이디
        'ad_image',
        'ad_line_token',
        'ad_telegram_token',
        'is_mention', // 멘션 가능 여부
		'active',
        'AD_REG_DATE', // 등록일
        'AD_UP_DATE', // 수정일
    ];

}