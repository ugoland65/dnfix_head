<?
$pageGroup = "comparison";
$pageName = "ranking_req";

include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_idx = securityVal($key);

	if( $_mode == "modify" ){
		$rank_data = wepix_fetch_array(wepix_query_error("select * from prd_ranking where rank_idx = '".$_idx."' "));

		$_ranking_subject = $rank_data[rank_subject];
		$_ary_rank_prd = explode("|", $rank_data[rank_prd]);
		$_ary_rank_change = explode("|", $rank_data[rank_change]);
		

	}else{
	
	}

include "../layout/header.php";
?>

<script type="text/javascript"> 
<!-- 

var prd_mode = "";
var prd_brand = "";
var prd_keyword = "";
var mode = "<?= $_mode ?>";

var prd_ranking_list = new Array();
var prd_ranking_list_count = 0;
if(mode  == "modify" ){
	prd_ranking_list_count = "<?=count($_ary_rank_prd)?>";
}
//--> 
</script> 

<div id="contents_head">
	<h1>랭킹 만들기</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div id="" class="list-box-layout3 display-table">
			<ul class="display-table-cell width-50p v-align-top">
				
				<div class="list-button-wrap m-b-5">
					<ul>
						<select name="" onchange="prd_select(this.value,'prd_mode')">
							<option value="all" selected>전체 상품</option>
<?
for($t=0; $t<count($koedge_prd_kind_array); $t++){
?>
							<option value="<?=$koedge_prd_kind_array[$t]['code']?>">(<?=$koedge_prd_kind_array[$t]['code']?>) <?=$koedge_prd_kind_array[$t]['name']?></option>
<?
}
?>
						</select>
					</ul>
					<ul>
						<select name="s_brand" onchange="prd_select(this.value,'prd_brand')">
							<option value="">전체 브랜드</option>
<?
	$brand_result = wepix_query_error("select BD_IDX,BD_NAME from "._DB_BRAND." order by BD_NAME asc ");
	while($brand_list = wepix_fetch_array($brand_result)){
?>
							<option value="<?=$brand_list[BD_IDX]?>" <? if( $_s_brand == $brand_list[BD_IDX] ) echo "selected";?> ><?=$brand_list[BD_NAME]?></option>
<? } ?>
						</select>
					</ul>
					<ul><input type="text" id="keyword" name="keyword" style="width:150px; " /></ul><!-- onKeyUp="prd_select(this.value,'prd_keyword')" -->
					<ul><button type="button" id="bklist_open" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="prdList()"><i class="fas fa-search"></i> 상품검색</button></ul>
				</div>

				<div class="list-box-layout3-wrap" id="stock_prd_list">

				</div>

			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell v-align-top">

				<div class="list-button-wrap m-b-5"><!--_stock_day-->
					<b>랭킹제목</b> : <input type="text" id="ranking_subject" name="ranking_subject" value="<?=$_ranking_subject?>" style="width:600px;" />
				</div>
				<div class="list-box-layout3-wrap">
					<form name='form1' id='ranking_form' action='processing.ranking.php' method='post' enctype="multipart/form-data" autocomplete="off">
					<?if( $_mode == "modify" ){?>
						<input type="hidden" name="a_mode" value="modify_ranking">
					<?}else{?>
						<input type="hidden" name="a_mode" value="new_ranking">
					<?}?>
					<input type="hidden" name="rank_key" value="<?=$key?>">
					<input type="hidden" name="rank_subject" id="rank_subject">
					<table class="table-list" id="stock_prd_cart">
<?
if( $_mode == "modify" ){

for ($i=0; $i<count($_ary_rank_prd); $i++){
	$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_ary_rank_prd[$i]."' "));
	$img_path = '../../data/comparion/'.$comparison_data[CD_IMG];
	$brand_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$comparison_data[CD_BRAND_IDX]."' "));
	$_view_brand_name = $brand_data[BD_NAME];
	$_rank_num = $i+1;
	
?>
	<tr id="<?=$_rank_num?>"> 
		<td><?=$_rank_num?></td> 
		<td><?=$_ary_rank_prd[$i]?></td> 
		<td><img src="<?=$img_path?>" style="height:70px;"></td> 
		<td><?=$_view_brand_name?></td> 
		<td><?=$comparison_data[CD_NAME]?></td> 
		<td> 
			<input type='hidden' name='ranking_key[]' value='<?=$_ary_rank_prd[$i]?>'> 
			<input type='text' name='ranking_change[]' value="<?=$_ary_rank_change[$i]?>"> 
		</td> 
		<td> 
			<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveUpItem('<?=$_rank_num?>')" style="width:40px" ><i class="fas fa-chevron-circle-up"></i></button> 
			<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveDownItem('<?=$_rank_num?>')" style="width:40px" ><i class="fas fa-chevron-circle-down"></i></button> 
		</td> 
		<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="cartLineDel(this,'<?=$_ary_rank_prd[$i]?>')">삭제</button></td> 
	</tr> 
