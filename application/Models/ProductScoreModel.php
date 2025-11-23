<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductScoreModel extends BaseModel
{

	protected $table = 'prd_score';
	protected $primaryKey = 'ps_idx';  //기본값 idx

    protected $fillable = [
        'ps_pd_idx',
        'ps_mode',
        'ps_ym',
        'ps_score',
        'ps_count',
        'ps_score_total',
        'ps_grade_count',
        'ps_grade_total',
        'ps_grade',
        'ps_grade_data',
    ];

}