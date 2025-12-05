<?
ini_set('display_errors', 1);
error_reporting(E_ALL);

$_idx = $_get1 ?? "";

if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from prd_grouping WHERE idx = '".$_idx."' "));
	
	// 배열 검증
	if (!is_array($data) || empty($data)) {
		$data = ['data' => '[]', 'pg_subject' => '', 'pg_mode' => '', 'pg_state' => '', 'pg_sday' => '', 'pg_day' => '', 'pg_memo' => ''];
	}
	
	$_prd_jsondata = json_decode($data['data'] ?? '[]', true);
	
	// 배열 검증
	if (!is_array($_prd_jsondata)) {
		$_prd_jsondata = [];
	}

	$_mode_text = [
		'sale' => "데이할인",
		'period' => "기간할인",
		'qty' => "수량체크",
		'event' => "기획전"
	];
}
/*
	echo "<pre>";
	print_r($_prd_jsondata);
	echo "</pre>";
*/
?>
<style type="text/css">
.pg-memo{ width:160px; height:80px; }
.pg-qty{ width:40px !important; height:28px !important; }
.pg-sale-price{ width:80px !important; height:28px !important; }
textarea { resize: none; }
.row-dis-sale-price{ color:#ff0000; }
.prd-memo{ color:#6b49ff; }
</style>
<div id="contents_head">
	<h1>상품 그룹핑 - <?=$data['pg_subject'] ?? ''?></h1>

	<div class="head-btn-wrap m-l-10">
		<button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdGroupingView.prdAdd('<?=$_idx?>')" >상품 추가/순서변경</button>
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="prdGroupingView.swindow('<?=$_idx?>')" >새창열기</button>
	</div>

	<div class="head-right-fixed-wrap">

		<!-- 
		<div>
			<ul class="filter-menu-title">정보변경</ul>
		</div>
		-->
		<div class="m-t-7">
			<ul class="m-t-5">
				그룹핑 모드 : <b><?=$_mode_text[$data['pg_mode'] ?? ''] ?? ''?></b>
			</ul>
			<ul class="m-t-5">
				그룹핑 상품 : <b><?=count($_prd_jsondata)?></b> 개
			</ul>
			<ul class="m-t-5">
				<input type='text' name='pg_subject' id='pg_subject' size='20' value="<?=$data['pg_subject'] ?? ''?>" placeholder="그룹핑 제목" >
			</ul>
			<ul class="m-t-5">
				진행상태 : 
				<select name="pg_state" id="pg_state" >
					<option value="진행" <? if( ($data['pg_state'] ?? "") == "진행" ) echo "selected"; ?> >진행</option>
					<option value="마감" <? if( ($data['pg_state'] ?? "") == "마감" ) echo "selected"; ?> >마감</option>
					<option value="취소" <? if( ($data['pg_state'] ?? "") == "취소" ) echo "selected"; ?> >취소</option>
				</select>
			</ul>
			<ul class="m-t-5">
				진행일

				<? if( ($data['pg_mode'] ?? "") == "period" ){ ?>
				<div class="calendar-input" style="display:inline-block; width:105px;" id="pg_sday_wrap"><input type="text" name="pg_sday"  id="pg_sday" value="<?=$data['pg_sday'] ?? ''?>" style="width:90px;" placeholder="시작일" autocomplete="off" > ~ </div>
				<? } ?>

				<div class="calendar-input" style="display:inline-block;"><input type="text" name="pg_day"  id="pg_day" value="<?=$data['pg_day'] ?? ''?>" style="width:90px;"  autocomplete="off" ></div>
			</ul>
			<ul class="m-t-10">
				메모
				<textarea name="pg_memo" id="pg_memo"><?=$data['pg_memo'] ?? ''?></textarea>
			</ul>
			<ul class="m-t-10">

				<? if( ($data['pg_mode'] ?? "") == "sale" || ($data['pg_mode'] ?? "") == "period" ){ ?>
					<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-lg " onclick="prdGroupingView.save('ing');" > 
						임시저장
					</button>
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg " onclick="prdGroupingView.save('end');" > 
						확정/마감
					</button>
				<? }else{ ?>
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg width-full" onclick="prdGroupingView.save('ing');" > 
						저장
					</button>
				<? } ?>

			</ul>
		</div>

	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap" class="have-head-right-fixed">

		<div id="list_wrap">

			<form id="form1">
			<input type="hidden" name="a_mode" value="prdGrouping_modify" >
			<input type="hidden" name="idx" value="<?=$_idx?>" >

			<table class="table-style">	
				<tr class="list">
					<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
					<th class="">순번</th>
					<th class="list-idx">고유번호</th>
					<th class="">이미지</th>
					<th class="">분류</th>
					<th class="">이름</th>
					<th>판매가<br>원가</th>
				<th class="">재고</th>
				<? if( ($data['pg_mode'] ?? "") == "qty" ){ ?>
				<th>수량</th>
				<? }elseif( ($data['pg_mode'] ?? "") == "event" ||  ($data['pg_mode'] ?? "") == "sale" || ($data['pg_mode'] ?? "") == "period" ){ ?>
				
				<? if( ($data['pg_state'] ?? "") != "마감" ){ ?>
				<th>할인률 예</th>
				<? } ?>

				<th>할인률</th>
				<? } ?>

				<th>메모</th>

				<? if( (($data['pg_mode'] ?? "") == "event" ||  ($data['pg_mode'] ?? "") == "sale" || ($data['pg_mode'] ?? "") == "period" ) && ($data['pg_state'] ?? "") == "마감" ){ ?>
				<th style="width:120px">실적</th>
				<? } ?>

				</tr>
			<?

		for ($z=0; $z<count($_prd_jsondata); $z++){
			
			// 배열 검증
			if (!is_array($_prd_jsondata[$z])) {
				continue;
			}

			$_prd_idx = $_prd_jsondata[$z]['idx'] ?? "";
			$_ps_idx = $_prd_jsondata[$z]['stockidx'] ?? "";
			$_pname = $_prd_jsondata[$z]['pname'] ?? "";
			
			$img_path = "";
			$_name = "";
			$_jancode = "";
			$prd_data = [];
			$ps_sale_log_data = [];

			if( $_prd_idx == "Instant" ){
			}else{

				$_colum = "A.CD_IDX, A.CD_IMG, A.CD_CODE2, A.CD_CODE3, A.CD_NAME, A.CD_KIND_CODE, A.cd_code_fn, A.cd_sale_price, A.cd_cost_price, A.CD_MEMO";
				$_colum .= ", B.ps_idx, B.ps_stock, B.ps_sale_log, B.ps_sale_date, B.ps_in_sale_s, B.ps_in_sale_e, B.ps_in_sale_data";
				$_colum .= ", C.BD_NAME";

				$_query = "select ".$_colum." from "._DB_COMPARISON." A
					left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX ) 
					left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND_IDX  ) 
					where CD_IDX = '".$_prd_idx."' ";

				$prd_data = sql_fetch_array(sql_query_error($_query));
				
				// 배열 검증
				if (!is_array($prd_data) || empty($prd_data)) {
					$prd_data = [
						'CD_IDX' => '', 'CD_IMG' => '', 'CD_NAME' => '', 'CD_KIND_CODE' => '', 'cd_code_fn' => '{}',
						'cd_sale_price' => 0, 'cd_cost_price' => 0, 'CD_MEMO' => '', 'BD_NAME' => '',
						'ps_idx' => '', 'ps_stock' => 0, 'ps_sale_log' => '[]', 'ps_sale_date' => 0,
						'ps_in_sale_s' => '', 'ps_in_sale_e' => '', 'ps_in_sale_data' => ''
					];
				}

				if( !empty($prd_data['CD_IMG']) ){
					$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
				}

				$_name = $prd_data['CD_NAME'] ?? "";

				$_cd_code_data = json_decode($prd_data['cd_code_fn'] ?? '{}', true);
				
				// 배열 검증
				if (!is_array($_cd_code_data)) {
					$_cd_code_data = ['jan' => ''];
				}

				$_jancode = $_cd_code_data['jan'] ?? "";

				$ps_sale_log_data = json_decode($prd_data['ps_sale_log'] ?? '[]', true);
				
				// 배열 검증
				if (!is_array($ps_sale_log_data)) {
					$ps_sale_log_data = [];
				}
			}

			?>
			<tr align="center" id="trid_<?=$_prd_idx?>">
				<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$_prd_idx?>" ></td>	
				<td class=""><b style="font-size:14px"><?=($z+1)?></b></td>
				<td class="list-idx">
					<div>
						<ul><span style="font-size:12px;"><?=$_prd_idx?></span></ul>
						
						<? if( $_prd_idx != "Instant" ){ ?>
						<ul>( <b style='color:#0093e9; font-size:14px;'><?=$_ps_idx?></b> )</ul>
						<? } ?>

					</div>
				</td>
				<td >
					<? if( !empty($prd_data['CD_IMG']) ){ ?>
						<img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;">
					<? }else{ ?>
						<div class="no-image">No image</div>
					<? } ?>
				</td>
				<td class="">
					<?=$koedge_prd_kind_name[$prd_data['CD_KIND_CODE'] ?? ''] ?? ''?>
				</td>
				<td class="text-left" style="max-width:300px;">
					
					<? if( $_prd_idx == "Instant" ){ ?>
						<b><?=$_pname?></b>
					<? }else{ ?>
					<div>
						<ul style="font-size:11px;"><?=$_jancode?></ul>
						<ul class="m-t-3" style="font-size:11px;"><?=$prd_data['BD_NAME']?></ul>
						<ul class="m-t-3"><span onclick="onlyAD.prdView('<?=$prd_data['CD_IDX'] ?? ''?>','info');" style="cursor:pointer;" ><?=$_name?></span></ul>
						
						<? if( !empty($prd_data['CD_MEMO']) ){ ?><ul class="m-t-3"><span class="prd-memo"><i class="fas fa-feather-alt"></i> <?=$prd_data['CD_MEMO']?></span></ul><? } ?>
						<ul><?=in_sale_icon($prd_data['ps_in_sale_s'] ?? '', $prd_data['ps_in_sale_e'] ?? '', $prd_data['ps_in_sale_data'] ?? '')?></ul>

						<? if( ($prd_data['ps_sale_date'] ?? 0) > 0 ){ ?>
						<ul class="m-t-5" style="font-size:11px; color:#ff0000;">지난 할인<br><b><?=$prd_data['ps_sale_date']?></b> | 할인수 : <b><?=count($ps_sale_log_data)?></b> | <?=$ps_sale_log_data[0]['pg_subject'] ?? ''?> | <b><?=$ps_sale_log_data[0]['sale_per'] ?? 0?></b>%</ul>
						<? } ?>

<?
/*
	echo "<pre>";
	print_r($ps_sale_log_data);
	echo "</pre>";
*/
?>
					</div>
					<? } ?>
				</td>
			<td class="text-right">
				<?
					$_margin = 0;
					$_margin_pre = 0;
					$_cd_sale_price = (int)($prd_data['cd_sale_price'] ?? 0);
					$_cd_cost_price = (int)($prd_data['cd_cost_price'] ?? 0);
					
					if( $_cd_sale_price > 0 && $_cd_cost_price > 0 ){
						$_margin = $_cd_sale_price - $_cd_cost_price;
						$_margin_pre = round(($_cd_sale_price - $_cd_cost_price) / $_cd_sale_price * 100, 2);
					}
				?>
				<div class="p-l-10 p-r-10">
					<ul>판매 : <span id="row_sale_price_<?=$z?>" data-saleprice="<?=$_cd_sale_price?>" ><?=number_format($_cd_sale_price)?></span></ul>
					<ul class="m-t-4">원가 : <span id="row_cost_price_<?=$z?>" data-costprice="<?=$_cd_cost_price?>" ><?=number_format($_cd_cost_price)?></span></ul>
					<? if( $_cd_sale_price > 0 && $_cd_cost_price > 0 ){ ?>
					<ul class="m-t-4">마진 : <?=number_format($_margin)?><br>( <b><?=$_margin_pre?></b> %) </ul>
					<? } ?>
				</div>

			</td>
			<td class="">
				<? if( ($prd_data['ps_stock'] ?? 0) == 0 ){ ?>
					<span style="color:#ff0000;">재고<br>없음</span>
				<? }else{ ?>
					<b style="font-size:15px; color:#5e41ff;"><?=number_format($prd_data['ps_stock'] ?? 0)?></b>
				<? } ?>
			</td>
			<? 
			// 수량체크 일경우
			if( ($data['pg_mode'] ?? "") == "qty" ){
				// mode_data 배열 검증
				$_mode_data_qty = "";
				if (isset($_prd_jsondata[$z]['mode_data']) && is_array($_prd_jsondata[$z]['mode_data'])) {
					$_mode_data_qty = $_prd_jsondata[$z]['mode_data']['qty'] ?? "";
				}
			?>
			<td>
				<input type='text' name="pg_prd_qty[]" id='' size='20' value="<?=$_mode_data_qty?>" placeholder="" class="pg-qty">
			</td>
			<? 
			// 기획전,할인일경우
			}elseif( ($data['pg_mode'] ?? "") == "event" ||  ($data['pg_mode'] ?? "") == "sale" || ($data['pg_mode'] ?? "") == "period" ){ 
				
				// mode_data 배열 검증
				$_mode_data = [];
				if (isset($_prd_jsondata[$z]['mode_data']) && is_array($_prd_jsondata[$z]['mode_data'])) {
					$_mode_data = $_prd_jsondata[$z]['mode_data'];
				}
				$_mode_data_per = $_mode_data['per'] ?? 0;
				$_mode_data_sale_price = $_mode_data['sale_price'] ?? 0;
				$_mode_data_margin_price = $_mode_data['margin_price'] ?? 0;
				$_mode_data_margin_per = $_mode_data['margin_per'] ?? 0;
			?>
			
			<? if( ($data['pg_state'] ?? "") != "마감" ){ ?>
			<td class="text-left">
				<div>
						
					<?
					$_margin_ex_per = array(10,15,20,25,30);

					for ($i=0; $i<count($_margin_ex_per); $i++){
						$dis_sale_price = ($_cd_sale_price*((100-$_margin_ex_per[$i]) / 100)); 
						$dis_margin = $dis_sale_price - $_cd_cost_price;
						$dis_margin_per = ($dis_sale_price > 0) ? round(($dis_sale_price - $_cd_cost_price) / $dis_sale_price * 100, 2) : 0;
					?>

						<ul class="p-2" style="font-size:12px;">
							<label onclick="prdGroupingView.exChoise( '<?=$z?>',<?=$_margin_ex_per[$i]?> );">
								<input type="radio" name="row_ex_<?=$z?>" <? if( $_mode_data_per == $_margin_ex_per[$i] ) echo "checked"; ?>>
								<b><?=$_margin_ex_per[$i]?></b>% | <?=number_format($dis_sale_price)?> | <b><?=number_format($dis_margin)?></b> |
								<?=$dis_margin_per?>%
							</label>
						</ul>

					<? } ?>

				</div>
			</td>
			<? } ?>

			<td class="text-left">
				
				<div class="p-l-10 p-r-10">
					
					<ul>
						할인률 : 
						<input type='text' name="pg_prd_per[]" id='pg_prd_per_<?=$z?>' value="<?=$_mode_data_per?>" autocomplete="off" class="pg-qty" onkeyUP="prdGroupingView.marginAutoCalculation( '<?=$z?>', this.value );">
					</ul>

					<ul class="m-t-4">
						판매가 : 
						<? /* 
						<input type='text' name="pg_prd_per[]" id='row_dis_sale_price_<?=$z?>' value="<?=number_format($_mode_data_sale_price)?>" autocomplete="off" class="pg-sale-price" onkeyUP="prdGroupingView.marginAutoCalculation( '<?=$z?>', this.value );">
						*/ ?>
						<span id="row_dis_sale_price_<?=$z?>" class="row-dis-sale-price"><?=number_format($_mode_data_sale_price)?></span>

					</ul>

					<ul class="m-t-4">마진금 : <span id="row_dis_margin_price_<?=$z?>"><?=number_format($_mode_data_margin_price)?></span></ul>
					<ul class="m-t-4">마진율 : <span id="row_dis_margin_per_<?=$z?>"><?=$_mode_data_margin_per?>%</span></ul>
				<div>

				<input type="hidden" name="original_sale_price[]" id="row_original_sale_price_input_<?=$z?>" value="<?=$_cd_sale_price?>" >
				<input type="hidden" name="dis_sale_price[]" id="row_dis_sale_price_input_<?=$z?>" value="<?=$_mode_data_sale_price?>" >
				<input type="hidden" name="dis_margin_price[]" id="row_dis_margin_price_input_<?=$z?>"value="<?=$_mode_data_margin_price?>" >
				<input type="hidden" name="dis_margin_per[]" id="row_dis_margin_per_input_<?=$z?>"value="<?=$_mode_data_margin_per?>" >
				
			</td>
				<? }?>

			<td>
				<textarea name="pg_prd_memo[]" class="pg-memo"><?=$_prd_jsondata[$z]['memo'] ?? ''?></textarea>
			</td>

			<? 
				if( (($data['pg_mode'] ?? "") == "event" ||  ($data['pg_mode'] ?? "") == "sale" || ($data['pg_mode'] ?? "") == "period" ) && ($data['pg_state'] ?? "") == "마감" ){

					$_psu_day = "";
					$showings = ['qty_sum' => 0];

					if( ($data['pg_mode'] ?? "") == "period" ){
						
						$_pg_day1 = date("Y-m-d",strtotime(($data['pg_sday'] ?? '0000-00-00')." +1 days"));
						$_pg_day2 = date("Y-m-d",strtotime(($data['pg_day'] ?? '0000-00-00')." +1 days"));

						$_psu_day = "<span style='font-size:11px;'>".$_pg_day1." ~<br>".$_pg_day2."</span>";
						
						if (!empty($_ps_idx)) {
							$showings = sql_fetch_array(sql_query_error("select 
							SUM(psu_qry) as qty_sum
							from prd_stock_unit WHERE psu_stock_idx = '".$_ps_idx."' AND psu_day >= '".$_pg_day1."' AND psu_day <= '".$_pg_day2."' AND psu_mode = 'minus' AND INSTR(psu_kind, '판매')  "));
							
							// 배열 검증
							if (!is_array($showings) || empty($showings)) {
								$showings = ['qty_sum' => 0];
							}
						}

					}elseif( ($data['pg_mode'] ?? "") == "sale" ){

						$_psu_day = date("Y-m-d",strtotime(($data['pg_day'] ?? '0000-00-00')." +1 days"));
						
						if (!empty($_ps_idx)) {
							$showings = sql_fetch_array(sql_query_error("select 
							SUM(psu_qry) as qty_sum
							from prd_stock_unit WHERE psu_stock_idx = '".$_ps_idx."' AND psu_day = '".$_psu_day."' AND psu_mode = 'minus' AND INSTR(psu_kind, '판매')  "));
							
							// 배열 검증
							if (!is_array($showings) || empty($showings)) {
								$showings = ['qty_sum' => 0];
							}
						}

					}

			?>
			<td>
				<div>
					<ul><?=$_psu_day?></ul>
					<?
						if( ($showings['qty_sum'] ?? 0) > 0 ){
					?>
						<ul class="m-t-5">판매 : <b style="color:#ff0000"><?=$showings['qty_sum']?></b>건</ul>
						<ul class="m-t-5">판매가 : <span style="color:#ff0000"><?=number_format($_mode_data_sale_price * $showings['qty_sum'])?></span></ul>
						<ul class="m-t-5">수익 : <span style="color:#ff0000"><?=number_format($_mode_data_margin_price * $showings['qty_sum'])?></span></ul>
					<? }else{ ?>
						<ul class="m-t-5">판매없음</ul>
					<? } ?>
				</div>
			</td>
			<? }?>

			<tr>
			<? }?>

			</table>
			</form>
		</div>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/ad/prd/prd_grouping'" > 
		<i class="fas fa-arrow-left"></i> 목록
	</button>
</div>

<script type="text/javascript">
<!-- 
var prdGroupingView = function() {

	var prdWindow;

	return {

		init : function() {

		},

		//상품 추가/순서변경
		prdAdd : function( idx ) {
		
			var width = "1400px";

			prdWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "상품 추가/순서변경",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/prd_grouping_prd',
						data: { "idx":idx },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
							
						}
					},
				}
			});
		
		},

		// 할인율 퍼센트로 자동계산
		marginAutoCalculation : function( forNum, per ) {
		
			var _dis_sale_price = Math.round($("#row_sale_price_" + forNum).data("saleprice")*((100-per) / 100));
			var _dis_margin_price = _dis_sale_price - $("#row_cost_price_" + forNum).data("costprice");
			//var _dis_margin_per = Math.round((_dis_sale_price - $("#row_cost_price_" + forNum).data("costprice")) / _dis_sale_price * 100, 2);
			var _dis_margin_per = ((_dis_sale_price - $("#row_cost_price_" + forNum).data("costprice")) / _dis_sale_price * 100).toFixed(2);

			$("#row_dis_sale_price_" + forNum).html(GC.comma(_dis_sale_price));
			//$("#row_dis_sale_price_" + forNum).val(GC.comma(_dis_sale_price));
			$("#row_dis_margin_price_" + forNum).html(GC.comma(_dis_margin_price));
			$("#row_dis_margin_per_" + forNum).html(_dis_margin_per + "%");

			$("#row_dis_sale_price_input_" + forNum).val(_dis_sale_price);
			$("#row_dis_margin_price_input_" + forNum).val(_dis_margin_price);
			$("#row_dis_margin_per_input_" + forNum).val(_dis_margin_per);

		},

		// 저장
		exChoise : function( forNum, per ) {
			$("#pg_prd_per_" + forNum).val(per);
			prdGroupingView.marginAutoCalculation( forNum, per );
		},

		// 저장
		save : function( save_mode ) {

			var _pg_subject = $("#pg_subject").val();
			var _pg_state = $("#pg_state").val();
			var _pg_day = $("#pg_day").val();
			var _pg_memo = $("#pg_memo").val();

			var formData = $("#form1").serializeArray();
			formData.push({ name: "pg_subject", value: _pg_subject });
			formData.push({ name: "pg_state", value: _pg_state });

			<? if( ($data['pg_mode'] ?? "") == "period" ){ ?>
			var _pg_sday = $("#pg_sday").val();
			formData.push({ name: "pg_sday", value: _pg_sday });
			<? } ?>

			formData.push({ name: "pg_day", value: _pg_day });


			formData.push({ name: "pg_memo", value: _pg_memo });
			formData.push({ name: "save_mode", value: save_mode });


			if( save_mode == "end" ){

				if( _pg_day == "0000-00-00" ){

					showAlert("Error", "진행일을 선택해주세요.", "dialog" );
					return false;

				}

				$.confirm({
					icon: 'fas fa-exclamation-triangle',
					title: '확정/마감을 진행합니다.',
					content: '확정/마감을 진행하면 현재 설정한 진행일('+ _pg_day +')로<br>상품의 지난할인 데이터로 저장됩니다.<br>수정이 불가능하니 반드시 확인 후 진행해 주세요.',
					type: 'red',
					typeAnimated: true,
					closeIcon: true,
					buttons: {
						somethingElse: {
							text: '확정/마감 진행하기',
							btnClass: 'btn-red',
							action: function(){ 
								
								$.ajax({
									url: "/ad/processing/prd_grouping",
									data: formData,
									type: "POST",
									dataType: "json",
									success: function(res){
										if ( res.success == true ){
											alert("저장 완료!");
											location.reload();
										}else{
											showAlert("Error", res.msg, "alert2" );
											return false;
										}
									},
									error: function(request, status, error){
										console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
										showAlert("Error", "에러", "alert2" );
										return false;
									},
									complete: function() {
										//$(obj).attr('disabled', false);
									} 
								});

							}
						},
						cencle: {
							text: '취소',
							action: function(){
							}
						}
					}
				});

			}else{

				$.ajax({
					url: "/ad/processing/prd_grouping",
					data: formData,
					type: "POST",
					dataType: "json",
					success: function(res){
						if ( res.success == true ){
							alert("저장 완료!");
							location.reload();
						}else{
							showAlert("Error", res.msg, "alert2" );
							return false;
						}
					},
					error: function(request, status, error){
						console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
						showAlert("Error", "에러", "alert2" );
						return false;
					},
					complete: function() {
						//$(obj).attr('disabled', false);
					}
				});

			}
		},

		swindow : function( idx ) {
			window.open("/ad/ajax/prd_grouping_view_list/"+ idx, "excelDown", "width=1000,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
		},

	};

}();


//--> 
</script> 