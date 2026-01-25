<?
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 변수 초기화
$_prd_idx = $_get1 ?? "";
$prd_data = [];
$img_path = "";

// 디버깅: $_prd_idx 값 확인
if( !$_prd_idx ){
	echo "오류: 상품 IDX가 없습니다. _get1 = " . ($_get1 ?? 'undefined');
	exit;
}

if( $_prd_idx ){
	$_colum = "A.CD_IMG, A.CD_NAME, A.CD_MEMO, comment_count, A.cd_godo_code";
	$_colum .= ",B.ps_idx, B.ps_stock, B.ps_stock_hold, B.ps_rack_code";
	$_colum .= ", C.BD_NAME";

	$_query = "select ".$_colum." from "._DB_COMPARISON." A
		left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX) 
		left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND_IDX AND A.CD_BRAND_IDX > 0) 
		where A.CD_IDX = '".$_prd_idx."' ";

	// 디버깅: 쿼리 확인
	// echo "<pre>쿼리: " . $_query . "</pre>";
	
	$result = sql_query_error($_query);
	
	// 디버깅: 쿼리 결과 확인
	if(!$result){
		echo "쿼리 실행 실패";
		exit;
	}

	$prd_data = sql_fetch_array($result);
	
	// 배열 검증
	if (!is_array($prd_data) || empty($prd_data)) {
		echo "<pre>오류: 상품 데이터를 찾을 수 없습니다.<br>";
		echo "상품 IDX: " . $_prd_idx . "<br>";
		echo "쿼리: " . $_query . "</pre>";
		$prd_data = [];
	}

	// 디버깅: 데이터 확인
	// echo "<pre>prd_data: "; print_r($prd_data); echo "</pre>";

	if( !empty($prd_data['CD_IMG']) ){
		$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
	}
}

// 디버깅: 최종 데이터 확인
// echo "<pre>최종 prd_data: "; print_r($prd_data); echo "</pre>";
// echo "<pre>img_path: " . $img_path . "</pre>";

include ($docRoot."/admin2/layout/header_popup.php");
?>
<div class="prd-quick-left">
	
	<div class="prd-img">
		<? if($img_path){ ?>
			<img src="<?=$img_path?>" style="height:150px; border:1px solid #eee !important;">
		<? }else{ ?>
			<div style="width:150px; height:150px; border:1px solid #eee; display:flex; align-items:center; justify-content:center; color:#999;">이미지 없음</div>
		<? } ?>
	</div>

	<div class="prd-quick-info">
		<ul class="prd-brand-name"><?=$prd_data['BD_NAME'] ?? ''?></ul>
		<ul class="prd-name"><b><?=$prd_data['CD_NAME'] ?? ''?></b></ul>
		<!-- <ul class="prd-name-en"><?=$prd_data['CD_NAME_OG'] ?? ''?></ul> -->

		<?php if( !empty($prd_data['cd_godo_code']) ){ ?>
		<ul>
			<button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall('<?=$prd_data['cd_godo_code'] ?? ''?>');">쑈당몰 상품보기</button>
		</ul>
		<?php } ?>

		<? if( !empty($prd_data['ps_idx']) ){ ?>
			<ul class="prd-stock-code">
				<b><?=$prd_data['ps_idx']?></b>
				<? if( !empty($prd_data['ps_rack_code']) ){ ?> ( <b><?=$prd_data['ps_rack_code']?></b> )<? } ?>
			</ul>
		<? }else{ ?>
			<ul class="prd-stock-code-make"><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdInfo.makePsIdx()"> <i class="fas fa-plus-circle"></i> 재고 코드 생성</button></ul>
		<? } ?>

		<ul>
			<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-xs" onclick="footerGlobal.comment('prd','<?=$_prd_idx?>')" >
				댓글
				<? if( ($prd_data['comment_count'] ?? 0) > 0 ) { ?> : <b><?=$prd_data['comment_count']?></b><? } ?>
			</button>
		</ul>
	</div>

	<div class="crm-menu m-t-10">
		<ul id="crm_menu_info" class="active" onclick="prdInfo.mode('', 'info')">상품정보</ul>
		<ul id="crm_menu_price" class="" onclick="prdInfo.mode('', 'price')">가격정보</ul>
		<ul id="crm_menu_saleLog" class="" onclick="prdInfo.mode('', 'saleLog')">할인 로그</ul>
		<ul id="crm_menu_stock_chart" class="" onclick="prdInfo.mode('', 'stock_chart')">재고/판매량 요약</ul>
		<ul id="crm_menu_stock" class="" onclick="prdInfo.mode('', 'stock')">재고/판매 리스트</ul>
		<ul id="crm_menu_onadb_config" class="" onclick="prdInfo.mode('', 'onadb_config')">오나DB 설정</ul>
		<ul id="crm_menu_onadb_comment" class="" onclick="prdInfo.mode('', 'onadb_comment')">오나DB 한줄평</ul>
	</div>

	<? if( !empty($prd_data['ps_idx']) ){ ?>
	<div class="stock-write-box">
		<ul>현재 재고 : <b id="now_stock"><?=$prd_data['ps_stock'] ?? 0?></b></ul>

		<ul class="m-t-7">보류 재고 : <b id="now_stock_hold" style="color:#999;"><?=$prd_data['ps_stock_hold'] ?? 0?></b></ul>

		<ul class="m-t-7"><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm btnstyle1-search-full" onclick="prdInfo.stockModify()" >재고 변경등록</button></ul>
	</div>
	<? } ?>

