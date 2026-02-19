<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductGroupingModel extends BaseModel
{

	protected $table = 'prd_grouping';
	protected $primaryKey = 'idx';  //기본값 idx

    protected $fillable = [
        'pg_subject',
        'public',
        'pg_mode',
        'prd_mode',
        'pg_state',
        'pg_sday',
        'pg_day',
        'pg_memo',
        'data',
        'reg',
    ];

}