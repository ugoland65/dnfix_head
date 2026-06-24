<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductPartnerModel extends BaseModel
{

	protected $table = 'prd_partner';
	protected $primaryKey = 'idx';  //기본값 idx

	protected $fillable = [
		'name',
		'short_desc',
		'name_p',
		'name_ori',
		'status',
		'img_mode',
		'img_src',
		'sale_price',
		'order_price',
		'cost_price',
		'min_sale_price', // 최소판매가
		'price_data',
		'code',
		'partner_idx',
		'brand_idx',
		'is_match_excluded', // 매칭제외 여부
		'supplier_prd_idx',
		'supplier_site',
		'supplier_prd_pk',
		'supplier_2nd_name',
		'supplier_img_mode',
		'supplier_img_src',
		'supplier_status',
		'supplier_status_date',
		'supplier_is_option',
		'supplier_option_data',
		'supplier_detail_img',
		'godo_goodsNo',
		'godo_option',
		'godo_is_option',
		'godo_loaded_at',
		'kind',
		'hbti_type',
		'category_code',
		'matching_code',
		'matching_option',
		'matching_data',
		'cate_json',
		'godo_cate_json',
		'memo',
		'memo_work',
		'sold_out_date',
		'detail_crawler_date',
		'discount_target_yn', // 할인대상 여부 (Y/N)
		'last_sale_date', // 마지막 할인일
	];	

}