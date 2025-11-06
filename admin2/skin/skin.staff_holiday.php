<?
	//$_where = " WHERE mode IN( '휴가','월차','조퇴','반차','유급휴가' ) ";

	$total_count = wepix_counter("schedule_sttaf", $_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	//$_query = "select * from "._DB_ADMIN." ".$_where." order by AD_IDX desc limit ".$from_record.", ".$list_num;
	$_query = "select * from schedule_sttaf ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);

?>
<div id="contents_head">
	<h1>월차 반차</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="staffHoliday.reg(this)" > 
			<i class="fas fa-plus-circle"></i>
			월차,반차 신청
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		
		<table class="table-style">	
			<tr class="list">
				<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
				<th class="list-idx">고유번호</th>
				<th class="">종류</th>
				<th class="">신청일</th>
				<th>신청인</th>
				<th>상태</th>
				<th>신청사유</th>
				<th>등록인</th>
				<th>등록일</th>
				<th>관리</th>
			</tr>
		<?

		$_state_text['1'] = "신청중";
		$_state_text['2'] = "확인중";
		$_state_text['3'] = "승인";
		$_state_text['4'] = "반려";

		while($_list = wepix_fetch_array($_result)){
			
			$_data = json_decode($_list['data'], true);
			$_reg_name = $_data['reg']['name'];
			$_reg_date = date("Y.m.d H:i", strtotime($_data['reg']['date']));
			$_target_name = $_data['target']['name'];
		?>
		<tr align="center" id="trid_<?=$_list['idx']?>" bgcolor="<?=$trcolor?>">
			<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$_list['idx']?>" ></td>	
			<td class="list-idx"><?=$_list['idx']?></td>
			<td class=""><b><?=$_list['mode']?></b></td>
			<td class=""><?=date("Y-m-d", strtotime($_list['date_s']))?></td>
			<td><?=$_target_name?></td>
			<td><?=$_state_text[$_list['state']]?></td>
			<td class=""><?=$_list['memo']?></td>
			<td><?=$_reg_name?></td>
			<td><?=$_reg_date?></td>
			<td>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="onlyAD.staffHolidayView('<?=$_list['idx']?>')"> 상세내용 </button>
			</td>
		<tr>
		<? }?>

	</table>

	</div>
</div>
<script type="text/javascript"> 
<!-- 
var staffHoliday = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		reg : function(obj) {

			var width = "600px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "휴가/월차/반차 신청",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/staff_holiday_view',
						data: { "pmode":"newReg" },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
							
						}
					},
				}
			});

		},
		view : function(obj, idx) {
/*
			var width = "600px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "휴가/월차/반차 내용보기",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/staff_holiday_view',
						data: { "idx":idx },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
							
						}
					},
				}
			});
*/
		},
	};

}();
//--> 
</script> 