<?php
namespace App\Models;

use App\Core\BaseModel;

class UserModel extends BaseModel {

	protected $table = 'user';
	protected $primaryKey = 'user_idx';  //기본값 idx

    protected $fillable = [
        'user_id',
        'user_pw',
        'user_nick',
        'user_email',
        'user_mode',
        'user_level',
        'user_join_data',
        'user_kind',
        'user_point',
        'user_score',
    ];

}
