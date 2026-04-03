<?php

namespace App\Models;

use App\Core\BaseModel;

class OrderSheetModel extends BaseModel
{

      protected $table = 'ona_order';
      protected $primaryKey = 'oo_idx';

      protected $fillable = [
            'oo_name', //주문서명
            'oo_po_name', //P/O CODE
            'oo_form_idx', //주문서폼 IDX
            'oo_import', // 수입형태
            'oo_sort', // 소트넘버 (미확인)
            'oo_state', // 주문서 상태 - 주문상태 1=작성중 / 2=주문전송 / 3=입금완료 / 4=입고완료 / 5=주문종료
            'oo_prd_currency', //상품 가격 화폐
            'oo_prd_exchange_rate', //상품 가격 환율
            'oo_fn_price', //확정 주문 금액
            'oo_sum_currency', //주문 결제 가격 화폐
            'oo_sum_exchange_rate', //주문 결제 가격 환율
            'oo_sum_price', //주문 결제 가격
            'oo_prd_to_pay_exchange_rate', // 환산 환율
            'oo_tex_data', //세금 정보
            'oo_upload_file', //주문 관련 파일
            'oo_express_data', //배송 정보
            'oo_approval_date', //결제기한 정보
            'oo_price_data',
            'oo_memo',
            'oo_date_data', //주문서 상태 변경 내역
            'oo_price_kr', //최종 합계 결제액
            'oo_in_date', //입고일
            'reg', //등록일시
            'created_by', //등록자 PK
            'created_name', //등록자 이름            
        ];
    

      /**
       *@deprecated
       *아직 작업중
       * @return array
       */
      public function get()
      {
            return $this->queryBuilder
                  ->table('v2_agency')
                  ->select(['idx', 'name'])
                  ->where('active', '=', 'Y')
                  ->where('mode', '=', 'land')
                  ->orderBy('name', 'ASC')
                  ->get();
      }

      /*
	//주문서 find
	public function find( $idx, $select=[] ) {

		$agency = $this->queryBuilder
            ->table('ona_order');

		//select
		if( !empty($select) ){
			$agency
				->select($select);
		}

		$results = $agency
            ->find($idx);

		return $results;

	}
*/
}
