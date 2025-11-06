<?
$pageGroup = "product2";
$pageName = "brand_group_list";

include "../lib/inc_common.php";

$_mps_code = securityVal($mps_code);

	$msp_where = "  ";
	$msp_query = "select * from "._DB_MAIN_PRODUCT_SHOW." ".$msp_where."order by MPS_IDX desc ";
	$msp_result = wepix_query_error($msp_query);

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-style{ width:100% !important; }
.btn-wrap-center{ margin:10px 0 5px; text-align:center; }
.btn-wrap{ margin:15px 0 5px; text-align:right; }

</STYLE>
<div id="contents_head">
	<h1>브랜드 그룹 진열관리</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<table cellspacing="0" cellpadding="0" border="0" class="table-style2 treewrap">	 
			<tr>
				<td class="treewrap-menu">

					<div class="tree-left-wrap">

						<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin2/product2/brand_group_list.php'" style="width:100%; height:28px !important;" > 
							<i class="fas fa-plus-circle"></i> 신규 진열 추가
						</button>

						<?
						while($msp_list = wepix_fetch_array($msp_result)){

							$_inst2_ul_class = ($msp_list[MPS_CODE] == $_mps_code) ? "tree-big-menu2 active" : "tree-big-menu2-closed";

							$_show2_msp_idx[$msp_list[MPS_CODE]] = $msp_list[MPS_IDX];
							$_show2_msp_title[$msp_list[MPS_CODE]] = $msp_list[MPS_TITLE];
							$_show2_mps_array[$msp_list[MPS_CODE]] = $msp_list[MPS_ARRAY];
							$_show2_msp_mode[$msp_list[MPS_CODE]] = $msp_list[MPS_MODE];
							$_show2_msp_board_code[$msp_list[MPS_CODE]] = $msp_list[MPS_BOARD_CODE];
						?>
							<ul id="cate_<?=$msp_list[MPS_IDX]?>" class="<?=$_inst2_ul_class?>" onclick="location.href='/admin2/product2/brand_group_list.php?mps_code=<?=$msp_list[MPS_CODE]?>'"><?=$msp_list[MPS_TITLE]?></ul>
						<? } ?>
					</div>
				</td>
				<td class="treewrap-margin" style="border:none;"></td>
				<td class="treewrap-body">

					<form name='form1' id='form1' action='<?=_A_PATH_PRODUCT_OK?>' method='post'>
