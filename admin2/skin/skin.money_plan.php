<?
	$_where = " ";

	$total_count = sql_counter("money_plan", $_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	//$_query = "select * from "._DB_ADMIN." ".$_where." order by AD_IDX desc limit ".$from_record.", ".$list_num;
	$_query = "select * from money_plan ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);

?>
<div id="contents_head">
	<h1>운영자금 계획</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="moneyPlan.reg(this)" > 
			<i class="fas fa-plus-circle"></i>
			신규 등록
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
				<th class="">이름</th>
				<th class="">balance</th>
				<th>관리</th>
			</tr>
		<?
		while($_list = sql_fetch_array($_result)){
			
		?>
		<tr align="center" id="trid_<?=$_list['idx']?>" bgcolor="<?=$trcolor?>">
			<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$_list['idx']?>" ></td>	
			<td class="list-idx"><?=$_list['idx']?></td>
			<td class=""><?=$_list['category']?></td>
			<td class=""><b><?=$_list['name']?></b></td>
			<td class=""><?=number_format($_list['balance'])?></td>
			<td>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moneyPlan.view(this, '<?=$_list['idx']?>')"> 상세내용 </button>
			</td>
		<tr>
		<? }?>

	</table>

	</div>
</div>
<script type="text/javascript"> 
<!-- 
var moneyPlan = function() {

	var moneyPlanWindow;

	var C = function() {
	};

	return {
		init : function() {

		},
		reg : function(obj) {

			var width = "800px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "신규 운영자금계획 등록",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/money_plan_info',
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

			var width = "800px";

			moneyPlanWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "운영자금계획 상세",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/money_plan_info',
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

		},

		// 주문서 리셋
		viewReset : function( idx ) {

			$.ajax({
				url: '/ad/ajax/money_plan_info',
				data: { "idx":idx },
				type: "POST",
				dataType: "html",
				success: function(res){
					moneyPlanWindow.setContent(res);
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
				}
			});

		},

	};

}();
//--> 
</script> 