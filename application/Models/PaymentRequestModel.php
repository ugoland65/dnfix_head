<?php
namespace App\Models;

use App\Core\BaseModel;

class PaymentRequestModel extends BaseModel
{

	protected $table = 'payment_request';
	//protected $primaryKey = 'idx';  //기본값 idx

    protected $fillable = [
        'kind',            // 발생 위치 (테이블명)
        'kind_idx',        // 참조 PK
    
        'category',        // 분류
        'currency',
        'amount',          // 금액
        'depositor_name',  // 예금주 이름
        'is_vat',          // 부가세 포함 여부
    
        'request_date',    // 결제 희망일
        'status',          // 상태
    
        'bank',            // 은행
        'bank_account',    // 계좌번호
        'depositor',       // 예금주
    
        'memo',            // 요청 메모

    
        'comment_count',   // 추가
        'meta_json',       // 추가
    
        'ad_pk',      // 등록자
        'ad_name',       // 등록자 이름

        'approved_ad_pk',     // 승인자
        'approved_ad_name',     // 승인자 
        'process_date',    // 처리일
        'process_memo',    // 처리 메모
    



    ];

}