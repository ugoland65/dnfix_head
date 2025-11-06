<?
$pageGroup = "product2";
$pageName = "prd2_check";

include "../lib/inc_common.php";

$search_sql = " where CD_CODE = 'undefined' ";

$query = "select CD_IDX, CD_CODE, CD_CODE2, CD_CODE3, cd_code_fn from "._DB_COMPARISON." ".$search_sql." order by CD_IDX desc";
$result = wepix_query_error($query);

$count = 0;
while($list = wepix_fetch_array($result)){

/*
	$_cd_code_data = array(
		'jan' => $list[CD_CODE],
		'pcode' => $list[CD_CODE2],
		'code' => $list[CD_CODE2],
		'npg' => $list[CD_CODE3]
	);

	$_cd_code_data = json_decode($list[cd_code_fn], true);

	$_cd_code_data['mg'] = $list[CD_CODE2];
	$_cd_code_fn = json_encode($_cd_code_data, JSON_UNESCAPED_UNICODE);
*/

	wepix_query_error("update "._DB_COMPARISON." set CD_CODE = '' where CD_IDX = '".$list[CD_IDX]."' ");

	$count++;
/*
	$_modify_cd_size = preg_replace("/\s+/","",$list[CD_SIZE]);
	$_modify_cd_size = preg_replace("/mm+/","",$_modify_cd_size);
	$_modify_cd_size = preg_replace("/W+/","",$_modify_cd_size);
	$_modify_cd_size = preg_replace("/H+/","",$_modify_cd_size);
	$_modify_cd_size = preg_replace("/D+/","",$_modify_cd_size);
	$_modify_cd_size = preg_replace("/X+/","x",$_modify_cd_size);
	$_modify_cd_size_array = explode("x", $_modify_cd_size);

	if( count($_modify_cd_size_array) == 3 ){

		$_cd_size_w = $_modify_cd_size_array[0];
		$_cd_size_h = $_modify_cd_size_array[1];
		$_cd_size_d = $_modify_cd_size_array[2];

		$_cd_size_data = array(
			'W' => $_cd_size_w,
			'H' => $_cd_size_h,
			'D' => $_cd_size_d
		);

		$_cd_size = json_encode($_cd_size_data);



	}
---------------------
	$_cd_weight_data = array(
		'1' => $list[CD_WEIGHT],
		'2' => $list[CD_WEIGHT2],
		'3' => $list[CD_WEIGHT3]
	);

	$_cd_weight_fn = json_encode($_cd_weight_data);
*/

}
?>
처리 : <?=$count?>
<?
exit;

include "../layout/header.php";

?>
<STYLE TYPE="text/css">
img{ 
/*
image-rendering: pixelated; 
*/
image-rendering: -webkit-optimize-contrast;
}
.ag-kind{ width:50px !important; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }

