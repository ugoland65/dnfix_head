<?
$pageGroup = "member";
$pageName = "member_info_popup";

include "../lib/inc_common.php";

	$_idx = securityVal($idx);
	$_vmode = securityVal($vmode);
	$_parent_reload = securityVal($parent_reload);

	$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
	$stock_data = wepix_fetch_array(wepix_query_error("select * from prd_stock where ps_prd_idx = '".$_idx."' "));
	$brand_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$comparison_data[CD_BRAND_IDX]."' "));

	$_view_brand_name = $brand_data[BD_NAME];

	if($comparison_data[CD_IMG] ){
		$img_path = '../../data/comparion/'.$comparison_data[CD_IMG];
	}
	
	$popup_browser_title = "상품 - ( ".$comparison_data[CD_NAME]." )";

include "../layout/header_popup.php";
?>
<STYLE TYPE="text/css">
.crm-wrap{ width:100%; height:calc(100% - 30px); }
.crm-menu-wrap{ width:200px; border-right:1px solid #9c9fae; }
.crm-gap{ width:5px; border-right:1px solid #9c9fae; }
.crm-body{ padding:20px 20px 20px; box-sizing:border-box; background-color:#dddddd; }

.crm-user-nick{ font-family: 'Godo', sans-serif; font-size:15px;  }
.crm-title{ height:30px; }
.crm-title h3{ height:30px; line-height:30px !important; display:inline-block; font-family: 'Godo', sans-serif; font-size:17px; margin:0 !important; padding:0 !important; }

#prd-quick-left{ width:200px; height:100%; background-color:#fff; border-right:1px solid #9c9fae;  position:fixed; }

.crm-menu{ width:100%; border-top:1px solid #9c9fae; }
.crm-menu ul{ width:100%; height:35px !important; line-height:35px; padding:0 0 0 15px !important; margin:0 !important; box-sizing:border-box; border-bottom:1px solid #9c9fae; cursor:pointer; }

.prd-img{ text-align:center; padding-top:20px; }
.prd-quick-info{ margin:0; padding:0; }
.prd-quick-info ul{ margin:0; padding:3px 0; box-sizing:border-box;  }
.prd-brand-name{ padding-top:10px !important; text-align:center;  }
.prd-name{ padding-top:5px !important; text-align:center; }
.prd-name-en{  text-align:center; } 
.prd-stock-code{ padding-top:7px !important;  text-align:center; font-size:20px; }
.prd-stock-code-make{ text-align:center; } 

.crm-menu-active{
	color:#fff;
	font-weight:bold;
	background-color:#2070db; 
	background: -webkit-linear-gradient(180deg, #0088cc, #0044cc);
	background:    -moz-linear-gradient(180deg, #0088cc, #0044cc);
	background:     -ms-linear-gradient(180deg, #0088cc, #0044cc);
	background:      -o-linear-gradient(180deg, #0088cc, #0044cc);
	background:         linear-gradient(180deg, #0088cc, #0044cc);
}

.stock-write-box{ padding:5px; }
.stock-write-box ul{ padding:2px !important; margin:0;   }
</STYLE>

<div id="prd-quick-left">
		<div class="prd-img">
			<img src="<?=$img_path?>" style="height:150px; border:1px solid #eee !important;">
		</div>
		<div class="prd-quick-info">
			<ul class="prd-brand-name"><?=$_view_brand_name?></ul>
			<ul class="prd-name"><b><?=$comparison_data[CD_NAME]?></b></ul>
			<!-- <ul class="prd-name-en"><?=$comparison_data[CD_NAME_OG]?></ul> -->
<?
if( $stock_data[ps_idx] ){ 
?>
			<ul class="prd-stock-code"><b><?=$stock_data[ps_idx]?></b></ul>
<? }else{ ?>
			<ul class="prd-stock-code-make"><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="makePsIdx()"> <i class="fas fa-plus-circle"></i> 재고 코드 생성</button></ul>
<? } ?>

		</div>

		<div class="crm-menu m-t-10">
			<ul id="crm_menu_info" class="" onclick="prdView.mode('info')">정보</ul>
			<ul id="crm_menu_price" class="" onclick="prdView.mode('price')">가격정보</ul>
			<ul id="crm_menu_saleLog" class="" onclick="prdView.mode('saleLog')">할인 로그</ul>
<!-- 
			<ul id="crm_menu_comparison" class="" onclick="prdView.mode('comparison')">가격비교 정보</ul>
 -->
<?
if( $stock_data[ps_idx] ){ 
?>
			<ul id="crm_menu_stock" class="" onclick="prdView.mode('stock')">재고현황</ul>
			<ul id="crm_menu_stockChart" class="" onclick="prdView.mode('stockChart')">재고현황 그래프</ul>
<? } ?>
			<ul id="crm_menu_contents" class="" onclick="prdView.mode('contents')">컨텐츠 관리</ul>

			<ul id="crm_menu_comment" class="" onclick="prdView.mode('comment')">상품댓글</ul>
		</div>

	<form name='form1' id='stockWriteform' action='/admin2/product2/processing.prd2_stock.php' method='post' enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="a_mode" value="quick_stock">
	<input type="hidden" name="idx" value="<?=$_idx?>">
	<input type="hidden" name="stock_idx" value="<?=$stock_data[ps_idx]?>">

		<div class="stock-write-box">
			<ul>현재 재고 : <b><?=$stock_data[ps_stock]?></b></ul>
			<ul>입출고 기록</ul>
			<ul>
				<input type="text" id="stock_day" name="stock_day" value="<?=date("Y-m-d")?>" style="width:80px; cursor:pointer;" readonly />
			</ul>
			<ul>
				<select name='stock_mode' class='selectpicker' >
					<option value='plus'>입고</option>
					<option value='minus'>출고</option>
				</select>
				<select name="stock_kind" >
					<option value="판매">- 판매</option>
					<option value="서비스">- 서비스</option>
					<option value="신규입고">+ 신규입고</option>
					<option value="반품">+ 반품</option>
					<option value="조정">조정</option>
				</select>
			</ul>
			<ul>
				수량 : <input type="text" name="stock_qry" style="width:80px;" placeholder="수량" value="1"/>
			</ul>
			<ul>
				<input type="text" name="stock_memo" style="width:150px;"  placeholder="메모" />
			</ul>
			<ul>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="stockWriteSubmit()">재고 등록</button>
			</ul>
	</form>

		</div>

</div>



<div class="crm-wrap display-table">
	<ul class="crm-menu-wrap display-table-cell v-align-top">
	</ul>
	<ul class="crm-gap display-table-cell"></ul>
	<ul class="crm-body display-table-cell v-align-top">
		<div id="crm_body">
		
		</div>
	</ul>
</div>


<script type="text/javascript"> 
<!-- 

var prdView = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		mode : function(mode, pn) {

			$(".crm-menu ul").each(function(){
				$(this).attr("class","");
			});

			$("#crm_menu_"+mode).attr("class","crm-menu-active");

			var stockMode = "nomal";

			var data = {"prd_idx":"<?=$_idx?>", "s_day" : s_day, "e_day" : e_day, "pn" : pn, "mode" : stockMode };

			if( mode == "info" ){
				var ajaxUrl = "ajax.comparison_modify_info.php";
			}else if( mode == "comparison" ){
				var ajaxUrl = "ajax.comparison_modify_comparison.php";
			}else if( mode == "stock" ){
				var ajaxUrl = "ajax.comparison_modify_stock.php";

			}else if( mode == "contents"){
				var ajaxUrl = "ajax.prd_contents.php";

			}else if( mode == "price" ){
				//var ajaxUrl = "ajax.comparison_modify_price.php";
				var ajaxUrl = "/ad/ajax/prd_info_price";

			//할인 로그
			}else if( mode == "saleLog" ){
				var ajaxUrl = "/ad/ajax/prd_info_salelog";

			}else if( mode == "stockChart"){
				var ajaxUrl = "ajax.comparison_modify_stockChart.php";
			}else if( mode == "stockChartSearch"){
				var ajaxUrl = "ajax.comparison_modify_stockChart.php";
				stockMode = "search";
			}else if( mode == "comment"){
				var ajaxUrl = "ajax.comparison_modify_comment.php";
			}

			var s_day = $("#s_day").val();
			var e_day = $("#e_day").val();
			
			if( pn == "" || pn== null ) var pn = 1;

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

		}
	};

}();

function prdShow(mode, pn){
	

}

function prdShowPaging(pn, mode){
	prdView.mode(mode, pn);
}

function pclose1(){
    opener.location.reload();
    //window.close();
}

<? if( !$_vmode || $_vmode == "comparison" ){?>
prdView.mode("comparison");
<? }elseif($_vmode){ ?>
prdView.mode("<?=$_vmode?>");
<? } ?>

<?
if( $_parent_reload == "ok" ){
?>
	pclose1();
<? } ?>



function makePsIdx(){

	$.ajax({
		url: "/admin2/product2/processing.prd2_stock.php",
		data: {
			"a_mode":"new_stock_prd_one",
			"ajax_mode":"on",
			"passkey":"<?=$_idx?>"
		},
		type: "POST",
		dataType: "text",
		success: function(getdata){
			if (getdata){
				redatawa = getdata.split('|');
				ckcode = redatawa[1];
				ckmsg = redatawa[2];
				if(ckcode == "Processing_Complete"){
					window.location.reload();
				}else{
					return false;
				}
			}
		},
		error: function(){
			$('#modal_alert_msg').html('에러');
			$('#modal-alert').modal({show: true,backdrop:'static'});
		}
	});

}

function stockWriteSubmit(){
	$("#stockWriteform").submit();
}


//--> 
</script> 

<!-- 
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2020.2.617/styles/kendo.default-v2.min.css" />
<script src="https://kendo.cdn.telerik.com/2020.2.617/js/jquery.min.js"></script>
<script src="https://kendo.cdn.telerik.com/2020.2.617/js/kendo.all.min.js"></script>

<link rel="stylesheet" href="/admin2/css/daterangepicker.css" />
<script src="/admin2/js/moment.min.js"></script>
<script src="/admin2/js/jquery.daterangepicker.js"></script>
 -->


<?
include "../layout/footer_popup.php";
exit;
?>