<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductModel extends BaseModel
{

	protected $table = 'COMPARISON_DB';
	protected $primaryKey = 'CD_IDX';  //기본값 idx

	/**
	 * 상품 재고 목록
	 * @return \App\Core\HasOneRelation
	 */
    public function stocks()
    {
        return $this->hasOne(ProductStockModel::class, 'ps_prd_idx', 'CD_IDX');
    }

}