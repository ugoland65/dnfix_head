<?
$_idx = $_get1;
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from work_log WHERE idx = '".$_idx."' "));
	
	$data_ad = sql_fetch_array(sql_query_error("select ad_nick, ad_image from admin WHERE idx = '".$data['reg_idx']."' "));

	$_file_data = json_decode($data['file'], true);

	$_view_check_data = json_decode($data['view_check'], true);

	$_my_view_check = "no";
	for ($i=0; $i<count($_view_check_data); $i++){
		if( $_view_check_data[$i]['idx'] == $_ad_idx ){
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


.work-log-comment-list-wrap{ height:calc(100% - 135px); overflow-y:scroll; }
.work-log-comment-write-box{ height:135px; padding:10px; background:#fff;  border-top:1px solid #555555; }

.work-log-comment-list-wrap::-webkit-scrollbar{ width:7px; height:7px; border-left:solid 1px rgba(255,255,255,.1)}
.work-log-comment-list-wrap::-webkit-scrollbar-thumb{  background:#aaa; border-radius:7px;  }





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
<div id="contents_body" class="partition-body">
	<div id="contents_body_wrap">

		<div class="chat-body-wrap">
			<ul class="chat-body-body">
				
			</ul>
			<ul class="chat-body-comment">
				
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

workLogView.commentView();
//--> 
</script> 