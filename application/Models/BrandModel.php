<?php
namespace App\Models;

use App\Core\BaseModel;

class BrandModel extends BaseModel
{

	protected $table = 'BRAND_DB';
	protected $primaryKey = 'BD_IDX';  //기본값 idx

	protected $fillable = [
        'BD_NAME',
        'BD_NAME_EN',
        'BD_NAME_GROUP',
        'BD_NAME_EN_GROUP',
        'BD_ACTIVE',
        'BD_LIST_ACTIVE',
        'BD_LOGO',
        'BD_DOMAIN',
        'BD_INTRODUCE',
        'BD_CODE',
        'BD_KIND_CODE',
        'bd_kind',
        'bd_showdang_active',
        'bd_cate_no',
        'bd_matching_cate',
        'bd_matching_brand',
        'bd_api_info',
        'bd_api_introduce',
        'bd_onadb_active',
        'bd_onadb_sort_num',
        'bd_memo',
        'BD_SORT',
        'brand_eval_json',
        'brand_eval_profit_score',
        'brand_eval_product_score',
        'brand_eval_risk_score',
        'brand_eval_ops_score',
        'brand_eval_growth_score',
        'brand_eval_total_score',
        'brand_grade_computed',
        'brand_grade_final',
        'brand_grade_forced',
        'brand_grade_forced_reason',
        'brand_grade_forced_by',
        'brand_grade_forced_at',
        'brand_eval_version',
        'brand_eval_by',
        'brand_eval_at',
    ];

}