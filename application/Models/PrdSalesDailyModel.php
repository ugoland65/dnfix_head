<?php
namespace App\Models;

use App\Core\BaseModel;

class PrdSalesDailyModel extends BaseModel
{

	protected $table = 'prd_sales_daily';
	//protected $primaryKey = 'idx';  //기본값 idx

    protected $fillable = [
        'sale_day',
        'prd_type',
        'prd_ref_idx',
        'partner_idx',
        'sold_qty',
    ];

    /**
     * (선택) 관계: 공급사
     * partners 테이블/모델명이 Partner 라는 전제
     */
    public function partner()
    {
        return $this->belongsTo(PartnersModel::class, 'partner_idx', 'idx');
    }

    /**
     * (선택) 타입별 원본 상품 관계
     * - stock: prd_stock
     * - partner: prd_partner
     * 모델명/PK는 네 프로젝트 네이밍에 맞게 바꾸면 됨
     */
    public function stockItem()
    {
        return $this->belongsTo(ProductScoreModel::class, 'prd_ref_idx', 'ps_idx');
    }

    public function partnerItem()
    {
        return $this->belongsTo(ProductPartnerModel::class, 'prd_ref_idx', 'idx');
    }

}