<?php
namespace App\Models;

use App\Core\BaseModel;

class PartnersModel extends BaseModel {

	protected $table = 'partners';
	protected $primaryKey = 'idx';  //기본값 idx

    protected $fillable = [
        'name',
        'category',
        'info',
        'memo',
        'reg',
        'bank_name',
        'bank_account',
        'bank_account_name',
        'created_at',
        'updated_at',
    ];

}