<?
$pageGroup = "product2";
$pageName = "order_sheet_list";

include "../lib/inc_common.php";

include "../layout/header.php";

?>
<div id="contents_head">
	<h1>주문서</h1>
    <div id="head_write_btn">
	</div>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">

<STYLE TYPE="text/css">
.name{ text-align:left !important; }
.now-price{ width:70px; text-align:right !important; }
.now-price2{ width:100px; text-align:right !important; }
.now-price input{ text-align:right; padding:0 5px; }
.table-exel tr td { text-align:center; height:30px; }

.frame{ display:table; }
.frame-left{ display:table-cell; vertical-align:top; }
.frame-right{ display:table-cell; vertical-align:top; padding-left:10px; }

.save-btn-wrap{ z-index:300; padding:10px 10px; position:fixed; bottom:30px; right:30px; background-color:rgba(0,0,0,0.4); border:1px solid #000000; text-align:center; vertical-align:middle; }
.save-btn-wrap button{ }

.table-exel tr.npg td { background-color:#ffe599 !important; }

</STYLE>

<div class="frame">
	<div class="frame-left">

	<form name='form1' id='form1' action='processing.order_sheet.php' method='post' enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="a_mode" value="orderSort">

<table class="table-exel">
<tr>
	<th></th>
	<th>주문서명</th>
	<th>주문가(엔)</th>
	<th>주문가(원)</th>
	<th>송금일</th>
	<th>수입신고가</th>
	<th>관부가세</th>
	<th>납기일</th>
	<th>처리일</th>
	<th>박스수</th>
	<th>총무게</th>
	<th>확정무게</th>
	<th>배송사</th>
	<th>배송금</th>
	<th>납기일</th>
	<th>처리일</th>
	<th>kg당 비용</th>
</tr>
<?
	$_ko_code = "'koetc','roma','lust','ko5','ko4','ko3','ko2','ko1','tenga'";

	if( $_mode == "ko" ){
		$_where = " WHERE oo_code IN (".$_ko_code.") ";
	}else{
		$_where = " WHERE oo_code NOT IN (".$_ko_code.") ";
	}


$total_count = wepix_counter("ona_order", $_where);
$inst_sort = $total_count + 1;
$query = "select * from ona_order ".$_where." order by oo_sort desc";
$result = wepix_query_error($query);
while($list = wepix_fetch_array($result)){

	$_oo_code = $list[oo_code];
	$_oo_price_date = ( $list[oo_price_date] > 0 ) ? $list[oo_price_date] : "" ; 
	$_oo_duty_due_date = ( $list[oo_duty_due_date] > 0 ) ? $list[oo_duty_due_date] : "" ; 
	$_oo_duty_settlement_date = ( $list[oo_duty_settlement_date] > 0 ) ? $list[oo_duty_settlement_date] : "" ; 
	$_oo_box = ( $list[oo_box] > 0 ) ? $list[oo_box] : "" ; 
	$_oo_box_weight = ( $list[oo_box_weight] > 0 ) ? $list[oo_box_weight]*1 : "" ; 
	$_oo_box_weight_fix = ( $list[oo_box_weight_fix] > 0 ) ? $list[oo_box_weight_fix]*1 : "" ; 
	$_oo_express_price = ( $list[oo_express_price] > 0 ) ? number_format($list[oo_express_price]) : "" ; 
	$_oo_express_price_date = ( $list[oo_express_price_date] > 0 ) ? $list[oo_express_price_date] : "" ; 


if( $list[oo_express_price] > 0 && $list[oo_box_weight] > 0  ){
	$kg_price1 = number_format(($list[oo_express_price]*1) / ($list[oo_box_weight]*1));
}else{
	$kg_price1 = "";
}

if( $list[oo_express_price] > 0 && $list[oo_box_weight_fix] > 0  ){
	$kg_price2 = number_format(($list[oo_express_price]*1) / ($list[oo_box_weight_fix]*1));
}else{
	$kg_price2 = "";
}

$_oo_express_number = $list[oo_express_number];

if( $_oo_express_number ){
	if( $list[oo_express] == "FEDEX" ){
		$_oo_express = "<a href='https://www.fedex.com/fedextrack/?trknbr=".$_oo_express_number."' target='_blank'>페댁스</a>";
	}
}else{
	$_oo_express = "";
}

/* ------------------- */
//$_oo_express_price_settlement_date = ( $list[oo_express_price_settlement_date] > 0 ) ? $list[oo_express_price_settlement_date] : "미처리" ; 
if( $list[oo_express_price_settlement_date] == 0 ){
	$_oo_express_price_settlement_date = "미처리";
	//$_sum_express_price[$list[oo_express_price_date]] = $list[oo_express_price];
}else{
	$_oo_express_price_settlement_date = $list[oo_express_price_settlement_date];
}

if( $list[oo_duty_price] > 0 && $list[oo_duty_settlement_date] == 0 ){
	$date_code = str_replace('-' , '', $_oo_duty_due_date);
	if( !${'ck'.$date_code} ){
		${'ck'.$date_code} = "on";
		$_ary_ck_date[] = $date_code;
		${'ck'.$date_code.'_date'} = $_oo_duty_due_date;
	}
	${'ck'.$date_code.'_duty_price'} += $list[oo_duty_price];
	${'ck'.$date_code.'_duty_price_count'} += 1;
}

if( $list[oo_express_price] > 0 && $list[oo_express_price_settlement_date] == 0 ){
	$date_code = str_replace('-' , '', $_oo_express_price_date);
	if( !${'ck'.$date_code} ){
		${'ck'.$date_code} = "on";
		$_ary_ck_date[] = $date_code;
		${'ck'.$date_code.'_date'} = $_oo_express_price_date;
	}
	${'ck'.$date_code.'_express_price'} += $list[oo_express_price];
	${'ck'.$date_code.'_express_price_count'} += 1;
}

	//$_rank_num = $list[oo_sort];

?>
<tr id="<?=$list[oo_idx]?>" class="<?=$_oo_code?>">
	<td>
		<input type='hidden' name='modify_idx[]' value='<?=$list[oo_idx]?>'>
		<input type="radio" name="chk" id="radio_<?=$list[oo_idx]?>" value="<?=$list[oo_idx]?>" onclick="chkSelect(this)" />
		<!-- <?=$list[oo_sort]?> -->
		<input type="hidden" name="modify_sort[]" id="sort_v_<?=$list[oo_idx]?>" value="<?=$list[oo_sort]?>" style="width:30px;">
		
	</td>
	<td class="name" onclick="orderSView('<?=$list[oo_idx]?>')">
		[<?=$list[oo_idx]?>] <b><?=$list[oo_name]?></b>
	</td>
	<td class="now-price"><?=number_format($list[oo_sum_price])?></td>
	<td class="now-price"><?=number_format($list[oo_price_kr])?></td>
	<td><?=$_oo_price_date?></td>
	<td class="now-price"><?=number_format($list[oo_reported_price])?></td>
	<td class="now-price"><?=number_format($list[oo_duty_price])?></td>
	<td class="now-price"><?=$_oo_duty_due_date?></td><!-- 관부가세 납기일 -->
	<td class="now-price"><?=$_oo_duty_settlement_date?></td>
	<td class=""><?=$_oo_box?></td>
	<td class=""><?=$_oo_box_weight?></td>
	<td class=""><?=$_oo_box_weight_fix?></td>
	<td class=""><?=$_oo_express?></td>
	<td class=""><?=$_oo_express_price?></td>
	<td class=""><?=$_oo_express_price_date?></td><!-- 배송비 납기일 -->
	<td class=""><?=$_oo_express_price_settlement_date?></td>
	<td class=""><?=$kg_price1?> / <?=$kg_price2?></td>
</tr>
<? } ?>
</table>

	</form>

</div>
	<div class="frame-right">

		<table class="table-exel">
<tr>
	<th>날짜</th>
	<th>관부가세</th>
	<th>배송비</th>
	<th>합계</th>
</tr>
<?
/*
$query = "select * from ona_order where 
	( oo_duty_price > 0 AND oo_duty_settlement_date = 0 ) OR  
	( oo_express_price >0 AND oo_express_price_settlement_date = 0 )
		group by oo_duty_due_date,oo_express_price_date order by oo_date desc";
$result = wepix_query_error($query);
while($list = wepix_fetch_array($result)){
$_oo_express_price_date = ( $list[oo_express_price_date] > 0 ) ? $list[oo_express_price_date] : "" ; 
*/
sort($_ary_ck_date);
for ($i=0; $i<count($_ary_ck_date); $i++){
	$date_code = $_ary_ck_date[$i];
	$sum = ${'ck'.$date_code.'_duty_price'} + ${'ck'.$date_code.'_express_price'};
	$_ary_sum[] = $sum;
?>
<tr>
	<td class=""><?=${'ck'.$date_code.'_date'}?></td>
	<td class="now-price2">(<?=number_format(${'ck'.$date_code.'_duty_price_count'})?>) <b><?=number_format(${'ck'.$date_code.'_duty_price'})?></b></td>
	<td class="now-price2">(<?=number_format(${'ck'.$date_code.'_express_price_count'})?>) <b><?=number_format(${'ck'.$date_code.'_express_price'})?></b></td>
	<td class="now-price2"><b><?=number_format($sum)?></b></td>
</tr>
<? } ?>
<tr>
	<td class=""></td>
	<td class="now-price2"></td>
	<td class="now-price2"></td>
	<td class="now-price2"><b><?=number_format(array_sum($_ary_sum))?></b></td>
</tr>
		</table>

	</div>
</div>
/<?=$total_count?>/
	</div>
</div>


<div class="save-btn-wrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm m-r-20" onclick="doSubmit()" style="width:90px" > <i class="far fa-check-circle"></i> 저장</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveUpItem2()" style="width:90px" > <i class="fas fa-chevron-circle-up"></i> UP</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveDownItem2()" style="width:90px" > <i class="fas fa-chevron-circle-down"></i> DOWN</button>
</div> 


<script type="text/javascript"> 
<!-- 
function orderSView(idx){
	window.open("/admin2/product2/popup.order_sheet_view.php?idx="+ idx, "orderSView_"+ idx, "width=800,height=500,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

function doSubmit() { 
	$("#form1").submit();
}

function moveUpItem(obj) {     
	alert(obj);
    var idStr = '#' + obj;
    var prevHtml = $(idStr).prev().html();
    if( prevHtml  == null) {
        alert("최상위 리스트입니다!");
        return;
    }
    var prevobj = $(idStr).prev().attr("id");
    var currobj = $(idStr).attr("id");
    var currHtml = $(idStr).html();
    $(idStr).html(prevHtml);//값 변경 
    $(idStr).prev().html(currHtml);
    $(idStr).prev().attr("id","TEMP_TR");//id 값도 변경
    $(idStr).attr("id",prevobj);
    $("#TEMP_TR").attr("id",currobj);
	$("#radio_"+ obj).attr("checked",true);


	chkSelect(obj);
}

 

function moveDownItem(obj) {     
    var idStr = '#' + obj;
    var nextHtml = $(idStr).next().html();
    if( nextHtml  ==  null) {
        alert("최하위 리스트입니다!");
        return;
    }
    var nextobj = $(idStr).next().attr("id");
    var currobj = $(idStr).attr("id");
    var currHtml = $(idStr).html();
    $(idStr).next().html(currHtml);
    $(idStr).html(nextHtml);//값 변경 
    $(idStr).next().attr("id","TEMP_TR");//id 값도 변경
    $(idStr).attr("id",nextobj);
    $("#TEMP_TR").attr("id",currobj);
	$("#radio_"+ obj).attr("checked",true);
	chkSelect(obj);
}

function moveNum() {
	var movenumber = ($('#move_number').val()*1)-1;

	var obj = $('input:radio[name=chk]:checked').val(); 
    var idStr = '#' + obj;

	$(idStr).closest('table').find('tr:eq('+movenumber+')').before($(idStr));
}

function moveTop2() {
	var obj = $('input:radio[name=chk]:checked').val(); 
    var idStr = '#' + obj;
	$(idStr).closest('table').find('tr:first').before($(idStr));
}

function moveBottom2() {
	var obj = $('input:radio[name=chk]:checked').val(); 
    var idStr = '#' + obj;
	$(idStr).closest('table').find('tr:last').after($(idStr));
}



function moveUpItem2() {
	var obj = $('input:radio[name=chk]:checked').val(); 
    var idStr = '#' + obj;
    var sortValue = $('#sort_v_' + obj).val()*1;
    var prevHtml = $(idStr).prev().html();
    if( prevHtml  ==  null) {
        alert("최상위 리스트입니다!");
        return;
    }
    var prevobj = $(idStr).prev().attr("id");
    var currobj = $(idStr).attr("id");
	//alert(prevobj+"/"+currobj);
    var currHtml = $(idStr).html();
    $(idStr).html(prevHtml);//값 변경 
    $(idStr).prev().html(currHtml);
    $(idStr).prev().attr("id","TEMP_TR");//id 값도 변경
    $(idStr).attr("id",prevobj);
    $("#TEMP_TR").attr("id",currobj);
	$("#radio_"+ obj).attr("checked",true);
    $('#sort_v_' + obj).val(sortValue+1);
    $('#sort_v_' + prevobj).val( $('#sort_v_' + currobj).val()*1-1);
}


function moveDownItem2() {   
	var obj = $('input:radio[name=chk]:checked').val(); 
    var idStr = '#' + obj;
    var sortValue = $('#sort_v_' + obj).val()*1;
    var nextHtml = $(idStr).next().html();
    if( nextHtml  ==  null) {
        alert("최하위 리스트입니다!");
        return;
    }
    var nextobj = $(idStr).next().attr("id");
    var currobj = $(idStr).attr("id");
    var currHtml = $(idStr).html();
    $(idStr).next().html(currHtml);
    $(idStr).html(nextHtml);//값 변경 
    $(idStr).next().attr("id","TEMP_TR");//id 값도 변경
    $(idStr).attr("id",nextobj);
    $("#TEMP_TR").attr("id",currobj);
	$("#radio_"+ obj).attr("checked",true);
    $('#sort_v_' + obj).val(sortValue-1);
    $('#sort_v_' + nextobj).val( $('#sort_v_' + currobj).val()*1+1);
}
//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>