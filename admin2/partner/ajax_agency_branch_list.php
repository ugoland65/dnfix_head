<?
$pageGroup = "partner";
$pageName = "agency_branch_list";

include "../lib/inc_common.php";

	$_ag_co_idx = securityVal($key);

	$agency_result = wepix_query_error("select * from "._DB_AGENCY." where AG_DEL_YN='N' and AG_KIND='B' and AG_CO_IDX = '".$_ag_co_idx."' order by AG_IDX desc");
?>
		<div class="table-wrap">
			<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
				<tr>
					<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
					<th class="list-idx">고유 번호</th>
					<th class="ag-kind">분류</th>
					<th class="ag-head-office">본사</th>
					<th class="ag-name">회사명</th>
					<th class="list-active">노출</th>
					<th class="ag-btn">관리</th>
				</tr>
<?
while($agency_list = wepix_fetch_array($agency_result)){
	$trcolor = "#f5f5f5";
?>
<tr align="center" id="trid_<?=$agency_list[AG_IDX]?>" bgcolor="<?=$trcolor?>">
	<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$agency_list[AG_IDX]?>" ></td>
	<td class="list-idx"><?= $agency_list[AG_IDX]?></td>
	<td class="ag-kind">지사</td>
	<td class="ag-head-office"><?= $agency_list[AG_BRANCH]?></td>
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
	<td class="ag-btn">
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="location.href='<?=_A_PATH_PARTNER_AG_REG?>?mode=modify&key=<?=$agency_list[AG_IDX]?>'">Modify</button>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="agencyDel('B','<?=$agency_list[AG_IDX]?>');"><i class="far fa-trash-alt"></i> DEL</button>
	</td>
</tr>
<? } ?>
			</table>
		</div>
<?
exit;
?>