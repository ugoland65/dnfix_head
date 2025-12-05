<?php
namespace App\Models;

use App\Core\BaseModel;

class RackModel extends BaseModel {

	protected $table = 'prd_rack';
	protected $primaryKey = 'idx';  //기본값 idx

    protected $fillable = [
        'name',
        'code',
        'prd',
        'memo',
    ];

}
