<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductPartnerModel extends BaseModel
{

	protected $table = 'prd_partner';
	protected $primaryKey = 'idx';  //기본값 idx

	protected $fillable = [
		'name',
		'name_p',
		'name_ori',
		'status',
		'img_mode',
		'img_src',
		'sale_price',
		'order_price',
		'cost_price',
		'price_data',
		'code',
		'partner_idx',
		'brand_idx',
		'supplier_prd_idx',
		'supplier_site',
		'supplier_prd_pk',
		'supplier_2nd_name',
		'supplier_img_mode',
		'supplier_img_src',
		'godo_goodsNo',
		'godo_option',
		'godo_is_option',
		'kind',
		'hbti_type',
		'matching_code',
		'matching_option',
		'matching_data',
		'memo',
		'sold_out_date',
	];	

}