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
        'ad_nick',
        'ad_name',
        'ad_name_en',
        'ad_level',
        'ad_birth',
        'ad_joining',
        'ad_data',
        'ad_image',
        'ad_line_token',
        'ad_telegram_token',
		'active',
        'AD_REG_DATE',
        'AD_UP_DATE',
    ];

}