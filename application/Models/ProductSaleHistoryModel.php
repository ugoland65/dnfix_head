<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductSaleHistoryModel extends BaseModel
{

	protected $table = 'prd_sale_history';
	protected $primaryKey = 'seq';  //기본값 idx

    protected $fillable = [
        'sale_status', // 할인 상태 할인상태 (wait:대기, start:시작, end:종료, upload:업로드완료)
        'sale_mode', // 할인모드 (day, week, month 등)
        'sale_start_date', // 할인 시작일
        'sale_end_date', // 할인 종료일
        'product_json', // 상품 JSON
        'meta_json', // 검색/추출 조건 메타 JSON
        'created_by', // 생성자 정보
        'temp_saved_yn', // 임시저장 여부 (Y/N)
        'temp_saved_at', // 임시저장 시각
        'uploaded_at', // 고도몰 등록 완료 시간
        'uploaded_by', // 고도몰 등록 처리자
    ];

}