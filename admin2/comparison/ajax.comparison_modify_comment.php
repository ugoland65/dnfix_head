<?
	include "../lib/inc_common.php";
	include 'board_inc.php';

	$_idx = securityVal($prd_idx);
	$_pn = securityVal($pn);

	$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));

	$where = " where PD_UID = '".$_idx."' ";
	$total_count = wepix_counter("COMPARISON_COMMENT", $where);

	// 페이지당 목록수
	$list_num = 20;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page = ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
	$counter = $total_count - (($_pn - 1) * $list_num);

	$query = "select * from COMPARISON_COMMENT ".$where." order by COMMENT_IDX desc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$function_value = ",'comment'";
	$_view_paging = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "prdShowPaging", $function_value);

?>
<STYLE TYPE="text/css">
.crm-detail-info{}
.price-min-best-active{ color:#0075ff; }
</STYLE>
<div class="crm-title">
	<h3>코멘트 등록/<?=$pn?></h3>
</div> 
<div class="crm-detail-info">
	<table class="table-style">
		<tr>
			<th class="tds1">모드</th>
			<td class="tds2">
				<div class="btn-group radio-form" data-toggle="buttons">
					<label class="btn btn-default " onclick="bo_mode_ig('off');">
						<input type="radio" name="bo_mode" id="bo_mode2" value="BS" autocomplete="off" > 일반
					</label>
					<label class="btn btn-default " onclick="bo_mode_ig('on');">
						<input type="radio" name="bo_mode" id="bo_mode1" value="IG" autocomplete="off" > 가상(회원)
					</label>
					<label class="btn btn-default active" onclick="bo_mode_ig('on');">
						<input type="radio" name="bo_mode" id="bo_mode4" value="IG2" autocomplete="off" checked> 가상(비회원)
					</label>
				</div>
			</td>
		</tr>
		<tr>
			<th class="tds1">정보</th>
			<td class="tds2">
				<div class="display-table">
					<ul id="board_witer_name_wrap" class="display-table-cell p-r-50 <? if($_show_board_mode == "BS") echo "display-none"; ?>" >
						노출이름 : <input type='text' name='board_witer_name' id='board_witer_name' value="<?=$_view_witer_name?>" style="width:120px;" >
					</ul>
					<ul class="display-table-cell p-r-50">
						작성일 : 
						<input type="text" id="board_date_day" name="board_date_day" value="<?=$_view_bo_date_ymd?>" style="width:80px; cursor:pointer;" class="text-center" />
<? /* ?>
						<select name="board_date_h" style="width:50px;">
<?
	for($i=0; $i<24; $i++){
		if($i == $gva_nowtime_h){ $selected = "selected"; }else{ $selected = ""; }
		if($i < 10){ $show_i = "0".$i; }else{ $show_i = $i; }
		echo "<option value=\"\" ".$selected.">".$show_i."</option>";
	}
?>
						</select>
<? */ ?>
						<input type='text' name='board_date_h' id='board_date_h' style="width:30px;" value="<?=$_view_bo_date_hh?>" class="text-center"> :
						<input type='text' name='board_date_s' id='board_date_s' style="width:30px;" value="<?=$_view_bo_date_ii?>" class="text-center">
						<label><input type="checkbox" name="board_date_modify" value="Y"> 작성일 지정</label>
					</ul>
					<ul class="display-table-cell p-r-50">
						노출 IP : 
						<input type='text' name='board_ip_show' id='board_ip_show' style="width:200px;"  value="<?=$_view_board_ip_show?>" >
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="tds1">내용</th>
			<td class="tds2">
				<textarea name='comm_body' id='comm_body' style='height:50px;' ></textarea>
			</td>
		</tr>
	</table>
</div> 
<div class="text-center m-t-10">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-submit" onclick="commentSubmit();" > 
		<i class="far fa-check-circle"></i> 등록
	</button>
</div>

<div class="crm-title">
	<h3>코멘트</h3>
	<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
</div> 
<div class="crm-detail-info">
	
	<table class="table-list" >
		<tr>
			<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
			<th class="tl-idx">고유번호</th>
			<th class="tl-bo-mode">모드</th>
			<th class="tl-body">내용</th>
<?
if( $cf_all_glob_sys_domain_by_skin_active == "active" ){
?>
			<th class="tl-domain">도메인</th>
<? } ?>
			<th class="tl-witer">작성자</th>
			<th class="tl-date">작성일</th>
		</tr>
<?
while($list = wepix_fetch_array($result)){

	$_view2_mode = $_bo_gv_mode[$list[COMMENT_MODE]];
	$comment_body = nl2br($list[COMMENT_BODY]);
	$_view_micon = "";
	$_view_board_name = $list[COMMENT_NAME];
	$_view_ip = $list[COMMENT_IP];
	$_view_ip_show = $list[COMMENT_IP_SHOW];

	$_view_domain = $list[DOMAIN];

	$board_date = date("Y-m-d H:i", $list[COMMENT_DATE]);
?>
<tr align="center" id="trid_<?=$list[COMMENT_IDX]?>" bgcolor="<?=$trcolor?>">
	<td class="tl-check"><input type="checkbox" name="key_check[]"  id="bo_idx_<?=$bo_list[UID]?>" class="checkSelect" value="<?=$list[COMMENT_IDX]?>"></td>
	<td class="tl-idx"><?=$list[COMMENT_IDX]?></td>
	<td class="tl-bo-mode"><?=$_view2_mode?></td>
	<td class="tl-body text-left">
		<?=$comment_body?>
	</td>
<?
if( $cf_all_glob_sys_domain_by_skin_active == "active" ){
?>
	<td class="tl-domain"><?=$_view_domain?></td>
<? } ?>
	<td class="tl-witer">
		<?=$_view_micon?><?=$_view_board_name?><br>
		<?=$bo_list[BOARD_WITER_ID]?>
		<?=$_view_ip?>
		<b><?=$_view_ip_show?></b>
	</td>
	<td class="tl-date"><?=$board_date?></td>
</tr>
		<? } ?>
	</table>

</div>
<div class="paging-wrap"><?=$_view_paging?></div>	
<script type="text/javascript"> 
<!-- 
function commentSubmit(mode, key){
		
	var commMode = $(':input:radio[name=bo_mode]:checked').val();
	var commName = $("#board_witer_name").val();
	var comm_body = $("#comm_body").val();
	var comm_ip_show = $("#board_ip_show").val();

	if( commName == "" ){
		alert('노출이름을 입력해주세요.');
		$('#board_witer_name').focus();
		return false;
	}

	$.ajax({
		url: "<?=_A_PATH_COMPARISON_OK?>",
		data: {
			"a_mode":"commWrite",
			"ajax_mode":"on",
			"pd_key":"<?=$_idx?>",
			"comm_mode":commMode,
			"comm_name":commName,
			"comm_body":comm_body,
			"comm_ip_show":comm_ip_show
		},
		type: "POST",
		dataType: "text",
		success: function(getdata){
			if (getdata){
				redatawa = getdata.split('|');
				ckcode = redatawa[1];
				ckmsg = redatawa[2];
				if(ckcode == "Processing_Complete"){
					prdShowPaging(1, "comment");
					$('#comm_body').val("");
				}else if(ckcode == "Erorr"){
					$('#modal_alert_msg').html(ckmsg);
					$('#modal-alert').modal({show: true,backdrop:'static'});
				}else{
					//return false;
				}
			}
		},
		error: function(){
			$('#modal_alert_msg').html('에러');
			$('#modal-alert').modal({show: true,backdrop:'static'});
		}
	});

}
//--> 
</script> 