<?
$pageGroup = "comparison";
$pageName = "comparison_sort";

include "../lib/inc_common.php";

	$_brand_idx = securityVal($brand_idx);
	$_mode = securityVal($mode);

	if( $_brand_idx ){
		$where = " where CD_BRAND_IDX = '".$_brand_idx."' ";
		$order_by = "CD_BRAND_RANK ASC";

	}else{
		if( $_mode ){
			$where = " where CD_KIND_CODE = '".$_mode."' ";
			$order_by = "CD_SORT ASC";
		}

	}
	
	if( $where ){
		$cd_query = "select * from "._DB_COMPARISON." ".$where." order by ".$order_by;
		$cd_result = wepix_query_error($cd_query);
	}

include "../layout/header.php";
?>

<STYLE TYPE="text/css">
.table-style{ width:100% !important; }
.btn-wrap-center{ margin:10px 0 5px; text-align:center; }
.btn-wrap{ margin:15px 0 5px; text-align:right; }
.save-btn-wrap{ z-index:300; padding:10px 10px; position:fixed; bottom:30px; right:30px; background-color:rgba(0,0,0,0.4); border:1px solid #000000; text-align:center; vertical-align:middle; }
.save-btn-wrap button{ }
</STYLE>

<div id="contents_head">
	<h1>상품 진열관리</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		
		<div class="list-top-btn-wrap">
			<ul class="list-top-btn">

				<select name="s_brand" onchange="moveBrand(this.value)">
					<option value="">전체 브랜드</option>
<?

	$brand_result = wepix_query_error("select BD_IDX,BD_NAME from "._DB_BRAND." order by BD_NAME asc ");
	while($brand_list = wepix_fetch_array($brand_result)){
?>
							<option value="<?=$brand_list[BD_IDX]?>" <? if( $_brand_idx == $brand_list[BD_IDX] ) echo "selected";?> ><?=$brand_list[BD_NAME]?></option>
<? } ?>
				</select>

				<button type="button" id="" class="btnstyle1 btnstyle1-sm" style="margin-left:15px;width:120px;" onclick="goSortList('ONAHOLE')">ONAHOLE</button>
				<button type="button" id="" class="btnstyle1 btnstyle1-sm" style="margin-left:5px;width:80px;" onclick="goSortList('REALDOLL')">Real Doll</button>
			</ul>
		</div>

		<table cellspacing="0" cellpadding="0" border="0" class="table-style2 treewrap">	
			<tr>
				
				<td class="treewrap-margin" style="border:none;"></td>
				<td class="treewrap-body">

					<form name='form1' id='form1' action='<?=_A_PATH_COMPARISON_OK?>' method='post'>
					<input type='hidden' name='a_mode' value='comparisonSort'>
					<input type='hidden' name='sort_mode' value='<?=$_mode?>'>
					<input type='hidden' name='brand_idx' value='<?=$_brand_idx?>'>

						<div id="main_show_pd_list">
							<div class="btn-wrap">
								<div class="save-btn-wrap">
									<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm m-r-20" onclick="doSubmit()" style="width:90px" > <i class="far fa-check-circle"></i> 저장</button>
									
									<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveTop2()" style="width:90px" > <i class="fas fa-chevron-circle-up"></i> 맨위</button>
									<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveUpItem2()" style="width:90px" > <i class="fas fa-chevron-circle-up"></i> UP</button>
									<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveDownItem2()" style="width:90px" > <i class="fas fa-chevron-circle-down"></i> DOWN</button>
									<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm m-r-20" onclick="moveBottom2()" style="width:90px" > <i class="fas fa-chevron-circle-down"></i> 맨아래</button>
									
									<input type="text" name="" id="move_number" style="width:50px;">
									<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveNum()" style="width:90px" >번으로 이동</button>
								</div> 
							</div>
					
<table cellspacing="1px" cellpadding="0" border="0" class="table-style" id='mps_pd_table'>	
<?
if( $cd_result ){
while($list = wepix_fetch_array($cd_result)){ 

	if( $_brand_idx ){
		$_rank_num =  $list[CD_BRAND_RANK];
	}else{
		$_rank_num = $list[CD_SORT];
	}

	$img_path = '../../data/comparion/'.$list[CD_IMG];
?>
	<tr align="center" id="<?=$_rank_num?>">
		<td width="25px">
			<input type='hidden' name='cd_idx[]' value='<?=$list[CD_IDX]?>'>
			<input type="radio" name="chk" id="radio_<?=$_rank_num?>" value="<?=$_rank_num?>" onclick="chkSelect(this)" />
		</td>
		<td width="50px"><?=$_rank_num?></td>
		<td width="50px" align="center"><?= number_format($list[CD_IDX])?> </td>
		<td width="80px"><? if( $list[CD_IMG]){?><img src="<?=$img_path?>" style="height:70px;"><? } ?></td>
		<td align="left">
			<div>
				<ul style="font-size:11px; margin-bottom:5px;"><?=$list[CD_RELEASE_DATE]?></ul>
				<ul><a href="<?=_A_PATH_COMPARISON_REG?>?mode=modify&key=<?=$list[CD_IDX]?>&return_query_string_list=<?=$check_query_string_urlencode?>" target="_blank"><b><?=$list[CD_NAME]?></b></a></ul>
				<? if($list[CD_NAME_OG]){ ?><ul style="margin-top:5px;"><?=$list[CD_NAME_OG]?></ul><? } ?>
				<? if($list[CD_MEMO]){ ?><ul style="margin-top:5px;"><span style='color:red;'><?=nl2br($list[CD_MEMO])?></ul></span><? } ?>
			</div>
		</td>
		<td width="100px" ><?= number_format($list[CD_REVIEW])?> </td>
		<td width="90px">
			<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveUpItem('<?=$_rank_num?>')" style="width:40px" > 
				<i class="fas fa-chevron-circle-up"></i> 
			</button>
			<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveDownItem('<?=$_rank_num?>')" style="width:40px" > 
				<i class="fas fa-chevron-circle-down"></i>
			</button>
		</td>

	</tr>
<? } } ?>
</table>


					</div>
					
					</form>

					<div class="btn-wrap-center">

<? if( $_view2_msp_idx ) {?>
						<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="doSubmit();" > 
							<i class="far fa-trash-alt"></i>
							삭제하기
						</button>
<? } ?>
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doSubmit();" > 
							<i class="far fa-check-circle"></i>
							저장하기
						</button>

					</div>

				</td>
			</tr>
		</table>

	</div>
</div>


<script type="text/javascript"> 

function moveBrand(idx) { 
	location.href='comparison_sort.php?brand_idx='+idx;
}

	function doSubmit() { 
		$("#form1").submit();
	}

function chkSelect(obj){
/*
	var lineTr = obj.parentNode.parentNode;
	lineTr.style.backgroundColor = (obj.checked) ? "#eeeeee" : "#fff";
*/
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
    var prevHtml = $(idStr).prev().html();
    if( prevHtml  ==  null) {
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
}


function moveDownItem2() {   
	var obj = $('input:radio[name=chk]:checked').val(); 
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
}

function pdSelctFinal(){
	var finalPdKeyArray = finalPdKeyCheck.join("/");

	if(mpsPd != ""){
		mpsPd += "/"+finalPdKeyArray;
	}else{
		mpsPd += finalPdKeyArray;
	}

	//alert(mpsPd);
	closedPopup();
	showMpsPdList();
	//alert(finalPdKeyArray);
}

function goSortList(mode){
	location.href='<?=_A_PATH_COMPARISON_SORT?>?mode='+mode;
}
</script>

 

 



<?
include "../layout/footer.php";
exit;
?>