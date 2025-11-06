<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\payment;

$payment = new payment();

$result = $payment->paymentIndex();

/*
	echo "<pre>";
	print_r($result);
	echo "</pre>";
*/
?>
<div id="contents_head">
    <h1>결제/입금 관리</h1>

    <div id="head_write_btn">
        <button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="payment.newCreate(this)" >
            <i class="fas fa-plus-circle"></i>
            신규 결제 요청
        </button>
    </div>

    <div class="head-search-wrap new">
        <ul>

            <div>
                <ul>
					<? /* ?>
                    <select name='search_category' id='search_category'  >
                        <option value="">Category</option>
                        <option value='HM' <? if( $search_category == 'HM')  echo "selected"; ?>>HM</option>
                        <option value='FA' <? if( $search_category == 'FA')  echo "selected"; ?>>FA</option>
                        <option value='GP' <? if( $search_category == 'GP')  echo "selected"; ?>>GP</option>
                        <option value='RO' <? if( $search_category == 'RO')  echo "selected"; ?>>RO</option>
                        <option value='GF' <? if( $search_category == 'GF')  echo "selected"; ?>>GF</option>
                        <option value='PKG' <? if( $search_category == 'PKG')  echo "selected"; ?>>PKG</option>
                        <option value='ICT' <? if( $search_category == 'ICT')  echo "selected"; ?>>ICT</option>
                        <option value='INS' <? if( $search_category == 'INS')  echo "selected"; ?>>INS</option>
                    </select>


                    <select name='search_date_kind' id='search_date_kind' class="m-l-15" >
                        <option value='In_Flight' <? if( $search_date_kind == 'In_Flight')  echo "selected"; ?>>IN Flight</option>
                        <option value='Change_Day' <? if( $search_date_kind == 'Change_Day')  echo "selected"; ?>>Change Day</option>
                        <option value='Booking_Day' <? if( $search_date_kind == 'Booking_Day')  echo "selected"; ?>>Booking Day</option>
                    </select>

                    <input type="text" id="s_day" name="s_day" value="<?=$_s_day ?? date('Y-m-d') ?>" style="width:100px; cursor:pointer;" class="text-center " placeholder="Start Day" readonly /> ~
                    <input type="text" id="e_day" name="e_day" value="<?=$_e_day ?? '' ?>" style="width:100px; cursor:pointer;" class="text-center" placeholder="End Day" readonly />

                    <p class="m-t-7">

                        <select name="search_kind" id="search_kind" >
                            <option value=''>Search type</option>
                            <option value='guest' <? if( $search_kind == 'guest')  echo "selected"; ?>>Guest</option>
                            <option value='hotel' <? if( $search_kind == 'hotel')  echo "selected"; ?>>Hotel</option>
                            <option value='agency' <? if( $search_kind == 'agency')  echo "selected"; ?>>Agency/Agenet</option>
                            <option value='manager' <? if( $search_kind == 'manager')  echo "selected"; ?>>OP name</option>
                            <!-- <option value='writer_id' <? if( $search_kind == 'writer_id')  echo "selected"; ?>>Writer ID</option> -->
                            <option value='team_number' <? if( $search_kind == 'team_number')  echo "selected"; ?>>Team Number</option>
                        </select>

                        <input type='text' name='search_text' id='search_text' value="<?=$_search_text ?? '' ?>"  placeholder="검색어" >

                    </p>
					<? */ ?>

                </ul>
                <ul>
                    <button type="button" class="btn btnstyle1 btnstyle1-info btnstyle1-sm" id="search_btn"><i class="fas fa-search"></i> Search</button>
                    <button type="button" class="btnstyle1 btnstyle1-sm" id="search_reset"><i class="far fa-trash-alt"></i> Clean</button>
                </ul>
            </div>

        </ul>
        <ul>
<!-- 
            <div>
                <ul>
                    <button type="button" id="exce" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="a.excelDownload(this)"><i class="fas fa-file-excel"></i> Download</button>
                </ul>
                <ul class="m-t-8">
                    <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="" >선택 그룹추가</button>
                    <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="bookingList.newGroup()" >선택 신규그룹</button>
                </ul>
            </div>
 -->
        </ul>
    </div>

