<?php
	
	// 변수 초기화
	$_idx = $_GET['idx'] ?? $_POST['idx'] ?? "";
	$_sort = $_GET['sort'] ?? $_POST['sort'] ?? "qty";
	
	$data = sql_fetch_array(sql_query_error("select * from prd_stock_history where uid = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = [];
	}

	$_json_data = json_decode($data['data'] ?? '[]', true);
	$_error_data = json_decode($data['error'] ?? '{"result":[]}', true);

	// JSON 디코딩 결과 검증
	if (!is_array($_json_data)) {
		$_json_data = [];
	}
	if (!is_array($_error_data)) {
		$_error_data = ['result' => []];
	}
	if (!isset($_error_data['result']) || !is_array($_error_data['result'])) {
		$_error_data['result'] = [];
	}

	if( $_sort == "brand" ){
		$_json_data = arr_sort( $_json_data,'brand_idx', 'desc' );
	}else{
		$_json_data = arr_sort( $_json_data,'qty', 'desc' );
	}


	// 배열 초기화
	$_row1 = [];
	$_row2 = [];

	foreach ( $_json_data as $key => $val ){

		// 배열 요소 검증
		if (!is_array($val)) {
			continue;
		}

		$_ps_idx = $val['ps_idx'] ?? '';

		$prd_data = sql_fetch_array(sql_query_error("select 
			A.ps_stock, A.ps_stock_object, A.ps_alarm_count,
			B.CD_NAME, B.CD_IMG, B.CD_CODE,
			C.BD_NAME 
			from prd_stock A 
			left join "._DB_COMPARISON." B ON (A.ps_prd_idx = B.CD_IDX ) 
			left join "._DB_BRAND." C  ON (B.CD_BRAND_IDX = C.BD_IDX ) 
			where ps_idx = '".$_ps_idx."' "));

		// 배열 검증
		if (!is_array($prd_data)) {
			$prd_data = [];
		}

		$_stock_sum = ($prd_data['ps_stock'] ?? 0) - ($val['qty'] ?? 0);

		if( $_stock_sum < 1 && ($prd_data['ps_stock_object'] ?? '') == "Y" ){

			$_mode = "";
			if( $_stock_sum < 0 ){
				$_mode = "stock_over";
			}elseif( $_stock_sum == 0 ){
				$_mode = "stock_zero";
			}

			$_row1[] = array(
				"mode" => $_mode,
				"ps_idx" => $_ps_idx,
				"cd_idx" => $val['cd_idx'] ?? '',
				"brand_name" => $prd_data['BD_NAME'] ?? '',
				"prd_name" => $prd_data['CD_NAME'] ?? '',
				"packageOut" => $val['packageOut'] ?? 0,
				"one_qty" => $val['one']['qty'] ?? 0,
				"set_qty" => $val['set']['qty'] ?? 0,
				"qty" => $val['qty'] ?? 0,
				"ps_stock" => $prd_data['ps_stock'] ?? 0,
				"stock_sum" => $_stock_sum,
				"ps_stock_object" => $prd_data['ps_stock_object'] ?? '',
			);

		}else{

			$_row2[] = array(
				"mode" => "basic",
				"ps_idx" => $_ps_idx,
				"cd_idx" => $val['cd_idx'] ?? '',
				"brand_name" => $prd_data['BD_NAME'] ?? '',
				"prd_name" => $prd_data['CD_NAME'] ?? '',
				"packageOut" => $val['packageOut'] ?? 0,
				"one_qty" => $val['one']['qty'] ?? 0,
				"set_qty" => $val['set']['qty'] ?? 0,
				"qty" => $val['qty'] ?? 0,
				"ps_stock" => $prd_data['ps_stock'] ?? 0,
				"stock_sum" => $_stock_sum,
				"ps_stock_object" => $prd_data['ps_stock_object'] ?? '',
			);

		}
	}

	// 배열 정렬 및 병합
	if( count($_row1) > 0 ){
		$_row1 = arr_sort( $_row1,'stock_sum', 'asc' );
		
		if( count($_row2) > 0 ){
			$_row = array_merge($_row1, $_row2);
		}else{
			$_row = $_row1;
		}

	}else{
		$_row = $_row2;
	}




?>
<style type="text/css">
.brand-name{ color:#296abc; }
.prd-name{  width: 450px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;  }
#sh_name{ height:30px; line-height:30px; }
</style>
<div class="division-top" id="sort_wrap" data-idx="" data-sort="qty">

	<!-- 
	<button type="button" class="btnstyle1 btnstyle1-sm" disabled="disabled" onclick="stockExcel.sort('qty')" >수량 높은순</button>
	<button type="button" class="btnstyle1 btnstyle1-sm"disabled="disabled"  onclick="stockExcel.sort('brand')" >브랜드 순</button>
	-->

	<span id="sh_name">
		[<?=$data['uid'] ?? ''?>] <b><?=$data['file_name'] ?? ''?></b> | 
	</span>

	<div class="float-right">
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" disabled="disabled" onclick="stockExcel.excelDown()" >엑셀 다운로드</button>
		<iframe id="excelDown_iframe" src='' style='display:none;'></iframe>
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm m-l-5" onclick="stockExcelView.swindow()" >새창열기</button>
		<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm m-l-20" onclick="stockExcelView.del(this)" ><i class="fas fa-minus-circle"></i> 삭제</button>
	</div>

</div>

<div class="division-body scroll-wrap" >

	<form id="form2">
	<input type="hidden" name="a_mode" value="day_stock">
	<input type="hidden" name="stock_history_idx" value="<?=$_idx ?? ''?>">

	<table class="table-style border01" id="">
		<thead class="sticky">
			<tr class="sticky">
				<th>재고<br>코드</th>
				<th>상품명</th>
				<th>패킹<br>재거</th>
				<th>단일<br>상품</th>
				<th>세트<br>상품</th>
				<th>출고<br>총합</th>
				<th>현재<br>재고</th>
				<th>남는<br>재고</th>
				<th></th>
				<th></th>
			</tr>
		</thead>

		<? 
		$_error_result_count = is_array($_error_data['result']) ? count($_error_data['result']) : 0;
		if( $_error_result_count > 0 ){ ?>
		<tbody>
			<tr>
				<td colspan="10">
					<div>
						※ 에러항목
					</div>
					<div>
						<? for ($i=0; $i<$_error_result_count; $i++){ ?>
							<ul><?=$_error_data['result'][$i] ?? ''?></ul>
						<? } ?>
					</div>
				</td>
			</tr>
		</tbody>
		<? } ?>

	<?
		$_notice_count1 = 0;
		$_notice_count2 = 0;

	// 배열 검증
	if (!isset($_row) || !is_array($_row)) {
		$_row = [];
	}

	foreach ( $_row as $key => $val ){
		
		// 배열 요소 검증
		if (!is_array($val)) {
			continue;
		}

		$_tr_class = "";
		if( ($val['mode'] ?? '') == "stock_over" ){
			$_tr_class = "red";
			$_notice_count1++;
		}elseif( ($val['mode'] ?? '') == "stock_zero" ){
			$_tr_class = "green";
			$_notice_count2++;
		}
	?>
	<input type='hidden' name='stock_key[]' value='<?=$val['ps_idx'] ?? ''?>'>
	<input type='hidden' name='stock_mode[]' class="stock-mode-value" value='minus'>
	<input type='hidden' name='stock_kind[]' value='판매 (엑셀)'>

		<tr class="<?=$_tr_class?>">
			<td class="text-center"><?=$val['ps_idx'] ?? ''?></td>
			<td class="text-left">

				<? if( ($val['mode'] ?? '') == "stock_over" ){ ?>
					<div style="color:#ff0000;" class="m-b-5">※ 재고 부족</div>
				<? } ?>

				<? if( ($val['mode'] ?? '') == "stock_zero" ){ ?>
					<div class="m-b-5">※ 재고 등록 후 품절</div>
				<? } ?>

				<p class="prd-name">
					<button type="button" id="aa" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?=$val['cd_idx'] ?? ''?>','info');"">보기</button>
					<? if( !empty($val['brand_name']) ) { ?><span class="brand-name">[<?=$val['brand_name']?>] </span><? } ?>
					<?=$val['prd_name'] ?? ''?>
				</p>
			</td>
			<td class="text-center"><? if( ($val['packageOut'] ?? 0) > 0 ){ ?><?=$val['packageOut']?><? } ?></td>
			<td class="text-center"><?=$val['one_qty'] ?? 0?></td>
			<td class="text-center"><?=$val['set_qty'] ?? 0?></td>
			<td><input type="text" name="stock_qry[]" style="width:40px; font-size:15px; font-weight:bold; color:#d00000;" placeholder="수량" value="<?=$val['qty'] ?? 0?>" /></td>
			<td class="text-center">
				<? if( ($val['ps_stock_object'] ?? '') == "N" ){ ?>
					∞
				<? }else{ ?>
					<?=$val['ps_stock'] ?? 0?>
				<? } ?>
			</td>
			<td class="text-center">
				<? if( ($val['ps_stock_object'] ?? '') == "N" ){ ?>
					∞
				<? }else{ ?>
					<?=$val['stock_sum'] ?? 0?>
				<? } ?>
			</td>
			<td class="stock-mode-text">출고</td>
			<td><input type="text" name="stock_memo[]" class="stock-memo" style="width:80px;" value="카페24 엑셀등록" /></td>
		</tr>
	<? } ?>
	</table>

	</form>

</div>

<div class="division-bottom text-center">

	<? if( ($data['step'] ?? '') == "2"){ ?>
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg"  disabled>등록 완료</button>
	<? }else{ ?>
		재고 처리 날짜 : 
		<div class="calendar-input" style="display:inline-block;"><input type='text' name="stock_day" id="stock_day" value="<?=date("Y-m-d")?>" ></div>
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg m-l-10"  onclick="stockExcelView.dayStock(this)">재고 입출고 등록하기</button>
	<? } ?>

</div>

<script type="text/javascript"> 
<!-- 
var stockExcelView = function() {

	var B;

	var C = function() {
	};

	return {
		
		init : function() {

		},

		swindow : function() {
			/*
			window.open(
				"/admin2/product2/prd2_stock_excel_view2.php?idx=<?=$_idx ?? ''?>&sort=qty", 
				"excelDown", 
				"width=1000,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no"
			);
			*/
			window.open(
				"/admin/sales/picking_list/<?=$_idx ?? ''?>", 
				"excelDown", 
				"width=1000,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no"
			);
		},

		del : function(obj) {

			<? if( ($data['step'] ?? '') == "2"){ ?>
				showAlert("Error", "완료된건은 삭제 불가합니다.", "alert2" );
				return false;
			<? } ?>

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '삭제하시면 데이터는 복구하지 못합니다.',
				autoClose: 'cencle|9000',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제',
						btnClass: 'btn-red',
						action: function(){

						$(obj).attr('disabled', true);
						$.ajax({
							url: "/ad/processing/prd_stock",
							data: { "a_mode":"day_stock_del", "idx":"<?=$_idx ?? ''?>" },
							type: "POST",
							dataType: "json",
								success: function(res){
									if (res.success == true ){
										alert("삭제되었습니다.");
										GC.movePage("/ad/order/stock_excel");
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
		},

		dayStock : function(obj) {

			var formData = $("#form2").serializeArray();
			var stock_day = $("#stock_day").val();
			//var stock_history_idx = $("#sort_wrap").data("idx");

			formData.push({name: "stock_day", value: stock_day});
			//formData.push({name: "stock_history_idx", value: stock_history_idx});

			$(obj).attr('disabled', true);
			$.ajax({
				url: "/ad/processing/prd_stock",
				data : formData, 
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						//GC.movePage("prd2_stock_excel.php?idx="+res.idx);
						alert("완료");
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		},

	};

}();


$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

	<? if( ($data['step'] ?? '') == "1" && ( $_notice_count1 > 0 || $_notice_count2 > 0 || $_error_result_count > 0 ) ){ ?>

		var content22 = ''


		<? if( $_error_result_count > 0 ){ ?>
			+ '- 에러항목이 ( <b><?=$_error_result_count?></b> )건 있습니다.<br>'
		<? } ?>
		<? if( $_notice_count1 > 0 ){ ?>
			+ '- 재고 부족상품이 ( <b><?=$_notice_count1?></b> )개 있습니다.<br>'
		<? } ?>
		<? if( $_notice_count2 > 0 ){ ?>
			+ '- 재고 등록후 품절될 상품이 ( <b><?=$_notice_count2?></b> )개 있습니다.<br>';
		<? } ?>


		$.confirm({
			boxWidth : "500px",
			useBootstrap : false,
			icon: 'fas fa-exclamation-triangle',
			title: '확인해주세요.',
			content: content22,
			type: 'red',
			typeAnimated: true,
			closeIcon: true,
			buttons: {
				cencle: {
					text: '확인완료',
					action: function(){
					}
				}
			}
		});		
	<? } ?>

});
//--> 
</script> 