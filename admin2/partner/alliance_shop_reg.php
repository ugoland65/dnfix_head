<?
include "../lib/inc_common.php";
	$pageGroup = "partner";
	$pageName = "partner_shop_reg";

	$_mode = securityVal($mode);
	$_as_idx = securityVal($key);

	if( $_mode == "modify" ){
		$alliance_data = wepix_fetch_array(wepix_query_error("select * from "._DB_ALLIANCE_SHOP." where AS_IDX = '".$_as_idx."' "));

		 
		$result = wepix_query_error("select * from "._DB_ALLIANCE_SHOP_CALCULATE." where ASC_AS_IDX = '".$_as_idx."' ");



		$page_title_text = "제휴샵 수정";
		$submit_btn_text = "제휴샵 수정";
	}else{
		$page_title_text = "제휴샵 등록";
		$submit_btn_text = "제휴샵 등록";
	}


include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
</STYLE>
<script language=javascript>


	function doAllianceSubmit(){
		var form = document.form1;
		form.submit();
	};


	function cuDel(idx){
		var form = document.form1;
		form.action_mode.value = "Del";
		form.submit();
	}

	
function cuModify(idx){
	
	var form = document.form1;
	form.action_mode.value = "modify";
	form.action ="partner_ok.php?key="+idx;
	form.submit();

}

var rowcount_room = 1;
var cuPlus=function(){
	rowcount_room++;
	var showHtml = ""
		+"<tr id='trid2_"+ rowcount_room +"' style='text-align:center;'>"
		+"<td><input type='text' name='as_money[]' ></td>"
		+"<td><input type='text' name='as_cu[]' class='inputtext1'></td>"
		+"<td><input type=\"button\" \ onClick='cuDel("+ rowcount_room +")' value='삭제'></td>"
		+"</tr>";

	$("#cuPt").append(showHtml);
};

var cuDel = function(key){
	$('#trid2_'+key).remove();
};


</script>
<div id="contents_head">
	<h1>제휴샵 등록</h1>
    <div id="head_write_btn">

	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
	 <div class="table-wrap">
	<form name='form1' action='partner_ok.php' method='post'>
			<? if( $_mode == "modify" ){ ?>
				<input type="hidden" name="a_mode" value="allianceModify">
				<input type="hidden" name="as_idx" value="<?=$alliance_data[AS_IDX]?>">
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="allianceNew">
			<? } ?>


				<table cellspacing="1" cellpadding="0" class="table-style">

			     <tr id='company' style="<?=$dispaly2?>">
					 <th class="tds1">제휴사 이름</th>
					 <td class="tds2"><input type='text' name='alliance_shop_name' id='alliance_shop_name' size='40' value="<?=$alliance_data[AS_NAME]?>" ></td>
				 </tr>
				<tr>
					 <th class="tds1">샵 정산율</th>
					 <td class="tds2"><input type='text' name='alliance_shop_calculate' size='4' value="<?=$alliance_data[AS_CALCULATE]?>"> %</td>
			    </tr>
				<tr>
					 <th class="tds1">샵 COODE</th>
					 <td class="tds2"><input type='text' name='alliance_shop_code' size='4' value="<?=$alliance_data[AS_VALUE]?>"></td>
			    </tr>
				<tr>
					 <th class="tds1">커미션</th>
					 <td class="tds2">
					 <div class="plusBtnWrap"><input type="button" value="커미션 추가 등록" class="plusBtn" onclick="cuPlus()"></div>
						 <table cellspacing="1" cellpadding="0" class="table-style" id="cuPt">
						<? while($cu_data =  wepix_fetch_array($result)){
							 $i++;
							 ?>
						   <input type='hidden' name='rmda[]' value='<?=$cu_data[ASC_IDX]?>'>
						 <tr>
							<th class="tds1">금액</th>
							<td class="tds2"><input type='text' name='as_money<?=$cu_data[ASC_IDX]?>'  value='<?=$cu_data[ASC_CRITERIA_MONEY]?>'>
							<th class="tds1">퍼센트 </th>
							<td class="tds2"><input type='text' name='as_cu_<?=$cu_data[ASC_IDX]?>'  value='<?=$cu_data[ASC_CRITERIA_CALCULATE]?>'>
							<input type='button' value='수정' onclick="javascript:cuModify('<?=$cu_data[ASC_IDX]?>');">
							<input type='button' value='삭제' onclick="javascript:cuDel('<?=$cu_data[ASC_IDX]?>');"></td>
						 </tr>
						<? } //while end?>
					</table>				 
					 </td>
				 </tr>
				 <tr>
					 <th class="tds1">메모</th>
					 <td class="tds2"><textarea name='alliance_shop_memo' cols='40' rows='6'><?=$alliance_data[AS_MEMO]?></textarea></td>
				 </tr>
				 <tr>
				   <th class="tds1">노출</th>
				   <td class="tds2">
					<input type="radio" name="alliance_shop_view"  value="Y" <? if( $alliance_data[AS_VIEW]=="Y" OR !$alliance_data[AS_VIEW] ) echo "checked"; ?> >노출
					<input type="radio" name="alliance_shop_view"  value="N" <? if( $alliance_data[AS_VIEW]=="N") echo "checked"; ?>>비노출
				   </td>
				 </tr>
				</table>
				
			</form>
			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_PARTNER_ALLIANCE_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doAllianceSubmit();" > 
						<i class="far fa-check-circle"></i>
						<?=$submit_btn_text?>
					</button>
				</ul>
			</div>
		</div>
	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>