.margin-box{ border:1px solid #ccc !important; background-color:#f7f7f7;  padding:5px; margin-bottom:5px; border-radius: 5px; box-sizing:border-box; position:relative;  }
.margin-box-wrap{ border:1px solid #ccc !important; background-color:#f7f7f7;  padding:5px; margin:2px 0 2px; border-radius: 5px; box-sizing:border-box; position:relative; text-align:left;  }
.margin-box-calculation{ position:absolute; top:-40px; left:-405px; width:400px; border:1px solid #000 !important; z-index:9999999; background-color:#222; padding:10px; border-radius: 7px; color:#ddd; line-height:140%; display:none; }

.margin-box-wrap-new{ position:relative;  } 
.margin-box-calculation-new{ position:absolute; top:-40px; left:-505px; width:500px; text-align:left; border:1px solid #000 !important; z-index:9999999; background-color:#222; padding:10px; border-radius: 7px;  line-height:140%; display:none; }
.margin-box-calculation-new div{ color:#ddd !important;  } 
.margin-box-calculation-new div input{ color:#000 !important;  } 
.show_cost_result{}
.show_cost_result ul{ padding:3px;}
.cost-p{ color:#ffce0a; font-size:13px; }
.cost-p2{ color:#b8b28b; font-size:13px; }
.o-p{ color:#48efb6; font-size:13px; }
.show_cost{ margin-top:8px; }
.sc-title{ font-weight:bold; font-size:13px; color:#eee; }
</STYLE>


<script type='text/javascript'>
	var _yen = "<?=$yen?>";
	var _kg_p = "<?=$kg_p?>";
</script>

<div id="contents_head">
	<h1>상품 목록</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_COMPARISON_REG?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">
					<table class="table-list">
						<tr>
							<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
							<th style="width:45px;">IDX</th>
							<th style="width:55px;">이미지</th>
							<th>이름</th>
							<th>브랜드</th>
							<th>오류 내용</th>
						</tr>
<?
while($list = wepix_fetch_array($result)){

	$_view_brand_name = $list[bd_name1];
	$_view_brand2_name = $list[bd_name2];

	//$brand_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$list[CD_BRAND_IDX]."' "));
	//$brand2_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$list[CD_BRAND2_IDX]."' "));
	//$stock_data = wepix_fetch_array(wepix_query_error("select ps_stock from prd_stock where ps_prd_idx = '".$list[CD_IDX]."' "));

	$_stock_qty = "";
	if( $list[ps_stock] > 0 ){
		$_stock_qty = $list[ps_stock];
	}

							
							

							if( $list[CD_WEIGHT2] > 0 ) { $_weight = $list[CD_WEIGHT2]; }else{ $_weight = $list[CD_WEIGHT]; }

							$img_path = '../../data/comparion/'.$list[CD_IMG];

							if($list[CD_COMPARISON] == "N" ){
								$_trcolor = "#eee";
							}else{
								$_trcolor = "#fff";
							}

	$_cd_size_data = json_decode($list[CD_SIZE], true);

	$_check_list = array();

	if( !$list[CD_SIZE] ){
		array_push($_check_list,"패키지 사이즈 정보가 없음");
	}
	if(is_array($_cd_size_data) != 1) {
		array_push($_check_list,"패키지 사이즈 정보 오류");
	}
?>

<tr bgcolor="<?=$_trcolor ?>">
	<td class="tl-check"><input type="checkbox" name="key_check[]" value="<?=$list[CD_IDX]?>" ></td>
	<td>
		<? if( $list[CD_SALE_STATE] == "N" ){ ?><b style='color:red; font-size:13px;'>단종</b><br><? } ?>
		<?=$list[CD_IDX]?><br>
		(<b style='color:red; font-size:14px;'><?=$list[ps_idx]?></b>)
	</td>
	<td>
		<? if($list[CD_COMPARISON] == "N" ){?>비노출<br><?}?>
		<? if( $list[CD_IMG]){?><img src="<?=$img_path?>" style="width:70px; "><? } ?>
		<br><b><?=$koedge_prd_kind_name[$list[CD_KIND_CODE]]?></b>
	</td>
							<td class="text-left" style="max-width:460px;">
								<div>
									<ul style="font-size:11px; margin-bottom:5px;"><?=$list[CD_RELEASE_DATE]?></ul>
									<ul ><b onclick="comparisonQuick('<?=$list[CD_IDX]?>','info');" style="cursor:pointer;"><?=$list[CD_NAME]?></b></ul>
									<? if($list[CD_NAME_OG]){ ?><ul style="margin-top:5px;"><?=$list[CD_NAME_OG]?></ul><? } ?>

									<ul style="margin-top:5px;"><?=$list[CD_CODE]?> | <?=$list[CD_CODE3]?> | <?=$list[CD_CODE2]?></ul>

									<? if($list[CD_MEMO]){ ?><ul style="margin-top:5px;"><span style='color:red; table-layout:fixed;word-break:break-all'><?=nl2br($list[CD_MEMO])?></ul></span><? } ?>
								</div>
							</td>
							<td>
								<a href="prd2_list.php?s_active=on&s_brand=<?=$list[CD_BRAND_IDX]?>"><?=$_view_brand_name?></a><br>
								<? if( $list[CD_BRAND2_IDX] ){ ?><a href="prd2_list.php?s_active=on&s_brand=<?=$list[CD_BRAND2_IDX]?>"><?=$_view_brand2_name?></a><br><? } ?>
								<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="location.href='<?=_A_PATH_COMPARISON_REG?>?s_brand=<?=$list[CD_BRAND_IDX]?>'"><?=$_view_brand_name?> 상품등록</button>
							</td>
	<td>
		<? for ($i=0; $i<count($_check_list); $i++){ ?>
			<div>(<?=($i+1)?>) <?=$_check_list[$i]?></div>
		<? } ?>
	</td>

						</tr>
						<? } ?>
					</table>
				</div><!-- #list_box2 -->
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
					<form name='search_form' id='search_form'  method='get' action="prd2_list.php">
					<input type="hidden" name="s_active" value="on">
					<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
<!-- 
					<ul class="filter-from-ui m-t-5">
						<select name="search_type">
							<option value="" >타입선택</option>
							<option value="standby">standby</option>
							<option value="approval">approval</option>
							<option value="cancel" >cancel</option>
						</select>
					</ul>

					<ul class="filter-from-ui m-t-5">
						<input type="text" id="s_day" name="s_day" value="<?=$s_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="시작일" readonly /> ~
						<input type="text" id="e_day" name="e_day" value="<?=$e_day?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="종료일" readonly />
					</ul>
 -->
					<ul class="filter-from-ui m-t-5">
						<input type='text' name='s_text' id='s_text' size='20' value="<?=$_s_text ?>" placeholder="상품이름">
					</ul>

					<ul class="filter-from-ui m-t-5">
						<select name="s_brand" class="selectpicker">
							<option value="">전체 브랜드</option>
<?
	$brand_result = wepix_query_error("select BD_IDX,BD_NAME from "._DB_BRAND." order by BD_NAME asc ");
	while($brand_list = wepix_fetch_array($brand_result)){
?>
							<option value="<?=$brand_list[BD_IDX]?>" <? if( $_s_brand == $brand_list[BD_IDX] ) echo "selected";?> ><?=$brand_list[BD_NAME]?></option>
<? } ?>
						</select>
					</ul>

					<ul class="filter-from-ui m-t-5">
						<select name="s_kind_code">
							<option value="">전체 종류</option>
<?
for($t=0; $t<count($koedge_prd_kind_array); $t++){
?>
							<option value="<?=$koedge_prd_kind_array[$t]['code']?>" <? if( $s_kind_code == $koedge_prd_kind_array[$t]['code'] ) echo "selected";?>><?=$koedge_prd_kind_array[$t]['name']?></option>
<?
}
?>
						</select>
					</ul>


					<ul class="filter-from-ui m-t-5">
						SORT : 
						<select name="sort_kind">
							<option value="idx" <? if( $_sort_kind == "idx" ) echo "selected";?> >IDX</option>
							<option value="hit" <? if( $_sort_kind == "hit" ) echo "selected";?>>조회수 많은순</option>
							<option value="stock" <? if( $_sort_kind == "stock" ) echo "selected";?>>재고 많은순</option>
							<option value="release_date" <? if( $_sort_kind == "release_date" ) echo "selected";?>>출시일 최근순</option>
						</select>
					</ul>

					<ul class="filter-from-ui m-t-5">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="goSerch();" > 
							<i class="fas fa-search"></i> 검색
						</button>
					</ul>

					</form>

					<ul class="filter-menu-title" style="margin-top:20px;"><i class="fas fa-calculator"></i> 원가 계산기</ul>
					<ul class="filter-from-ui m-t-5">
						적용환율 <input type='text' name='' id='calculator_ex_yen' style="width:50px" value="1160" placeholder="적용환율">
						1kg 배송비 <input type='text' name='' id='calculator_kg_p' style="width:50px" value="6000" placeholder="1kg 배송비">
					</ul>
					<ul class="filter-from-ui m-t-5">
						상품가(엔) <input type='text' name='' id='calculator_o_p' style="width:150px" value="">
					</ul>
					<ul class="filter-from-ui m-t-5">
						상품무게(g) <input type='text' name='' id='calculator_weight' style="width:150px" value="">
					</ul>
					<ul class="filter-from-ui m-t-5">
						<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="calculatorShow();"><i class="fas fa-calculator"></i> 원가계산하기</button>
					</ul>
					<ul id="calculator_result">
					</ul>

					<ul class="filter-menu-title" style="margin-top:20px;"><i class="fas fa-calculator"></i> 판매가 계산기</ul>
					<ul class="filter-from-ui m-t-5">
						원가 <input type='text' name='' id='ma_o_p' style="width:100px" value="1160" placeholder="적용환율">
						퍼센트 <input type='text' name='' id='ma_per' style="width:50px" value="40" placeholder="1kg 배송비">
					</ul>
				</div>
			</ul>
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>
	</div>
</div>

<script type="text/javascript"> 
<!-- 

function calculatorShow(){

	var o_p = $("#calculator_o_p").val();
	var ex_yen = $("#calculator_ex_yen").val();
	var weight = $("#calculator_weight").val();
	var kg_p = $("#calculator_kg_p").val();

	var op_won = o_p * (ex_yen/100); //원가 원전환
	var delivery_p  = weight * (kg_p * 0.001); // 배송비
	var tariff_p = Math.round(op_won*0.08,0); //관세
	var tariff_vat_p = Math.round((op_won + tariff_p)*0.1,0); //부가세

	var tax_p = tariff_p + tariff_vat_p + delivery_p;
	var cost_p = op_won + tax_p;

	var str = "<div>";
	str += "<ul>원전환 : "+ Comma_int(o_p)+ "￥ -> "+ Comma_int(op_won) + "원</ul>";
	str += "<ul>관세(8%) : "+ Comma_int(tariff_p)+"원 / 부가세 : "+ Comma_int(tariff_vat_p)+"원 </ul>";
	str += "<ul>배송비 : "+Comma_int(delivery_p)+"원 = "+Comma_int(tax_p)+"원</ul>";
	str += "<ul>원가 : <b>"+Comma_int(cost_p)+"</b>원</ul>";
	str += "</div>";

	$("#calculator_result").html(str);
	
	//alert(str);

}
function calculatorShow2(){
	var ma_o_p = $("#ma_o_p").val();
	var ma_per = $("#ma_per").val();

17725
}

function goSerch(){
	$("#search_form").submit();
}

function calculationView( idx, mode, view ){
	if( view == "closed" ){
		$("#margin_box_calculation_"+ mode +"_"+ idx).hide();
	}else{

		$(".margin-box-calculation").each(function(){
			$(this).hide();
		});
		
		$("#margin_box_calculation_"+ mode +"_"+ idx).show();
	}
}




function calculationViewDel( idx ){
	$("#margin_box_calculation_new_"+ idx).hide();
}


function calculationViewNew( idx ){

		$(".margin-box-calculation-new").each(function(){
			$(this).hide();
		});
		
		costShow( idx );

		$("#margin_box_calculation_new_"+ idx).show();

}


function costShow( idx ){
	$.ajax({
		url: "ajax_cost_show.php",
		data: {
			"idx":idx,
			"yen":_yen,
			"kg_p":_kg_p
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#cost_show_'+idx).html(getdata);
		},
		error: function(){
		}
	});
}


function quickPriceModify( idx ){

	var cd_supply_price_1 = $("#cd_supply_price_1_"+idx).val();
	var cd_supply_price_2 = $("#cd_supply_price_2_"+idx).val();
	var cd_supply_price_3 = $("#cd_supply_price_3_"+idx).val();
	var cd_supply_price_4 = $("#cd_supply_price_4_"+idx).val();
	var cd_supply_price_5 = $("#cd_supply_price_5_"+idx).val();
	var cd_supply_price_6 = $("#cd_supply_price_6_"+idx).val();
	var cd_supply_price_7 = $("#cd_supply_price_7_"+idx).val();
	var cd_supply_price_8 = $("#cd_supply_price_8_"+idx).val();
	var cd_supply_price_9 = $("#cd_supply_price_9_"+idx).val();
	var cd_weight = $("#cd_weight_"+idx).val();
	var cd_weight2 = $("#cd_weight2_"+idx).val();

		$.ajax({
			type: "post",
			url : "<?=_A_PATH_COMPARISON_OK?>",
			data : { 
				a_mode : "quickPriceModify",
				idx : idx ,
				cd_supply_price_1 : cd_supply_price_1,
				cd_supply_price_2 : cd_supply_price_2,
				cd_supply_price_3 : cd_supply_price_3,
				cd_supply_price_4 : cd_supply_price_4,
				cd_supply_price_5 : cd_supply_price_5,
				cd_supply_price_6 : cd_supply_price_6,
				cd_supply_price_7 : cd_supply_price_7,
				cd_supply_price_8 : cd_supply_price_8,
				cd_supply_price_9 : cd_supply_price_9,
				cd_weight : cd_weight ,
				cd_weight2 : cd_weight2
			},
			success: function(getdata) {
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					costShow( idx );

				}else if(ckcode=="Value_null"){

				}
			}
		});

}

	function goDel(idx){

		var prd_stock = $("#prd_stock_"+idx).html()*1;
		var msg = "";
		
		if( prd_stock > 0 ){
			msg = "재고가 있습니다. 재고 일일 데이터가 있을경우 삭제되지 않습니다.";
		}else{
			msg = "삭제하시겠습니까?";
		}

		if(confirm(msg)){
			$.ajax({
				type: "post",
				url : "<?=_A_PATH_COMPARISON_OK?>",
				data : { 
					a_mode : "comparisonDel",
					idx : idx
				},
				success: function(getdata) {
					makedata = getdata.split('|');
					ckcode = makedata[1];
					ckmsg = makedata[2];

					if(ckcode=="Processing_Complete"){
						alert('삭제완료');
						location.reload();
					}else{
						alert(ckmsg);
						return false;
					}
				}
			});
		}

	}
//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>

