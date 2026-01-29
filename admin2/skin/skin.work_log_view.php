<?
$_idx = $_get1;
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from work_log WHERE idx = '".$_idx."' "));
	
	if (!is_array($data)) {
		$data = [];
	}
	
	$data_ad = sql_fetch_array(sql_query_error("select ad_nick, ad_image from admin WHERE idx = '".($data['reg_idx'] ?? '')."' "));
	
	if (!is_array($data_ad)) {
		$data_ad = [];
	}

	$_file_data = json_decode($data['file'] ?? '{}', true);
	if (!is_array($_file_data)) {
		$_file_data = [];
	}

	$_view_check_data = json_decode($data['view_check'] ?? '[]', true);
	if (!is_array($_view_check_data)) {
		$_view_check_data = [];
	}

	$_my_view_check = "no";
	for ($i=0; $i<count($_view_check_data); $i++){
		if( ($_view_check_data[$i]['idx'] ?? '') == $_ad_idx ){
			$_my_view_check = "ok";
			break;
		}
	}

	if( $_my_view_check == "no" ){
	
		$_reg_d = array( "idx" => $_ad_idx, "date" => $action_time, "id" => $_sess_id, "name" => $_ad_name, "ip" => $check_ip, "domain" => $check_domain );

		if( is_array($_view_check_data) ){
			array_push($_view_check_data, $_reg_d);
		}else{
			$_view_check_data = array($_reg_d);
		}

		$_view_check = json_encode($_view_check_data, JSON_UNESCAPED_UNICODE);

		$query = "update work_log set
			view_check = '".$_view_check."'
			where idx = '".$_idx."' ";
		sql_query_error($query);

	}

}
?>
<style type="text/css">
#file_list_wrap{ width:500px; display:inline-block; }
.file-list{ padding:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:5px; }