<? } //for END 
} ?>

					</table>
					</form>
				</div>

			</ul>
		</div>

		<div class="list-bottom-btn-wrap">
			<ul class="list-top-total">
				<span class="count"></span>
			</ul>
			<ul class="list-top-btn">
				<button type="button" id="bklist_open" class="btnstyle1 btnstyle1-primary btnstyle1-md" onclick="rankMake()">랭킹 만들기</button>
			</ul>
		</div>

	</div>
</div>

<script type="text/javascript"> 
<!-- 
function prdList(){
	
	prd_keyword = $("#keyword").val();

	$.ajax({
		type: "post",
		data: {
			"prd_mode":prd_mode,
			"prd_brand":prd_brand,
			"prd_keyword":prd_keyword
		},
		url : "ajax_prd_list.php",
		success: function(shtml) {
			$('#stock_prd_list').html(shtml);
		}
	});

}

function prd_select(value, mode){
	if(mode=="prd_mode"){
		prd_mode = value;
	}else if(mode=="prd_brand"){
		prd_brand = value;
	}else if(mode=="prd_keyword"){
		prd_keyword = value;
	}
	prdList();
}

function prdRankingCartArray( prd_idx ) {
	this.prd_idx = prd_idx;
}

function prdRankingCartHtml(idx){

	var prdImg = $("#ps_prd_img_"+idx).html();
	var brandName = $("#ps_brand_name_"+idx).html();
	var prdName = $("#ps_prd_name_"+idx).html();

	var html;
		html += "<tr id=\""+ prd_ranking_list_count +"\">";
 		html += "<td>"+ prd_ranking_list_count +"</td>";
 		html += "<td>"+ idx +"</td>";
		html += "<td>"+ prdImg +"</td>";
		html += "<td>"+ brandName +"</td>";
 		html += "<td>"+ prdName +"</td>";
 		html += "<td>";
 		html += "<input type='hidden' name='ranking_key[]' value='"+ idx +"'>";
 		html += "<input type='text' name='ranking_change[]'>";
 		html += "</td>";
 		html += "<td>";
 		html += "<button type=\"button\" class=\"btnstyle1 btnstyle1-info btnstyle1-sm\" onclick=\"moveUpItem('"+ prd_ranking_list_count +"')\" style=\"width:40px\" ><i class=\"fas fa-chevron-circle-up\"></i></button>";
 		html += "<button type=\"button\" class=\"btnstyle1 btnstyle1-info btnstyle1-sm\" onclick=\"moveDownItem('"+ prd_ranking_list_count +"')\" style=\"width:40px\" ><i class=\"fas fa-chevron-circle-down\"></i></button>";
 		html += "</td>";
 		html += "<td><button type=\"button\" id=\"show_type_all\" class=\"btnstyle1 btnstyle1-danger btnstyle1-sm\" onclick=\"cartLineDel(this,'"+ idx +"')\">삭제</button></td>";
 		html += "</tr>";

 		$("#stock_prd_cart").append(html);

}


function prdRankingCart(idx){
	var i = 0;
	var prd = null;
	var ck = 0;
	//var test = "";

	for(i=0; i<prd_ranking_list.length; i++){
		prd = prd_ranking_list.shift();
		if(prd.prd_idx==idx){
			ck++;
		}else{
		}
		prd_ranking_list.push(prd);
		//test += "/"+prd.prd_idx;
	}
	if(ck ==0){
		prd = new prdRankingCartArray( idx );
		prd_ranking_list.push(prd);
		prd_ranking_list_count++;
		prdRankingCartHtml( idx );
	}
	//alert(test);
}


function prdRankingCartDel(idx){
	var i = 0;
	var prd = null;
	for(i=0; i<prd_ranking_list.length; i++){
		prd = prd_ranking_list.shift();
		if(prd.prd_idx!=idx){
			prd_ranking_list.push(prd);
		}else{
		}
	}
}


function moveUpItem(obj) {     
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


function cartLineDel(obj, idx){
	$(obj).parent().parent().remove();
	prdRankingCartDel(idx);
}

function rankMake(mode){

	var form = document.ranking_form;
	if( $("#ranking_subject").val() == "" ){
		alert("랭킹제목은 필수");
		return;
	}
	$("#rank_subject").val($("#ranking_subject").val());
	$("#ranking_form").submit();
}
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>