</div>

<div class="crm-wrap">
	<ul class="crm-menu-wrap"></ul>
	<ul class="crm-gap "></ul>
	<ul class="crm-body">
		<div id="crm_body">
		
		</div>
	</ul>
</div>

<script type="text/javascript"> 
<!-- 
var prdInfo = function() {

	var prd_idx = "<?=$_prd_idx?>";
	var ps_idx = "<?=$prd_data['ps_idx'] ?? ''?>";
	var stockModifyWindow;

	var C = function() {
	};

	return {
		
		init : function() {

		},

		mode : function( pn, mode ) {

			$(".crm-menu ul").removeClass('active');
			$("#crm_menu_"+mode).addClass('active');

			var _search_date_s = "";
			var _search_date_e = "";

			if( $(".list-search-date-box").length ){
				_search_date_s = $("#search_date_s").val();
				_search_date_e = $("#search_date_e").val();
			}

			var data = { "prd_idx":prd_idx };
			var ajaxUrl = "/ad/ajax/prd_reg_form";

			if( mode == "info" ){
				ajaxUrl = "/ad/ajax/prd_reg_form";
			
			}else if( mode == "price" ){
				ajaxUrl = "/ad/ajax/prd_info_price";

			}else if( mode == "saleLog" ){ //할인 로그
				ajaxUrl = "/ad/ajax/prd_info_salelog";

			}else if( mode == "stock_chart" ){ //재고 챠트
				ajaxUrl = "/ad/ajax/prd_info_stock_chart";
				data = { "prd_idx":prd_idx, "ps_idx":ps_idx };

			}else if( mode == "stock" ){ //재고 챠트
				ajaxUrl = "/ad/ajax/prd_info_stock";
				data = { "prd_idx":prd_idx, "ps_idx":ps_idx, "pn":pn, "sdate" : _search_date_s, "edate" : _search_date_e };

			}else if( mode == "onadb_config" ){ //오나DB 설정
				ajaxUrl = "/ad/ajax/prd_info_onadb_config";

			}else if( mode == "onadb_comment" ){ //오나DB 한줄평
				ajaxUrl = "/ad/ajax/onadb_prd_comment_list";
				data = { "prd_idx":prd_idx, "pn":pn, "load_page":"prdInfo" };

			}

			$.ajax({
				url: ajaxUrl,
				data: data,
				type: "POST",
				dataType: "text",
				success: function(getHtml){
					if (getHtml){
						$("#crm_body").html(getHtml);
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {

				}
			});

		},

		makePsIdx : function( ) {

			$.ajax({
				url: "/ad/processing/prd",
				data: { "a_mode":"new_stock_psidx", "prd_idx":prd_idx },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
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

		},

		stockModify : function( ) {

			var width = "600px";

			stockModifyWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "재고 변경등록",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/prd_stock_reg',
						data: { "prd_idx":prd_idx, "ps_idx":ps_idx },
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

		stockUnitModify : function( idx, pn ) {

			var width = "600px";

			stockModifyWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "재고 유닛 수정",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/prd_stock_reg',
						data: { "prd_idx":prd_idx, "ps_idx":ps_idx, "idx":idx, "pn":pn },
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

		stockModifyClose : function( ) {
			stockModifyWindow.close();
		},

	};

}();


$(function(){

	prdInfo.mode('','info');

});
//--> 
</script>
<?
include ($docRoot."/admin2/layout/footer_popup.php");
exit;
?>