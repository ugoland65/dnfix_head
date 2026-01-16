<?php

	// 변수 초기화
	$form_view = $_GET['form_view'] ?? $form_view ?? "show";
	$_order_sec_data = [];

	$oo_data = sql_fetch_array(sql_query_error("select * from ona_order A 
		left join ona_order_group B ON ( B.oog_idx = A.oo_form_idx )  WHERE A.oo_idx = '".$_idx."' "));

	if (!is_array($oo_data)) {
		$oo_data = [];
	}

	if( !$form_view ) $form_view = "show";
	if( ($oo_data['oo_state'] ?? 0) == 4 || ($oo_data['oo_state'] ?? 0) == 5 || ($oo_data['oo_state'] ?? 0) == 7 ){ 
		$form_view = "hidden";
	}

	$_price_colum = $oo_data['price_colum'] ?? '';

	//폼 그룹 리스트
	//$_oog_brand1 = '['.$oo_data['oog_brand'].']';
	$_oog_brand = json_decode($oo_data['oog_brand'] ?? '[]', true);
	if (!is_array($_oog_brand)) {
		$_oog_brand = [];
	}
	
	$_order_sec_json2 = $oo_data['oo_json'] ?? '[]';
	$_order_sec_json = json_decode($_order_sec_json2, true);
	if (!is_array($_order_sec_json)) {
		$_order_sec_json = [];
	}

	$_oo_sum_price2 = 0;
	$_oo_sum_goods2 = 0;
	$_oo_sum_qty2 = 0;
	$_oo_sum_weight2 = 0;

	for ($i=0; $i<count($_order_sec_json); $i++){
		
		$_os_data_idx = $_order_sec_json[$i]['bidx'] ?? '';
		$_order_sec_data[$_os_data_idx]['item'] = $_order_sec_json[$i]['item'] ?? 0;
		$_order_sec_data[$_os_data_idx]['qty'] = $_order_sec_json[$i]['qty'] ?? 0;
		$_order_sec_data[$_os_data_idx]['price'] = $_order_sec_json[$i]['price'] ?? 0;
		$_order_sec_data[$_os_data_idx]['weight'] = $_order_sec_json[$i]['weight'] ?? 0;
		
		if( ($_order_sec_json[$i]['false'] ?? 0) > 0 ){
			$_order_sec_data[$_os_data_idx]['item'] = (int)($_order_sec_json[$i]['item'] ?? 0) - (int)($_order_sec_json[$i]['false'] ?? 0);
			$_order_sec_data[$_os_data_idx]['qty'] = (int)($_order_sec_json[$i]['qty'] ?? 0) - (int)($_order_sec_json[$i]['false_sum_qty'] ?? 0);
			$_order_sec_data[$_os_data_idx]['price'] = (int)($_order_sec_json[$i]['price'] ?? 0) - (int)($_order_sec_json[$i]['false_sum_price'] ?? 0);
			$_order_sec_data[$_os_data_idx]['weight'] = (int)($_order_sec_json[$i]['weight'] ?? 0) - (int)($_order_sec_json[$i]['false_sum_weight'] ?? 0);
			$_order_sec_data[$_os_data_idx]['false'] = $_order_sec_json[$i]['false'] ?? 0;
			$_order_sec_data[$_os_data_idx]['false_sum'] = $_order_sec_json[$i]['false_sum'] ?? 0;
		}

		$_oo_sum_price2 += $_order_sec_data[$_os_data_idx]['price'] ?? 0;
		$_oo_sum_goods2 += $_order_sec_data[$_os_data_idx]['item'] ?? 0;
		$_oo_sum_qty2 += $_order_sec_data[$_os_data_idx]['qty'] ?? 0;
		$_oo_sum_weight2 += (int)str_replace(',','', $_order_sec_data[$_os_data_idx]['weight'] ?? 0);
		
	}

?>
<style type="text/css">
.order_sheet_detail{ display:table; width:100%; height:100%; background-color:#fff; border-spacing:0; border-collapse:collapse; }
.order_sheet_detail > ul { display:table-cell; vertical-align:top; height:100%; box-sizing:border-box;  }
.order_sheet_detail > ul.left{ width:180px;  background-color:#dddddd; border-right:1px solid #888; }
.order_sheet_detail > ul.right{ position:relative;  }

.overflow-y { width:100%; height:100%; overflow-y:scroll;  box-sizing:border-box; padding:5px; }
.overflow-y::-webkit-scrollbar{ width:7px; background:#ccc; border-left:solid 1px rgba(255,255,255,.1)}
.overflow-y::-webkit-scrollbar-thumb{background:linear-gradient(#0860d5,#2077ea);border:solid 1px #444; border-radius:3px; }

.ost-big{ width:100%; border:1px solid #ccc; background-color:#eee; padding:8px 10px; margin-bottom:3px; border-radius:6px; box-sizing:border-box; cursor:pointer; }
.ost-big.inorder{ background-color:#fff; }
.ost-big.active{ background-color:#ffde00; }

.ospl-wrap{ position:absolute; top:0; left:0; width:calc(100% - 7px); height:70px; background-color:#fff; box-sizing:border-box; 
	border-left:1px solid #777; border-right:1px solid #777; border-bottom:2px solid #777;
}

.ospl-top{ width:100%; height:100%;  display:table; }
.ospl-top > ul{ display:table-cell; box-sizing:border-box;  padding:6px; }
.ospl-top > ul.btn{ width:160px; display:flex; }
.ospl-top > ul.btn button{ height:100% !important; }

.group-side-allsum-price{ font-weight:bold !important; }
</style>

<script type="text/javascript"> 
<!-- 
var oogBrand = <?=$oo_data['oog_brand'] ?? '[]'?>;
//--> 
</script>

<?
/*
	echo "<pre>";
	//print_r($_oog_brand);
	//print_r($_order_sec_json);
	echo "</pre>";
*/
?>
<div class="order_sheet_detail">
	<ul class="left">
		<div class="overflow-y">
		
		<? 
		for ($i=0; $i<count($_oog_brand); $i++){
			
			$_brand = $_oog_brand[$i]['brand'] ?? '';
			$_name = $_oog_brand[$i]['name'] ?? '';
			$_oop_idx = $_oog_brand[$i]['oop_idx'] ?? '';


			$oop_data = wepix_fetch_array(wepix_query_error("select oop_data from ona_order_prd where oop_idx = '".$_oop_idx."' "));

			if (!is_array($oop_data)) {
				$oop_data = [];
			}

			$_oop_json_check_data = substr($oop_data['oop_data'] ?? '', 0, 1);
			if( $_oop_json_check_data == "[" ){
				$_oop_json = $oop_data['oop_data'] ?? '[]';
			}else{
				$_oop_json = '['.($oop_data['oop_data'] ?? '').']';
			}

			$_oop_jsondata = json_decode($_oop_json, true);
			if (!is_array($_oop_jsondata)) {
				$_oop_jsondata = [];
			}

		$_item = 0;
		$_qty = 0;
		$_price = 0;
		$_weight = 0;
		$_show_weight = "";

		if( isset($_order_sec_data[$_oop_idx]['item']) && $_order_sec_data[$_oop_idx]['item'] ) $_item = $_order_sec_data[$_oop_idx]['item'];
		if( isset($_order_sec_data[$_oop_idx]['qty']) && $_order_sec_data[$_oop_idx]['qty'] ) $_qty = $_order_sec_data[$_oop_idx]['qty'];
		if( isset($_order_sec_data[$_oop_idx]['price']) && $_order_sec_data[$_oop_idx]['price'] ) $_price = $_order_sec_data[$_oop_idx]['price'];
		if( isset($_order_sec_data[$_oop_idx]['weight']) && $_order_sec_data[$_oop_idx]['weight'] ) $_weight = $_order_sec_data[$_oop_idx]['weight'];

		if( $_weight ){
			if( (float)$_weight > 1000 ){
				$_show_weight = number_format(((float)$_weight*0.001),2)."kg";
			}else{
				$_show_weight = number_format((float)$_weight)."g";
			}
		}
		?>
		<div class="ost-big <? if($_qty > 0) echo 'inorder';?>" id="group_side_<?=$_oop_idx?>" onclick="orderSheetDetail.PrdList('<?=$_idx?>', '<?=$_oop_idx?>')">
			<ul><b><?=$_name?></b></ul>
			<ul class="m-t-3">
			<b><?=count($_oop_jsondata)?></b> / 
			<span class="oprice-sum-goods-cate" id="oprice_sum_goods_<?=$_oop_idx?>"><?=$_item?></span>
			<? if( isset($_order_sec_data[$_oop_idx]['false']) && $_order_sec_data[$_oop_idx]['false'] > 0 ){ ?>
			<span class="" id="">실패 : <?=$_order_sec_data[$_oop_idx]['false']?></span>
			<? } ?>
			</ul>
		<ul>
			<span class="oprice-sum-qty" id="group_side_sum_qty_<?=$_oop_idx?>" data-value="<?=number_format((float)$_qty)?>" ><?=number_format((float)$_qty)?></span> /
			<span class="oprice-sum-weight" id="oprice_sum_weight_<?=$_oop_idx?>"><?=$_show_weight?></span>
		</ul>
		<ul><span class="group-side-allsum-price" id="oprice_allsum_<?=$_oop_idx?>"><?=number_format((float)$_price, 2)?></span></ul>
		</div>
		<? } ?>

		</div>
	</ul>
	<ul class="right">
		<div id="order_sheet_detail_prd_list" class="scroll-wrap">
		</div>
	</ul>
</div>

<script>

var orderSheetDetail = function() {

	var detailDisplay;
	var normalFormView = "<?=$form_view?>";
	var gState = "normal";
	var open_idx = "<?=$_idx?>";
	var open_oop_idx = "";

	var ckTr = function( id, mode ) {

		if( mode == "on" ){
			$("#tr_"+ id +" td").css({'background':'#ffcbcb' }); 
			$("#checkbox_"+ id).prop("checked", true);
		}else{
			var beforetrcolor = $("#tr_"+ id).attr("bgcolor");
			//alert(beforetrcolor);
			$("#tr_"+ id +" td").css({'background':beforetrcolor }); 
			$("#checkbox_"+ id).prop("checked", false);
		}

	};

	//열려있는 그룹 합 재계산
	var groupSum = function (oop_idx) {

		var oprice_allsum = 0;
		var oprice_sum_qty = 0;
		var oprice_sum_goods = 0;
		var oprice_sum_weight = 0;

		$(".checkSelect:checked").each(function(){

			var checkbox_id = $(this).val();

			if( $("#unit_qty_" + checkbox_id).val() == "" ){
				var plus_oprice_sum_qty = 1;
			}else{
				var plus_oprice_sum_qty = ($("#unit_qty_" + checkbox_id).val() * 1);
			}

			oprice_allsum = oprice_allsum + ($("#unit_price_sum_" + checkbox_id).val() * 1);
			oprice_sum_goods++;
			oprice_sum_qty = oprice_sum_qty + plus_oprice_sum_qty;
			oprice_sum_weight = oprice_sum_weight + ($("#weight_"+checkbox_id).data('weight') * plus_oprice_sum_qty);

		});


		$("#oprice_allsum_" + oop_idx).html(GC.comma(oprice_allsum));
		$("#oprice_allsum_data_" + oop_idx).val(oprice_allsum);

		//선택상품
		$("#oprice_sum_goods_" + oop_idx ).html(GC.comma(oprice_sum_goods));
		$("#oprice_sum_goods_data_" + oop_idx ).val(oprice_sum_goods);
		if( $("#group_body_sum_goods_" + oop_idx).length ){ $("#group_body_sum_goods_" + oop_idx).html(GC.comma(oprice_sum_goods)); }


		//총 수량
		$("#group_side_sum_qty_" + oop_idx).html(GC.comma(oprice_sum_qty));
		$("#oprice_sum_qty_data_" + oop_idx).val(oprice_sum_qty);
		if( $("#group_body_sum_qty_" + oop_idx).length ){ $("#group_body_sum_qty_" + oop_idx).html(GC.comma(oprice_sum_qty));  }


		//총 무게
		if( oprice_sum_weight > 1000 ){
			var show_oprice_sum_weight = GC.comma(Math.round((oprice_sum_weight*0.001) * 100) / 100 )+"kg";
		}else{
			var show_oprice_sum_weight = GC.comma(Math.round(oprice_sum_weight))+"g";
		}

		$("#oprice_sum_weight_" + oop_idx).html(show_oprice_sum_weight);
		$("#oprice_sum_weight_data_" + oop_idx).val(oprice_sum_weight);
		if( $("#group_body_sum_weight_" + oop_idx).length ){ $("#group_body_sum_weight_" + oop_idx).html(show_oprice_sum_weight);  }

		var bw1 = $("#group_side_sum_qty_"+ detailDisplay).html() * 1;
		var bw2 = $("#group_side_sum_qty_"+ detailDisplay).data('value') * 1;

		//변경된 값이 있는지 체크
		if(  bw1 != bw2 ){
			orderSheetDetail.groupState("ing");
		}else if(  bw1 == bw2 ){
			orderSheetDetail.groupState("normal");
		}

	}


	var showPrdList = function ( oo_idx, oop_idx, form_view ) {

		if( !form_view ) form_view = normalFormView;

		$(".ost-big").removeClass('active');
		$("#group_side_" + oop_idx).addClass('active');

		$.ajax({
			url: "/ad/ajax/order_sheet_detail_prd",
			data : { "oo_idx" : oo_idx, "oop_idx" : oop_idx, "form_view" : form_view  },
			type: "POST",
			dataType: "html",
			success: function(html){
				$("#order_sheet_detail_prd_list").html(html);
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


	return {

		init : function() {

		},

		// 디테일 상품리스트
		PrdList: function( oo_idx, oop_idx ) {
			
			open_oop_idx = oop_idx;
			var showPrdListAction = true;

			if( detailDisplay ){
				if( detailDisplay == oop_idx ){
					return false;
				
				//다른 그룹으로 이동시
				}else if( detailDisplay != oop_idx ){
					
					/*
						var bw1 = $("#group_side_sum_qty_"+ detailDisplay).html() * 1;
						var bw2 = $("#group_side_sum_qty_"+ detailDisplay).data('value') * 1;

						//변경된 값이 있는지 체크
						var chModify = true;
						if(  bw1 != bw2 ){
							chModify = false;
							orderSheetDetail.groupState("ing");
						}

						//var testdata = "("+detailDisplay+")("+oop_idx+")("+$("#group_side_sum_qty_"+oop_idx).html()+")("+$("#group_side_sum_qty_"+oop_idx).data('value')+")";
						var testdata = "("+ bw1 +")("+ bw2 +")";
						toast2("success", "데이터", testdata);
					*/
				
					if( gState == "ing" ){
						
						showPrdListAction = false;
						var save_oop_idx = detailDisplay;

						$.confirm({
							icon: 'fas fa-exclamation-triangle',
							title: '그룹 변경된 내용이 있습니다.',
							content: '( 그룹 idx : '+ save_oop_idx +' )<br>변경된 내용을 저장후 그룹이동을 하시겠습니까?',
							type: 'red',
							typeAnimated: true,
							closeIcon: true,
							buttons: {
								somethingElse: {
									text: '저장',
									btnClass: 'btn-red',
									action: function(){
										orderSheetDetailPrd.groupOrder(oo_idx, save_oop_idx);
										showPrdList(oo_idx, oop_idx);
									}
								},
								cencle: {
									text: '취소',
									action: function(){
										showPrdList(oo_idx, oop_idx);
										gState = "normal";
									}
								}
							}
						});

					}else{
						gState = "normal";
					}

				}
			}
			/*
			if( detailDisplay ){
			
				if( detailDisplay == oop_idx ){
					return false;
				}

				//변경된 값이 있는지 체크
				var chModify = true;

				if(  $("#group_side_sum_qty_"+detailDisplay).html() != $("#group_side_sum_qty_"+detailDisplay).data('value') ){
					chModify = false;
					showPrdListAction = false;
				}

				if( chModify == false ){



				}

			}else{

			}
			*/

			if( showPrdListAction == true ){
				showPrdList(oo_idx, oop_idx);
			}

			detailDisplay = oop_idx;
			orderSheetDetail.groupState("normal");

		},

		PrdListReload: function( ) {
			showPrdList(open_idx, open_oop_idx);
		},



		// 디테일 상품리스트 따로 호출
		prdListShow: function( oo_idx, oop_idx, form_view ) {
			showPrdList(oo_idx, oop_idx, form_view);
		},

		//수량 변경
		qtyGogo : function( id, oop_idx ) {

			var oprice = $("#unit_price_"+ id).val();
			var v = $("#unit_qty_"+ id).val();

			if(v=="") v=0;
			if( oprice > 0 && v > 0 ) {	
				var oprice_sum = oprice*v;
				if( oprice_sum > 0 ){
					$("#unit_price_sum_"+ id).val(oprice_sum);
					$("#order_qty_sum_"+ id).html(GC.comma(oprice_sum));
					ckTr(id,"on");
				}
			}else{
				$("#unit_price_sum_"+ id).val("");
				$("#order_qty_sum_"+ id).html("");
				ckTr(id,"off");
			}

			groupSum(oop_idx);

			//toast2("success", "테스트", "id : "+ id +" | oprice : "+ oprice +" |  v : "+ v +" |  oprice_sum : "+ oprice_sum);
		},

		//그룹상태 변경
		groupState : function( mode ) {
			
			gState = mode;
			
			if( mode == "normal" ){
				$("#group_state").removeClass("ing").removeClass("end").addClass("normal").html("state : 보기중");
			}else if( mode == "ing" ){
				$("#group_state").removeClass("normal").removeClass("end").addClass("ing").html("state : 저장중");
			}else if( mode == "end" ){
				$("#group_state").removeClass("normal").removeClass("ing").addClass("end").html("state : 저장완료");
			}

		}

	};

}();

<? if( $_open_oop_idx ){ ?>
	orderSheetDetail.PrdList('<?=$_idx?>', '<?=$_open_oop_idx?>');

<? } ?>

<? if( ($oo_data['oo_form_idx'] ?? 0) == 0 ){ ?>
	showAlert("Error", "이 주문서에 [주문서 폼]이 지정되어있지 않습니다.<br>(주문서 상세정보)에서 주문서 폼을 지정해주세요.", "alert2" );
<? } ?>
//--> 
</script> 