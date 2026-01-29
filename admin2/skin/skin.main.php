<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<style type="text/css">
	#contents_body_wrap{ padding:5px !important; }
	.main-wrap{ width:100%; display:table; }
	.main-wrap > ul{ display:table-cell; vertical-align:top; }
	.main-wrap > ul.report{ width:400px; padding:20px 20px 0 10px; }
	.main-wrap > ul.calendar-menu{ width:170px; padding-top:50px; }
	.main-wrap > ul.calendal-wrap{ padding:10px 10px 0 0; }
	.calendar-menu-wrap{ width:160px; background-color:#fff; border:1px solid #bbb; box-sizing:border-box; border-radius:5px; padding:10px 0 10px 7px; }
	.calendar-menu-wrap > ul{}
	.calendar-menu-wrap > ul > li{ padding: 5px 3px 5px 8px; }
	.calendar-menu-wrap > ul > li i{ width:18px; text-align:center; }
	.calendar-menu-wrap > ul > li label{ margin:0 !important; font-weight: 500 !important; cursor:pointer; }

	.main-notice-wrap{ background-color:#fff; border:1px solid #bbb; box-sizing:border-box; border-radius:5px; padding:5px 10px; margin-bottom:4px; }

	.my-schedule-box{ background-color:#fff; border:1px solid #bbb; box-sizing:border-box; border-radius:5px; padding:5px 10px; margin-bottom:4px; }
	.main-target-mb-profile{ display:inline-block; width:20px; height:20px; border:1px solid #999; overflow:hidden; border-radius:50%; }
	.main-target-mb-profile img{ width:100%; }
</style>

<div id="contents_head">
	<h1>DASHBOARD</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div class="main-wrap">
			<ul class="report">
			
				<div id="my_count">
				</div>

				<div class="m-t-15">나의 참여 일정</div>

				<div>
				<?
					$_target_mb_text = "@".$_ad_idx;
					$_where = " WHERE state ='I' AND INSTR(target_mb, '".$_target_mb_text."')";
					$_query = "select * from calendar ".$_where." ORDER BY idx DESC";
					$_result = sql_query_error($_query);
					while($_list = sql_fetch_array($_result)){
						$_this_target_mb_idx = explode("@", $_list['target_mb']);

						for ($i=1; $i<count($_this_target_mb_idx); $i++){
							
						}

				?>
					<div class="my-schedule-box" onclick="calendar.detail(this, '<?=$_list['idx']?>');" style="cursor:pointer;" >
						<ul><b><?=date("y.m.d", strtotime($_list['date_s']))?></b> [<?=$_list['kind']?>] <?=$_list['subject']?></ul>
						<ul class="m-t-3"><?=$_list['memo']?></ul>
						<ul class="m-t-3">
							<? 
							for ($i=1; $i<count($_this_target_mb_idx); $i++){ 
								$_this_addata = sql_fetch_array(sql_query_error("select ad_nick, ad_image from admin WHERE idx = '".$_this_target_mb_idx[$i]."' "));
							?>
								<div class="main-target-mb-profile" data-toggle="tooltip" data-placement="top" title="<?=$_this_addata['ad_nick']?>"><img src="/data/uploads/<?=$_this_addata['ad_image']?>" alt=""></div>
							<? } ?>
						</ul>
					</div>
				<? } ?>
				</div>

			</ul>

			<ul class="calendal-wrap">
				<div id="calendar_wrap">
				</div>
			</ul>

			<ul class="calendar-menu">

				<div class="calendar-menu-wrap">
					<ul>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="delivery" class="calendar-view-checkbox" checked> <i class="fas fa-truck"></i> 배송비</label></li>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="tax" class="calendar-view-checkbox" checked> <i class="fas fa-receipt"></i> 관부가세</label></li>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="staff_meeting" class="calendar-view-checkbox" checked> <i class="far fa-comment-dots"></i> 회의</label></li>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="meeting" class="calendar-view-checkbox" checked> <i class="far fa-handshake"></i> 미팅</label></li>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="schedule" class="calendar-view-checkbox" checked> <i class="fas fa-hiking"></i> 일정</label></li>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="check" class="calendar-view-checkbox" checked> <i class="fas fa-calendar-check"></i> 체크</label></li>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="point" class="calendar-view-checkbox" checked> <i class="fas fa-star"></i> 중요</label></li>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="event" class="calendar-view-checkbox" checked> <i class="fas fa-democrat"></i> 행사</label></li>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="individual" class="calendar-view-checkbox" checked> <i class="fas fa-tag"></i> 개인 (나만보임)</label></li>
						<li><label onclick="calendar.view();"><input type="checkbox" data-mode="holiday" class="calendar-view-checkbox" checked> <i class="fas fa-user-alt-slash"></i> 월차,반차,조퇴</label></li>
					<ul>
				</div>

			</ul>

		</div>
	</div>
</div>
<script type="text/javascript"> 
<!-- 
var calendarWindow;

var main = function() {

	return {
		init : function() {

		},
		myCount : function(cmode) {

			$.ajax({
				url: "/ad/ajax/main_my_count",
				data: { "cmode":cmode },
				type: "POST",
				dataType: "html",
				success: function(res){
					$("#my_count").html(res);
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					//$(obj).attr('disabled', false);
				}
			});

		}
	};

}();

var calendar = function() {


	var C = function() {
	};

	return {
		
		init : function() {

			var cvStorageOtput = localStorage.getItem("cvStorage");		
			var cvStorageArr = JSON.parse(cvStorageOtput);

			$(".calendar-view-checkbox").each(function() { 
				var objname = $(this).data("mode");
				if( cvStorageOtput ){
					if(cvStorageArr.indexOf(objname) < 0)  {
						$(this).attr('checked', false);
					}else{
						$(this).attr('checked', true);
					}
				}else{
					$(this).attr('checked', true);
				}

			});

		},

		view : function( y, m ) {
			
			const calendarView = [];
			
			$(".calendar-view-checkbox").each(function() { 
				if( $(this).is(':checked') ) {
					var objname = $(this).data("mode");
					calendarView.push(objname);
				}
			});

			var cv = JSON.stringify(calendarView);
			
			localStorage.setItem("cvStorage", cv);

			/*
			if( $("#cv_delivery").val() == "show" ){
				const obj = { delivery: 'show' };
				arr.push(obj);
			}
			*/
			
			$.ajax({
				url: "/ad/ajax/calendar",
				data: { "y":y, "m":m, "calendar_view":calendarView },
				type: "POST",
				dataType: "html",
				success: function(res){
					$("#calendar_wrap").html(res);
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					//$(obj).attr('disabled', false);
				}
			});

		},

		reg : function( y,m,d ) {

			var width = "800px";

			calendarWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "캘린더 등록",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/calendar_reg',
						data: { "y":y, "m":m, "d":d, },
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

		// 캘린더 상세보기
		detail : function( obj, idx, mode ) {

			var width = "800px";

			calendarWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "캘린더 상세보기",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/calendar_reg',
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

main.myCount();
calendar.init();
calendar.view();
//--> 
</script> 