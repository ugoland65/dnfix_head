<?
$pageGroup = "partner";
$pageName = "agency_list";

include "../lib/inc_common.php";

	$agency_where = " where AG_DEL_YN='N' and AG_KIND='A' ";

	$total_count = wepix_counter(_DB_AGENCY, $agency_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$agency_query = "select * from "._DB_AGENCY." ".$agency_where." order by AG_IDX desc limit ".$from_record.", ".$list_num;
	$agency_result = wepix_query_error($agency_query);

	$page_link_text = _A_PATH_PARTNER_AG_LIST."?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.ag-kind{ width:50px !important; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }
</STYLE>
<div id="contents_head">
	<h1>에이전시 목록</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_PARTNER_AG_REG?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div class="table-wrap">
			<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
				<tr>
					<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
					<th class="list-idx">고유번호</th>
					<th class="ag-kind">분류</th>
					<th class="ag-name">회사명</th>
					<th class="list-active">노출</th>
					<th class="ag-sub-count">지사</th>
					<th class="ag-sub-view">지사보기</th>
					<th class="ag-btn">관리</th>
				</tr>
<?
while($agency_list = wepix_fetch_array($agency_result)){
	$trcolor = "#fff";

	if( $agency_list[AG_VIEW] == "N" ){
		$trcolor = "#eee";
	}else{
	}

	$sub_count = wepix_counter(_DB_AGENCY, " where AG_KIND='B' and AG_CO_IDX='".$agency_list[AG_IDX]."' and AG_DEL_YN='N' ");
	
?>
<tr align="center" id="trid_<?=$agency_list[AG_IDX]?>" bgcolor="<?=$trcolor?>">
	<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$agency_list[AG_IDX]?>" ></td>
	<td class="list-idx"><?= $agency_list[AG_IDX]?></td>
	<td class="ag-kind">본사</td>
	<td class="ag-name" style="text-align:left !important;"><B><?=$agency_list[AG_COMPANY]?><B/></td>
	<td class="list-active">
		<? if( $agency_list[AG_VIEW]=="Y" ){ ?>
			<button type="button" id="active_btn_n_<?= $agency_list[AG_IDX]?>" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" style="width:100% !important;" onclick="agencyActive('N', '<?=$agency_list[AG_IDX]?>');">노출</button>
			<button type="button" id="active_btn_y_<?= $agency_list[AG_IDX]?>" class="btnstyle1 btnstyle1-primary btnstyle1-xs" style="width:100% !important; display:none;" onclick="agencyActive('Y', '<?=$agency_list[AG_IDX]?>');">비노출</button>
		<? }else{ ?>
			<button type="button" id="active_btn_n_<?= $agency_list[AG_IDX]?>" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" style="width:100% !important; display:none;" onclick="agencyActive('N', '<?=$agency_list[AG_IDX]?>');">노출</button>
			<button type="button" id="active_btn_y_<?= $agency_list[AG_IDX]?>" class="btnstyle1 btnstyle1-primary btnstyle1-xs" style="width:100% !important;" onclick="agencyActive('Y', '<?=$agency_list[AG_IDX]?>');">비노출</button>
		<? } ?>
	</td>
	<td class="ag-sub-count"><? if( $sub_count > 0 ){ echo $sub_count; }?></td>
	<td class="ag-sub-view">
		<? if( $sub_count > 0 ){ ?>
		<button type="button" id="branch_view_btn_show_<?=$agency_list[AG_IDX]?>" class="btnstyle1 btnstyle1-info btnstyle1-xs" style="width:100% !important;" onclick="branchView('show', '<?=$agency_list[AG_IDX]?>');"><i class="fas fa-caret-down"></i></button>
		<button type="button" id="branch_view_btn_hide_<?=$agency_list[AG_IDX]?>" class="btnstyle1 btnstyle1-primary btnstyle1-xs" style="width:100% !important; display:none;" onclick="branchView('hide', '<?=$agency_list[AG_IDX]?>');"><i class="fas fa-caret-up"></i></button>
		<? } ?>
	</td>
	<td class="ag-btn">
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="location.href='<?=_A_PATH_PARTNER_AG_REG?>?mode=modify&key=<?=$agency_list[AG_IDX]?>'">Modify</button>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="agencyDel('A','<?=$agency_list[AG_IDX]?>');"><i class="far fa-trash-alt"></i> DEL</button>
	</td>
</tr>
<tr id="branch_tr_<?=$agency_list[AG_IDX]?>" style="display:none;" >
	<td colspan="8">
		<div id="branch_<?=$agency_list[AG_IDX]?>">
		</div>
	</td>
</tr>
<tr><td colspan="8" style="border:none; padding:0; margin:0; height:3px;"></td></tr>
<? } ?>
			</table>
		</div>
		<div class="footer-padding"></div>
	</div>
</div>
<script type="text/javascript"> 
<!-- 
// 지사보기
branchView=function(mode, idx){
	if( mode == "show" ){
		$("#branch_tr_"+idx).show();
		$("#branch_view_btn_show_"+idx).hide();
		$("#branch_view_btn_hide_"+idx).show();
		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PARTNER_AG_BRANCH_LIST?>",
			data : { key : idx },
			success: function(oHtml) {
				$('#branch_'+idx).html(oHtml);
			}
		});
	}else{
		$("#branch_tr_"+idx).hide();
		$("#branch_view_btn_show_"+idx).show();
		$("#branch_view_btn_hide_"+idx).hide();
	}

};

// 에이전시 노출 
agencyActive=function(mode, idx){

		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PARTNER_OK?>",
			data : { 
				a_mode : "agencyActive",
				idx : idx,
				mode : mode
			},
			success: function(getdata) {
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					if( mode == "N" ){
						$("#active_btn_n_"+idx).hide();
						$("#active_btn_y_"+idx).show();
					}else{
						$("#active_btn_n_"+idx).show();
						$("#active_btn_y_"+idx).hide();
					}
				}
			}
		});

};

// 에이전시 삭제
agencyDel=function(kind, idx){

	if(kind=="A"){
		var confirmMsg = "해당 에이전시를 삭제합니다\n지사가 있을경우 지사도 모두 삭제됩니다.\n정말 삭제하시겠습니까?";
	}else{
		var confirmMsg = "해당 에이전시를 삭제합니다\n정말 삭제하시겠습니까?";
	}
	if(confirm(confirmMsg)){
		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PARTNER_OK?>",
			data : { 
				a_mode : "agencyDel",
				idx : idx,
				ag_kind : kind
			},
			success: function(getdata) {
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					$("#trid_"+idx).remove();
				}
			}
		});
	}
};
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>