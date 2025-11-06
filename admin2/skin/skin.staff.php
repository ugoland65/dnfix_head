<?
	$_where = "";

	$total_count = wepix_counter(_DB_ADMIN, $_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	//$_query = "select * from "._DB_ADMIN." ".$_where." order by AD_IDX desc limit ".$from_record.", ".$list_num;
	$_query = "select * from admin ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);

?>
<style type="text/css">
.ad-profile-img{ width:80px; height:80px; box-sizing:border-box;  overflow:hidden; border-radius:50%; }
.ad-profile-img img{ width:100%; }
</style>
<div id="contents_head">
	<h1>인사관리</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="staff.reg()" > 
			<i class="fas fa-plus-circle"></i>
			신규 직원등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<table class="table-style">	
			<tr class="list">
				<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
				<th class="list-idx">고유번호</th>
				<th class="">프로필</th>
				<th class="list-nick">닉네임</th>
				<th class="list-id">아이디</th>
				<th width="80px">이름</th>
				<th>생일</th>
				<th>입사일</th>
				<th width="80px">등급</th>
				<th width="60px">관리</th>
			</tr>
		<?
		while($_list = wepix_fetch_array($_result)){
		
		?>
		<tr align="center" id="trid_<?=$_list['idx']?>" bgcolor="<?=$trcolor?>">
			<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$_list['idx']?>" ></td>	
			<td class="list-idx"><?=$_list['idx']?></td>
			<td class="">
				<div class="ad-profile-img"><img src="/data/uploads/<?=$_list['ad_image']?>?v=<?=time()?>" alt=""></div>
			</td>
			<td class="list-nick"><b><?=$_list['ad_nick']?></b></td>
			<td class="list-id"><?=$_list['ad_id']?></td>
			<td><?=$_list['ad_name']?></td>
			<td><?=$_list['ad_birth']?></td>
			<td><?=$_list['ad_joining']?></td>
			<td><?=$_list['ad_level']?></td>
			<td>
				<? if( $_sess_id == $_list['ad_id'] || $_ad_level == 100 ){ ?>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="staff.view(this, '<?=$_list['idx']?>')"> 수정 </button>
				<? } ?>
			</td>
		<tr>
		<? } ?>

	</table>

	<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>
<script type="text/javascript">
<!-- 
var staff = function() {

	var B;

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
				title : "신규직원 등록",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/staff_info',
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

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "인사관리",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/staff_info',
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