.work-log-view-partition-body{  border-right:1px solid #555555; }
.work-log-view-partition-comment{ width:500px; }
.work-log-comment-list-wrap{ height:calc(100% - 135px); overflow-y:scroll; }
.work-log-comment-write-box{ height:135px; padding:10px; background:#fff;  border-top:1px solid #555555; }

.work-log-comment-list-wrap::-webkit-scrollbar{ width:7px; height:7px; border-left:solid 1px rgba(255,255,255,.1)}
.work-log-comment-list-wrap::-webkit-scrollbar-thumb{  background:#aaa; border-radius:7px;  }

.partition-wrap{ display:table; height:100%; overflow:hidden; }
.partition-wrap > ul{ display:table-cell; vertical-align:top; }
.partition-scroll{ width:100%; height:100%; overflow-y:scroll; }
.partition-scroll::-webkit-scrollbar{ width:7px; height:7px; border-left:solid 1px rgba(255,255,255,.1)}
.partition-scroll::-webkit-scrollbar-thumb{  background:#aaa; border-radius:7px;  }

.bo_body_wrap{ }
.bo_body_wrap img{ max-width:100% !important; }

.work-log-view-mb-profile{ display:inline-block; width:24px; height:24px; border:1px solid #999; overflow:hidden; border-radius:50%; vertical-align:middle;  }
.work-log-view-mb-profile img{ width:100%;  }
</style>
<div id="contents_head">
	<h1>업무 게시판 - (<?=$data['category']?>) <?=$data['subject']?></h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="partition-wrap work-log-view-partition">
			<ul class="work-log-view-partition-body">
				<div class="partition-scroll p-10">
					<table class="table-reg th-150">
						<tr>
							<th>분류/제목</th>
							<td>
								[<?=$data['category']?>] <b><?=$data['subject']?></b>
							</td>
						</tr>

						<tr>
							<th>작성일</th>
							<td>
								<?=date("y.m.d H:i", strtotime($data['reg_date']))?> | 
								<div class="work-log-view-mb-profile"><img src="/data/uploads/<?=$data_ad['ad_image']?>" alt=""></div>
								<?=$data_ad['ad_nick']?>
							</td>
						</tr>
					
						<? if( $data['target_mb'] ){ ?>
						<tr>
							<th>참여자</th>
							<td>
								<?
									$_this_target_mb_idx = explode("@", $data['target_mb']);
									for ($i=1; $i<count($_this_target_mb_idx); $i++){ 
										$_this_addata = sql_fetch_array(sql_query_error("select ad_nick, ad_image from admin WHERE idx = '".$_this_target_mb_idx[$i]."' "));
								?>
								<div class="work-log-view-mb-profile" ><img src="/data/uploads/<?=$_this_addata['ad_image']?>" alt=""></div> <?=$_this_addata['ad_nick']?>
								<? } ?>
							</td>
						</tr>
						<? } ?>

						<? if( $_ad_level == 100 ){ ?>
						<tr>
							<th>읽음 체크<br>( 관리자 래밸 100만 보임)</th>
							<td>
								<div>
								<?
									for ($i=0; $i<count($_view_check_data); $i++){
								?>
									<ul><?=$_view_check_data[$i]['date']?> <?=$_view_check_data[$i]['name']?></ul>
								<? } ?>
								</div>

								<?=$data['target_mb']?>
							</td>
						</tr>

						<? } ?>

						<?
						if( $data['category'] != "업무일지"){

							$_view_check_action_active = "no";

						// 변수 초기화
						$_view_check_list = [];
						
						$_query = "select
							A.*,
							B.ad_nick, B.ad_image 
							from work_view_check A
								left join admin B ON (B.idx = A.mb_idx  ) 
							WHERE mode = 'log' AND tidx = '".$_idx."' ";
						$_result = sql_query_error($_query);
						while($view_check = sql_fetch_array($_result)){
						
							$_view_check_list[] = array(
								"nick" => $view_check['ad_nick'] ?? '',
								"ad_img" => $view_check['ad_image'] ?? '',
								"check_time" => $view_check['reg_date'] ?? ''
							);

							if( ($view_check['mb_idx'] ?? '') == $_ad_idx ){
								$_view_check_action_active = "ok";
							}

							}

							//이글을 내가 썼는가?
							if( $data['reg_idx'] == $_ad_idx ){
								$_view_check_action_active = "ok";
							}

						?>
						<tr>
							<th>체크</th>
							<td>

								<? if( $_view_check_action_active == "no" ){ ?>
									<div class="m-b-20">
										<div style="color:#ff0000;">
											체크 여부는 해당 내용을 전달 또는 확인 받았다는 피드백입니다.<br> 
											아직 이 게시물을 확인처리 하지 않았습니다. 내용을 확인 후 확인 처리를 해주세요.
										</div>
										<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm m-t-5" onclick="workLogView.viewCheck()" >이 내용을 확인했습니다.</button>
									</div>
								<? }else{ ?>

								<? } ?>

								<div>체크 완료 리스트</div>
								<div>
								<?
								for ($i=0; $i<count($_view_check_list); $i++){
							?>
								<ul>
									<div class="work-log-view-mb-profile" ><img src="/data/uploads/<?=$_view_check_list[$i]['ad_img'] ?? ''?>" alt=""></div> <?=$_view_check_list[$i]['nick'] ?? ''?> | 
									<?=$_view_check_list[$i]['check_time'] ?? ''?>
								</ul>
							<? } ?>
								</div>

							</td>
						</tr>

						<tr>
							<th>상태</th>
							<td>

								<button type="button" id="" class="btnstyle1 <? if( $data['state'] == "대기" ) echo "btnstyle1-info"; ?> btnstyle1-sm " onclick="workLogView.stateModify(this, '대기');" >대기</button>
								<button type="button" id="" class="btnstyle1 <? if( $data['state'] == "확인" ) echo "btnstyle1-info"; ?> btnstyle1-sm " onclick="workLogView.stateModify(this, '확인');" >확인</button>
								<button type="button" id="" class="btnstyle1 <? if( $data['state'] == "완료" ) echo "btnstyle1-info"; ?> btnstyle1-sm " onclick="workLogView.stateModify(this, '완료');" >완료</button>
								<button type="button" id="" class="btnstyle1 <? if( $data['state'] == "반려" ) echo "btnstyle1-info"; ?> btnstyle1-sm " onclick="workLogView.stateModify(this, '반려');" >반려</button>

							</td>
						</tr>
					<? } ?>

					<? 
					$file_name_list = $_file_data['file_name'] ?? [];
					if (!is_array($file_name_list)) {
						$file_name_list = [];
					}
					if( count($file_name_list) > 0 ){ 
					?>
					<tr>
						<th>첨부파일</th>
						<td>
							<div id="file_list_wrap">
								<? for ($i=0; $i<count($file_name_list); $i++){ ?>
								<div class="file-list">
									<a href="/data/work_log/<?=$file_name_list[$i] ?? ''?>"><?=$file_name_list[$i] ?? ''?></a>
								</div>
								<? } ?>
							</div>
						</td>
					</tr>
					<? } ?>



						<tr>
							<td colspan="2" class="bo_body_wrap p-30">
								<?=$data['body']?>
							</td>
						</tr>
					</table>


					<? if( $data['cmt_b_count'] > 0 ){ ?>

					<style type="text/css">
					.reply-title{ font-size:20px; font-weight:600; margin-top:20px; padding:0 0 6px 5px; }

					.reply-wrap{ background-color:#fff; border:1px solid #bbb; padding:10px; margin-bottom:5px; border-radius:5px;  }

					.reply-box{ display:table; }
					.reply-box > ul{ display:table-cell; vertical-align:top; }
					.reply-box > ul.left{ width:70px; }
					.comment-mb-profile{ display:inline-block; width:60px; height:60px; border:1px solid #999; overflow:hidden; border-radius:50%; }
					.comment-mb-profile img{ width:100%; }
					</style>
					<div class="reply-title">답변</div>
					<div>
					<?
						$_where = " WHERE mode = 'log' AND kind = 'B' AND tidx = '".$_idx."' ";
						$_query = "select 
							A.*,
							B.ad_nick, B.ad_image 
							from work_comment A
							left join admin B ON (B.idx = A.mb_idx  ) 
							".$_where." ORDER BY grpno DESC, grpord ASC";
						$_result = sql_query_error($_query);
						while($_list = wepix_fetch_array($_result)){
					?>
						<div class="reply-wrap">
							<div class="reply-box">
								<ul class="left text-center">
									<div class="comment-mb-profile"><img src="/data/uploads/<?=$_list['ad_image']?>" alt=""></div>
									<span style="font-size:11px;"><?=$_list['idx']?></span>
								</ul>
								<ul class="p-l-7">
									<div>
										<ul>
											<span style="font-weight:600"><?=$_list['ad_nick']?></span> 
											<span style="font-size:11px;"><?=date("y.m.d H:i", strtotime($_list['reg_date']))?></span></ul>
										<ul class="m-t-5"><?=$_list['comment']?></ul>
									<div>
								</ul>
							</div>
						</div>
					<? } ?>
					</div>
				<? } ?>


				</div>
			</ul>
			<ul class="work-log-view-partition-comment">

<div style="padding:50px">
	댓글 기능이 업그레이드 되었습니다.<br>
	(1)업무게시판 (2)달력(캘린더) (3)상품 (4)주문<br>
	------------------------------------------------------------<br>
	(1. 업무게시판)<br>
	지속적 팔로업 해서 진행사항을 업데이트 해야하는 경우 및 보고<br>
<br>
	(2. 달력: 캘린더)<br>
	일일 업무처리를 위한 인스턴트 요청 및 회신 사항<br>
<br>
	(3,4. 상품, 주문)<br>
	상품, 주문 업무의 히스토리,요청 및 회신 사항 <br>
<br><br>
			<button type="button" id="" class="btnstyle1" onclick="footerGlobal.comment('log','<?=$data['idx']?>')" >
				댓글
				<? if( $data['cmt_s_count'] > 0 ) { ?> : <b><?=$data['cmt_s_count']?></b><? } ?>
			</button>
</div>

				<!-- 
				<div class="work-log-comment-list-wrap">
					<div class=" p-10" id="work_log_comment_list">

					</div>
				</div>


				<form id="work_comment_form">
				<input type="hidden" name="a_mode" value="work_comment_reg" >
				<input type="hidden" name="work_comment_mode" value="log" >
				<input type="hidden" name="kind" value="S" >
				<input type="hidden" name="tidx" value="<?=$_idx?>" >

				<div class="work-log-comment-write-box">
					<ul><textarea name="comment" id="comment" style="width:100%; height:70px;" placeholder="댓글입력"></textarea></ul>
					<ul>@ 멘션 : 
						<?
							$_where = "";
							$_query = "select * from admin ".$_where." ORDER BY idx DESC";
							$_result = sql_query_error($_query);
							while($_list = wepix_fetch_array($_result)){
						?>
							<label><input type="checkbox" name="target_mb_idx[]" value="<?=$_list['idx']?>" > <?=$_list['ad_nick']?></label>
						<? } ?>
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="workLogView.commentWrite(this, '<?=$_idx?>')" >댓글입력</button>
					</ul>
				</div>
				</form>
				-->

			</ul>
		</div>

	</div>
</div>
<div id="contents_bottom">

	<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/ad/staff/work_log/<?=$data['category']?>'" > 
		<i class="fas fa-arrow-left"></i> 목록
	</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="location.href='/ad/staff/work_log_reg/<?=$_idx?>'" > 
		<i class="far fa-check-circle"></i> 수정
	</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="workLogView.commentBigReg()" > 
		답변달기
	</button>
	<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="workLogView.contentsDel(this, '<?=$_idx?>')" >
		<i class="fas fa-trash-alt"></i> 삭제
	</button>

</div>
<script type="text/javascript"> 
<!--
var workLogView = function() {

	var tidx = "<?=$_idx?>";
	var commentBigWindow;

	var C = function() {
	};

	return {
		init : function() {

		},

		fileDel : function( obj, idx, delnum ) {
			
			var filename = $(obj).data("filename");

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '( '+ filename +' ) 파일을 삭제합니다.<br>삭제시 복구되지 않습니다.',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제하기',
						btnClass: 'btn-red',
						action: function(){

							$.ajax({
								url: "/ad/processing/work",
								data : { "a_mode" : "work_file_del", "pmode" : "work_log", "idx" : idx, "delnum" : delnum   },
								type: "POST",
								dataType: "json",
								success: function(res){
									if ( res.success == true ){
										alert("파일 삭제 완료");
										location.href='/ad/staff/work_log_view/'+ idx;
									}else{
										showAlert("Error", res.msg, "alert2" );
										return false;
									}
								},
								error: function(request, status, error){
									console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
									showAlert("Error", "에러", "alert2" );
									return false;
								},
								complete: function() {
									$(obj).attr('disabled', false);
								}
							});

						}
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});

		},

		contentsDel : function( obj, idx ) {

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '컨텐츠를 전부 삭제합니다.<br>삭제시 첨부파일도 모두 삭제됩니다.<br>삭제시 복구되지 않습니다.',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제하기',
						btnClass: 'btn-red',
						action: function(){

							$(obj).attr('disabled', true);
							$.ajax({
								url: "/ad/processing/work",
								data: { "a_mode":"workLogDel", "idx":idx },
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										alert("삭제가 완료되었습니다.");
										location.href='/ad/staff/work_log';
									}else{
										showAlert("Error", res.msg, "alert2" );
										return false;
									}
								},
								error: function(request, status, error){
									console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
									showAlert("Error", "에러", "alert2" );
									return false;
								},
								complete: function() {
									$(obj).attr('disabled', false);
								}
							});

						}
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});
		},

		viewCheck : function( obj ) {

			$.ajax({
				url: "/ad/processing/work",
				data : { "a_mode" : "work_view_check", "tidx" : tidx, "work_mode" : "log" },
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						alert("확인처리 완료");
						location.reload();
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		},

		stateModify : function( obj, state ) {

			$.ajax({
				url: "/ad/processing/work",
				data : { "a_mode" : "work_state_modify", "idx" : tidx, "state" : state },
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						alert("상태변경 처리 완료");
						location.reload();
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		},

		commentBigReg : function(obj) {

			var width = "1000px";

			commentBigWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "답변달기",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/work_comment_big_reg',
						data: { "tidx":tidx },
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


		commentWrite : function( obj, idx ) {

			var _comment = $("#comment").val();

			if( !_comment ){
				showAlert("Error", "댓글내용을 입력해주세요!", "alert2" );
				return false;			
			}

			var formData = $("#work_comment_form").serializeArray();

			$.ajax({
				url: "/ad/processing/work",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						workLogView.commentView();
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		},

		commentView : function() {

			$.ajax({
				url: "/ad/ajax/work_comment",
				data: { "work_comment_mode":"log", "tidx":tidx },
				type: "POST",
				dataType: "html",
				success: function(getdata){
					$('#work_log_comment_list').html(getdata);
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {
					loading('off','white');
				}
			});

		},

		commentReMake : function( obj, idx ) {

			if( $("#comment_re_"+ idx).data("state") == "off" ){
				var shtml = ''
					+ '<div class="m-t-10">'
					+ '<ul><textarea name="comment" id="comment" style="width:100%; height:80px;" placeholder="댓글입력"></textarea></ul>'
					+ '<ul>@ 멘션 : '
					+ '</ul>';

				$("#comment_re_"+ idx).html(shtml);
				$("#comment_re_"+ idx).data("state","on");

			}else{
				$("#comment_re_"+ idx).html("");
				$("#comment_re_"+ idx).data("state","off");
			}

		},

		commentviewCheck : function( obj, idx ) {

			$.ajax({
				url: "/ad/processing/work",
				data : { "a_mode" : "work_view_check", "tidx" : idx, "work_mode" : "comment" },
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						alert("확인처리 완료");
						workLogView.commentView();
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		},

	};

}();

//workLogView.commentView();
//--> 
</script> 