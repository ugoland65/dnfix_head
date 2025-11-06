<?
	$_where = "";

	$total_count = wepix_counter("prd_rack", $_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	//$_query = "select * from "._DB_ADMIN." ".$_where." order by AD_IDX desc limit ".$from_record.", ".$list_num;
	$_query = "select 
	* ,
	LEFT(code, 2) as code_group
	from prd_rack ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);

?>
<div id="contents_head">
	<h1>(RACK) 랙 관리</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="prdRack.reg()" > 
			<i class="fas fa-plus-circle"></i>
			신규 랙 생성
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<table class="table-style">	
			<tr class="list">
				<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
				<th class="list-idx">고유번호</th>
				<th class="">랙 이름</th>
				<th class="">코드그룹</th>
				<th class="">랙 코드</th>
				<th width="60px">관리</th>
			</tr>
			<?
			while($_list = wepix_fetch_array($_result)){
			?>
			<tr align="center" id="trid_<?=$_list['idx']?>" bgcolor="<?=$trcolor?>">
				<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$_list['idx']?>" ></td>	
				<td class="list-idx"><?=$_list['idx']?></td>
				<td class=""><b><?=$_list['name']?></b></td>
				<td class=""><?=$_list['code_group']?></td>
				<td class=""><?=$_list['code']?></td>
				<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdRack.view(this, '<?=$_list['idx']?>')"> 수정 </button></td>
			<tr>
			<? }?>
		</table>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>
<script type="text/javascript"> 
<!-- 
var prdRack = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		reg : function(obj) {

			var width = "400px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "신규 랙 등록",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/rack_info',
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

			var width = "1200px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "랙 상세",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/rack_info',
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

	};

}();
//--> 
</script>