</div>
<div id="contents_body">
    <div id="contents_body_wrap" class="head-search-on">
        <div id="list_new_wrap">

            <div class="list-top">
                <span class="count">Total : <b><?=$result['total']?></b></span>
            </div>

            <div id="" class="table-wrap5">
                <div class=" scroll-wrap">

                    <table class="table-st1">
                        <thead>
                        <tr>
                            <th class="list-checkbox"><input type="checkbox" name="" class="check_box_all" ></th>
                            <th class="list-idx">Idx</th>
                            <th class="">Detail</th>
                            <th class="">Status</th>
                            <th class="">Kind</th>
                            <th class="">Mode</th>
                            <th class="">Price</th>
                            <th class="">Due date</th>
                            <th class="">Bank</th>
                            <th class="">Memo</th>
                            <th class="">Date</th>
                            <th class="">처리</th>
                            <th class="">연관</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?

							$_payment_kind['plus'] = "입금";
							$_payment_kind['minus'] = "출금";

							$_payment_step[0] = "대기";
							$_payment_step[1] = "확인";
							$_payment_step[2] = "보류";
							$_payment_step[3] = "완료";

							$_payment_text['orderSheet'] = "주문서";

							foreach ( $result['payment'] as $list ){

                        ?>

                            <tr  id="trid_<?=$list['idx']?>"  >
                                <td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list['idx']?>" class="checkSelect" ></td>
                                <td class="list-idx"><?=$list['idx']?></td>
                                <td class="text-center">
                                    <button type="button" id="" class="btnstyle1 <?=$_button_class?> btnstyle1-sm" onclick="adBooking.teamViewNew('<?=$list['idx']?>')" >Detail</button>
                                </td>

                                <td class="text-center"><?=$_payment_step[$list['state']]?></td>
                                <td class="text-center"><?=$_payment_kind[$list['kind']]?></td>
                                <td class="text-center"><?=$_payment_text[$list['mode']] ?? ""?><?=$list['mode_text'] ?? ""?></td>
                                <td class="text-right">
									<b><?=number_format($list['price'])?></b>
                                </td>
                                <td class="text-center"><?=$list['desired_date']?></td>
                                <td class="text-left"><?=$list['bank']?></td>
                                <td class="text-left"><?=$list['memo']?></td>
                                <td class="text-center"><?=$list['reg_date']?> (<?=$list['ad_name']?>)</td>
                                <td class="text-left">
									<? if( $list['state'] == 0 ){ ?>
										<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="payment.statusModify('<?=$list['idx']?>')" >처리</button>
									<? }elseif( $list['state'] == 3 ){ ?>
										<?=$list['reg']['step4']['date']?> (<?=$list['reg']['step4']['name'] ?? ''?>)
									<? }else{ ?>
									<? } ?>
								</td>
								<td>
								</td>
                            </tr>

                        <? } ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <div id="contents_body_bottom_padding"></div>
    </div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap"><?=$result['paga_nation']?></div>
</div>

<script type="text/javascript">
<!--
	const API_ENDPOINTS = {
		procStatusModify: "/ad/proc/Admin/payment/paymentStatusModify",
	};
 
	const payment = (function() {
 
		return {
 
			// 초기화
			init() {
				console.log('baseCode module initialized.');
			},
 
			newCreate() {
				var width = "1000px";
				openDialog("/ad/ajax/payment_reg",{ "mode":"payment"  },"결제요청",width); 
			},

			statusModify(idx){
			
				ajaxRequest(API_ENDPOINTS.procStatusModify, {
					idx
				})
				.done(res => {
					if (res.status === "success") {
						alert("수정 되었습니다.");
						location.reload();
					} else {
						dnAlert('Error', res.message || '상태 변경 실패', 'red'); // 실패 시 메시지 표시
					}
				})
				.catch(error => {
					dnAlert('Error', '상태 변경 실패', 'red');
					throw new Error('AJAX 요청 실패');
				});
			
			}
		
		}	
 
	})();
 
    $(function(){

        $('#search_btn').click(function(){

            // URL 생성
            const params = new URLSearchParams();
            const fields = ['search_category', 'search_area', 'search_date_kind', 's_day', 'e_day', 'search_kind', 'search_text'];

            // 각 필드 값을 URL 파라미터에 추가
            fields.forEach(field => {
                const value = document.getElementById(field).value;
                if (value) {
                    params.append(field, value);
                }
            });

            // 현재 페이지에 파라미터 추가 후 리다이렉트
            const baseUrl = window.location.pathname;
            window.location.href = `${baseUrl}?${params.toString()}`;

        });

        $('#search_reset').click(function(){
            /*
            $("#search_value").val("");
            $("#s_day").val("");
            $("#e_day").val("");
            bookingList.list();
            */
            window.location.href = '/ad/v2_booking/booking?';
        });

    });


    //-->
</script>