<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductModel extends BaseModel
{

	protected $table = 'COMPARISON_DB';
	protected $primaryKey = 'CD_IDX';  //기본값 idx
	protected $fillable = [
		'sale_status',
		'purchase_type',
		'CD_KIND_CODE',
		'CD_CATEGORY_CODE',
		'CD_BRAND_IDX',
		'CD_BRAND2_IDX',
		'CD_NAME',
		'CD_NAME_OG',
		'CD_NAME_EN',
		'CD_CONT',
		'CD_MEMO',
		'cd_memo2',
		'cd_memo3',
		'cd_reference_links',
		'CD_SEARCH_TERM',
		'CD_RELEASE_DATE',
		'target_month',
		'img_mode',
		'CD_IMG',
		'CD_IMG2',
		'cd_add_img',
		'CD_SIZE',
		'CD_SIZE2',
		'cd_size_fn',
		'cd_weight_fn',
		'CD_CODE',
		'CD_CODE2',
		'CD_CODE3',
		'cd_reg_time',
		'cd_update_time',
		'cd_reg',
		'cd_national',
		'CD_INV_NAME1',
		'CD_INV_NAME2',
		'CD_INV_MATERIAL',
		'CD_COO',
		'cd_price_fn',
		'cd_price_history',
		'cd_fixed_price',
		'cd_sale_price',
		'CD_IMAGE_STORAGE_PATH',
		'cd_sale_price_changed_at',
		'cd_sale_price_change_meta',
		'cd_cost_price',
		'cd_cost_price_info',
		'cd_cost_price_memo',
		'cd_code_fn',
		'supplier_prd_idx',
		'cd_site_show',
		'cd_tier',
		'cd_godo_code',
		'cd_last_inspection_at',
		'cd_last_inspection_version',
		'cd_restock_alert_qty',
		'cd_restock_alert_collected_at',
		'CD_MACHING_CODE',
		'cd_hbti_data',
		'cd_hbti',
		'is_discontinued', // 단종
		'is_handling_stopped', // 취급중단
		'CD_WEIGHT',
		'CD_WEIGHT2',
		'CD_WEIGHT3',
		'CD_COLOR',
		'CD_HASH_TAG',
		'CD_SCORE',
		'CD_REVIEW',
		'CD_KEEP',
		'CD_SORT',
		'CD_BRAND_RANK',
		'CD_PD_INFO',
		'CD_RELATED_GOODS',
		'CD_RECOMMEND_GOODS',
		'CD_UPDATE_DATE',
		'CD_REG_DATE',
		'CD_HIT',
		'comment_count',
		'delivery_type',
		'cd_spec',
		'CD_DELETED_YN',
		'CD_DELETED_AT',
		'CD_DELETED_ADMIN_IDX',
		'CD_DELETED_ADMIN_NAME',
	];

	/**
	 * 상품 재고 목록
	 * @return \App\Core\HasOneRelation
	 */
    public function stocks()
    {
        return $this->hasOne(ProductStockModel::class, 'ps_prd_idx', 'CD_IDX');
    }

}