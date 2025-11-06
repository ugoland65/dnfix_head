<?
$pageGroup = "member";
$pageName = "member_info_popup";

include "../lib/inc_common.php";

	$_idx = securityVal($idx);

	$popup_browser_title = " 상품 알람 설정 - ( ".$_idx." )";


	$query = "select * from prd_stock where ps_idx = '".$_idx."'";
	$result = wepix_query_error($query);

	$alarm_data = wepix_fetch_array($result);

	$cd_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$alarm_data[ps_prd_idx]."'"));

	$_ary_alarm_count = explode(",", $alarm_data[ps_alarm_count]);
	$_ary_alarm_message = explode(",", $alarm_data[ps_alarm_message]);
	$_ps_alarm_yn = $alarm_data[ps_alarm_yn];

include "../layout/header_popup.php";
?>
<script>
function doAlarmSet(){
	var form = document.alarm_stock_form;
	form.submit();
}
</script>
	<form name='alarm_stock_form' method='post' action='processing.prd2_stock.php'>
		<input type="hidden" name="a_mode" value="stockAlarmSet">
		<input type="hidden" name="prd_key" value="<?=$_idx?>">
		<table class="table-list">
			<tr>
				<th>상품 이름</th>
				<td><?=$cd_data[CD_NAME]?></td>
			</tr>
			<tr>
				<th>알림 설정</th>
				<td>
				<label><input type='radio' value='Y' name='alarmYN' <?if($_ps_alarm_yn == 'Y' || $_ps_alarm_yn == '' ){ echo "checked"; } ?> > Y </label>
				<label><input type='radio' value='N'name='alarmYN'  <?if($_ps_alarm_yn == 'N' ){ echo "checked"; } ?> > N </label>
				</td>
			</tr>
			<tr>
				<th>알림 카운트</th>
				<th>알림 메세지</th>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[0]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [0]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[1]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [1]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[2]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [2]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[3]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [3]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[4]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [4]?>'></td>
			</tr>
			<tr>
				<td><input type='text' name='alarmCount[]' value='<?=$_ary_alarm_count[5]?>'></td>
				<td><input type='text' name='alarmMassage[]'value='<?=$_ary_alarm_message [5]?>'></td>
			</tr>
			<tr>
				<td colspan='2'><input type='button' value="설정 완료" onclick="doAlarmSet();"></td>
			</tr>
		</table>
	</form>
<?
include "../layout/footer_popup.php";
exit;
?>