<?
if( !$_mps_code ){
?>
<script type="text/javascript"> 
<!-- 
var mpsPd = "";
//--> 
</script>
					<input type='hidden' name='a_mode' value='main_show_new'>

					<div class="ajax-page-title"> 브랜드 진열 추가</div>
					<div class="table-wrap">
						<table cellspacing="1" cellpadding="0" class="table-style2 basic-form">
							<tr>
								<th>진열 명칭</th>
								<td><input type='text' name='mps_title' id='mps_title' ></td>
							</tr>
							<!--
							<tr>
								<th>진열 모드</th>
								<td>
									<select name='mps_mode' id='mps_mode'>
										<option value='BRAND'>BRAND</option>
									</select>
								</td>
							</tr>
							-->
							<?
									$bo_c_query = "select BAC_NAME, BOARD_CODE from "._DB_BOARD_A_CONFIG."  order by UID desc ";
									$bo_c_result = wepix_query_error($bo_c_query);
							?>
							<!--
							<tr>
								<th>진열 코드</th>
								<td><input type='text' name='mps_code' id='mps_code' ></td>
							</tr>
							-->
							<tr>
								<th>진열 상품</th>
								<td>
									<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="pdChoice();" > <i class="fas fa-plus-circle"></i> 진열 브랜드 추가</button>
								</td>
							</tr>
						</table>
					</div>

<?
}elseif( $_mps_code ){

	$_view2_msp_idx = $_show2_msp_idx[$_mps_code];
	$_view2_msp_title = $_show2_msp_title[$_mps_code];
	$_view2_mps_code = $_mps_code;
	$_view2_mps_mode = $_show2_msp_mode[$_mps_code];
	$_view2_mps_board_code = $_show2_msp_board_code[$_mps_code];

	$_show2_mps_pd = $_show2_mps_array[$_mps_code];
	$_ary2_mps_pd= explode("/", $_show2_mps_array[$_mps_code]);
	$_view2_mps_pd_count = count($_ary2_mps_pd);
?>
<script type="text/javascript"> 
<!-- 
var mpsPd = "<?=$_show2_mps_pd?>";
var board_code = "<?= $_view2_mps_board_code ?>";
var mode = "<?= $_view2_mps_mode ?>";

//--> 
</script>
					<input type='hidden' name='a_mode' value='main_show_modify'>
					<input type='hidden' name='modify_key' value='<?=$_view2_msp_idx?>'>
					<input type='hidden' name='mps_code' value='<?=$_view2_mps_code?>'>

					<div class="ajax-page-title">상품 진열 관리/<?=_A_PATH_PRODUCT_MAIN_SHOW_PD_LIST?>/</div>
					<div class="table-wrap">
						<table cellspacing="1" cellpadding="0" class="table-style2 basic-form">
							<tr>
								<th>진열 명칭</th>
								<td><input type='text' name='mps_title' id='mps_title' value="<?=$_view2_msp_title?>"></td>
							</tr>
							<!--
							<tr>
								<th>진열 모드</th>
								<td><b><?=$_view2_mps_mode?></b><!-- <input type='text' name='area_name' id='area_name' style="width:200px" value="<?=$_view2_mps_code?>">  --></td>
							<!--</tr>
							-->
							<tr>
								<th>진열 코드</th>
								<td><b><?=$_view2_mps_code?></b><!-- <input type='text' name='area_name' id='area_name' style="width:200px" value="<?=$_view2_mps_code?>">  --></td>
							</tr>
							<!--
							<tr>
								<th>게시판 코드</th>
								<td><b><?=$_view2_mps_board_code?></b>
							</tr>
							-->

							<tr>
								<th>진열 상품수</th>
								<td>
									<b><?=$_view2_mps_pd_count?></b>개 
									<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="pdChoice();" > <i class="fas fa-plus-circle"></i> 진열 상품 추가</button>
								</td>
							</tr>
						</table>

<!-- 
						<table cellspacing="1px" cellpadding="0" border="0" class="table-style" >	
							<tr>
								<th width="25px"></th>
								<th width="80px">노출순서</th>
								<th width="80px">고유번호</th>
								<th width="170px">카테고리</th>
								<th>상품명</th>
								<th width="100px">판매가</th>
								<th width="100px">원가</th>
								<th width="170px"></th>
							</tr>
						</table>
 -->

<? } ?>
					</div>
<!-- 
					<div class="btn-wrap-center">
						<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-lg" onclick="pdChoice();" > 
							<i class="fas fa-plus-circle"></i>
							진열 상품 추가
						</button>
					</div>
 -->
					<div id="main_show_pd_list">
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

	

	function doSubmit() { 
		$("#form1").submit();
	}

	function showMpsPdList(str) { 
		var board_code = "<?= $_view2_mps_board_code ?>";
		var mode = "<?= $_view2_mps_mode ?>";

		if(!board_code){
			var board_code = $("#mps_board_code option:selected").val();
		}
		if(!mode){
			var mode = $("#mps_mode option:selected").val();
		}
		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PRODUCT_MAIN_SHOW_PD_LIST?>",
			data : { mps_pd : mpsPd , board_code : board_code , mode : mode },
			success: function(getdata) {
				$("#main_show_pd_list").html(getdata);
			}
		});
	}

<? if( $_view2_mps_pd_count > 0 ) { ?>
	showMpsPdList('<?=$_show2_mps_pd?>');
<? } ?>

	function pdChoice(board_code,mode) { 
		var board_code = "<?= $_view2_mps_board_code ?>";
		var mode = "<?= $_view2_mps_mode ?>";
		if(!board_code){
			var board_code = $("#mps_board_code option:selected").val();
		}
		if(!mode){
			var mode = $("#mps_mode option:selected").val();
		}
		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PRODUCT_CHOICE?>",
			data : { board_code : board_code , mode : mode
						
/*
				a_mode : "area_del",
				Idx : idx
*/
			},
			success: function(getdata) {
				$("#popup_layer_body").html(getdata);
				showPopup('1000', '600', 'ajax');
			}
		});
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
</script>

 

 



<?
include "../layout/footer.php";
exit;
?>