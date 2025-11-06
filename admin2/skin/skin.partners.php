<?php
/*
=================================================================================
뷰 파일: admin2/skin/skin.partners.php
호출경로 : /ad/partners
설명: 거래처 목록 화면
작성자: Lion65
수정일: 2025-03-15
=================================================================================

GET
@getParam {int} $_prd_idx - 상품 시퀀스

CONTROLLER
/application/Controllers/Admin/OrderController.php

*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\OrderController;

$orderController = new OrderController(); 

$viewData = $orderController->partnersIndex();

/*
	$_where = " ";

	$total_count = wepix_counter("partners", $_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	//$_query = "select * from "._DB_ADMIN." ".$_where." order by AD_IDX desc limit ".$from_record.", ".$list_num;
	$_query = "select * from partners ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);
*/
?>
<div id="contents_head">
	<h1>거래처 관리</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="partners.reg(this)" > 
			<i class="fas fa-plus-circle"></i>
			거래처 등록
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
				<th>관리</th>
			</tr>
		<?
			foreach($viewData['partnersList'] as $partner) {
		?>
		<tr align="center" id="trid_<?=$partner['idx']?>" bgcolor="<?=$trcolor?>">
			<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$partner['idx']?>" ></td>	
			<td class="list-idx"><?=$partner['idx']?></td>
			<td class=""><?=$partner['category']?></td>
			<td class=""><b><?=$partner['name']?></b></td>
			<td>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="partners.view(this, '<?=$partner['idx']?>')"> 상세내용 </button>
			</td>
		</tr>
		<? }?>

	</table>

	</div>
</div>
<script type="text/javascript"> 
<!-- 
var partners = function() {

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
				title : "거래처 등록",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/partners_info',
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
				title : "거래처 상세",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/partners_info',
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