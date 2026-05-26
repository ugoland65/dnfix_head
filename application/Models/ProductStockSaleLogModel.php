<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductStockSaleLogModel extends BaseModel 
{

    protected $table = 'prd_stock_sale_log';
    protected $primaryKey = 'seq';  //기본값 idx

    /**
     * 할인 로그 이력
     */
    protected $fillable = [

        // 상품 정보
        'ps_idx',              // 상품 재고 PK
        'prd_mode',            // 상품 구분 (provider: 공급사상품, prdDB: 자사보유상품)

        // 할인 정보
        'sale_mode',           // 할인 모드 (day, period 등)
        'grouping_idx',        // 할인 그룹 PK

        'pg_subject',          // 할인명
        'pg_sday',             // 할인 시작일
        'pg_day',              // 할인 종료일 또는 할인일

        'sale_per',            // 할인율

        // 가격 정보
        'original_price',      // 기존 판매가
        'sale_price',          // 할인 판매가

        'margin_price',        // 마진 금액
        'margin_per',          // 마진율

        // 등록 정보
        'reg_date',            // 등록일시

        'reg_id',              // 등록자 아이디
        'reg_name',            // 등록자 이름

        'reg_ip',              // 등록 IP
        'reg_domain',          // 등록 도메인

        // 기타
        'raw_json',            // 원본 JSON 데이터

        'created_at',          // 생성일시

    ];

}

