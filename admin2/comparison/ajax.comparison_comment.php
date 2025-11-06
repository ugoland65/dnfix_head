<?
	include "../lib/inc_common.php";
	include 'board_inc.php';

	$_key = securityVal($key);
	$_pn = securityVal($pn);

	//댓글쿼리
	$comment_query = "where PD_UID = '".$_key."' ";
	$total_count = wepix_counter("COMPARISON_COMMENT", $comment_query);

	$list_num = 7;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page  = @ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
	$counter = $total_count - (($_pn - 1) * $list_num);
	$_view_no = $counter;

	$comment_result = wepix_query_error("select * from COMPARISON_COMMENT ".$comment_query."order by HEADNUM asc limit ".$from_record.", ".$list_num." ");

	$_view_paging = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, 'showComment','');
?>
<STYLE TYPE="text/css">
.comment-write-wrap-right{ width:450px; border:1px solid #dedede; background-color:#f5f8f9; box-sizing:border-box; padding:10px; margin:0 0 0 15px; }
.comment-write-wrap-right textarea{ width:100%; height:100px; padding:5px; line-height:140%; box-sizing:border-box;  }
.comment-write-wrap-right .form ul{ margin-bottom:5px; }

.bo-view-comment-wrap{ margin:0 0 0 15px; }

.bo-view-comment-list-no-count{ height:300px; padding-top:120px; color:#76b2c6; box-sizing:border-box; text-align:center; }
.bo-view-comment-list-no-count i { font-size:30px; margin-bottom:5px; }
.bo-view-comment-list-info{ height:30px; line-height:30px; }
.bo-view-comment-list{ border:1px solid #dedede; background-color:#f5f8f9; box-sizing:border-box;  }
.comment-list-wrap{ width:100%; display:table; border-bottom:1px solid #dedede; }
.comment-list-wrap ul.col-3{ display:table-cell; }
.comment-list-depth-gap{ width:30px; }
.comment-list-m-icon{ width:66px;  box-sizing:border-box; padding:5px; }

.comment-list-imagine-m-icon{ width:50px; height:50px; line-height:45px; background-color:#323232; border:1px solid #000; border-radius:50%; text-align:center; vertical-align:middle; 
	font-size:20px; color:#ffcc00; }
.comment-list-no-m-icon{ width:50px; height:50px; line-height:45px; background-color:#fff; border:1px solid #dde1e2; border-radius:50%; text-align:center; vertical-align:middle; font-size:20px; color:#999; }
.comment-list-admin-icon{ width:50px; height:50px; line-height:45px; background-color:#fff; border:1px solid #dde1e2; border-radius:50%; text-align:center; vertical-align:middle; font-size:20px; color:#2482ff; }

.comment-list-body{ padding-top:13px; vertical-align:top; }
.comment-list-btn{ width:45px; text-align:center; vertical-align:top; box-sizing:border-box;   }
.clbtn-wrap{ width:35px; height:35px; line-height:35px; position:relative; color:#999; font-size:15px; margin-top:15px; cursor:pointer; }
.clbtn-menu{ width:70px; padding:7px; border:1px solid #dde1e2; top:0; left:-60px; background-color:#fff; position:absolute; display:none; }
.clbtn-menu ul{ height:20px; line-height:20px; }

.clb-wrap-info-date{ font-size:11px; color:#999999; }
.clb-wrap-body{ margin:5px 0 8px; line-height:140%; padding:5px; box-sizing:border-box; }

.comment-modify{ padding:10px; border-bottom:1px solid #dedede;  display:none;  } 
.comment-modify-btn{ padding:5px; }

.comment-comment{ padding:10px; border-bottom:1px solid #dedede; background-color:#f5f5f5; display:none; } 
</STYLE>

<div class="comment-write-wrap-right">

			<div class="form">
				<ul>
					<div class="btn-group radio-form" data-toggle="buttons">
						<label class="btn btn-default active" onclick="bo_mode_ig('off');">
							<input type="radio" name="bo_mode" id="bo_mode2" value="BS" autocomplete="off" checked> 일반
						</label>
						<label class="btn btn-default " onclick="bo_mode_ig('on');">
							<input type="radio" name="bo_mode" id="bo_mode1" value="IG" autocomplete="off" > 가상
						</label>
					</div>
				</ul>
				<ul id="comm_name_wrap" class="display-none">
					<input type="text" name="comm_name" id="comm_write_name"  value="<?=$_view_board_site_name?>" style="width:100px !important;" placeholder="작성자명">
					<!-- <input type="text" name="comm_level" id="comm_level" value="<?=$_view_board_site_name?>" style="width:30px !important; text-align:center !important;"> 레벨 -->
				</ul>
				<ul>
					작성일 : 
					<input type="text" id="comm_date_day" name="comm_date_day" value="<?=$_view_bo_date_ymd?>" style="width:80px; cursor:pointer;" class="text-center" readonly />
					<input type='text' name='comm_date_h' id='comm_date_h' style="width:30px;" value="<?=$_view_bo_date_hh?>" class="text-center"> :
					<input type='text' name='comm_date_s' id='comm_date_s' style="width:30px;" value="<?=$_view_bo_date_ii?>" class="text-center">
					<label><input type="checkbox" name="comm_date_modify" id="comm_date_modify" value="Y"> 작성일 지정</label>
				</ul>
				<ul><textarea name="comm_body" id="comm_body"></textarea></ul>
				<ul class="text-right"><button type="button" id="fast2" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-submit" onclick="commentSubmit();" >댓글등록</button></ul>
			</div>



</div>

<div class="bo-view-comment-wrap">

		<div class="bo-view-comment-list-info position-relative">
			Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page
			<div class="position-absolute" style="top:0; right:0;"><button type="button" id="fast2" class="btnstyle1 btnstyle1-gray btnstyle1-sm" onclick="showComment();" ><i class="fas fa-redo-alt"></i> 새로고침</button></div>
		</div>

		<div class="bo-view-comment-list">
<? if( $total_count == 0 ){ ?>
			<div class="bo-view-comment-list-no-count">
				<i class="far fa-surprise"></i><br>
				댓글이 없습니다.
			</div>
<? }else{ ?>
<?
	while($comment_list = wepix_fetch_array($comment_result)){
		$_view_comm_id = ( $comment_list[COMMENT_MODE] == "IG" ) ? "가상" : $comment_list[COMMENT_ID];
		$_view_comm_body = nl2br($comment_list[COMMENT_BODY]);
?>
				<div class="comment-list-wrap">
<?
	if( $comment_list[DEPTH] > 0 ){ 
?>
				<ul class="col-3 comment-list-depth-gap text-right v-align-top">
					<img src="<?=_A_FOLDER?>/img/comment_reply_icon.png" alt="">
				</ul>
<? } ?>
				<ul class="col-3 comment-list-m-icon">
<? if( $comment_list[COMMENT_MODE] == "IG" ){  ?>
					<div class="comment-list-imagine-m-icon"><i class="fas fa-user-secret"></i></div>
<? }else{ ?>
	<? if( $comment_list[COMMENT_ADMIN] == "Y" ){  ?>
					<div class="comment-list-admin-icon"><i class="fas fa-crown"></i></div>
	<? }else{ ?>
					<div class="comment-list-no-m-icon"><i class="far fa-user"></i></div>
	<? } ?>
<? } ?>
				</ul>
				<ul class="col-3 comment-list-body">
					<div class="clb-wrap">
						<ul class="clb-wrap-info">
							<!-- <span class="clb-wrap-info-level">(<?=$comment_list[comm_level]?>)</span> -->
							<span class="clb-wrap-info-name"><b><?=$comment_list[COMMENT_NAME]?></b></span> | 
							<span class="clb-wrap-info-id"><?=$_view_comm_id?></span> |
							<span class="clb-wrap-info-date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo date("y-m-d H:i", $comment_list[COMMENT_DATE]) ?></span> |
							<span class="clb-wrap-info-date"><?=$comment_list[COMMENT_IP]?></span>
						</ul>
						<!-- <ul class="clb-wrap-like"><?=$comment_body?></ul> -->
						<ul class="clb-wrap-body"><?=$_view_comm_body?></ul>
					</div>
				</ul>
				<ul class="col-3 comment-list-btn">
					<div class="clbtn-wrap">
						<i class="fas fa-ellipsis-v"></i>
						<div class="clbtn-menu">
							<ul onclick="commentComment('<?=$comment_list[COMMENT_IDX]?>')">댓글답변</ul>
							<ul onclick="commentModify('<?=$comment_list[COMMENT_IDX]?>')">댓글수정</ul>
							<ul onclick="commentDel('<?=$comment_list[COMMENT_IDX]?>')">댓글삭제</ul>
						</div>
					</div>
				</ul>
			</div>
			<div class="comment-comment" id="comment_comment_<?=$comment_list[COMMENT_IDX]?>">
				<div>
					<ul>
						<div class="btn-group radio-form" data-toggle="buttons">
							<label class="btn btn-default active" onclick="bo_mode_ig('off', 'reply', '<?=$comment_list[COMMENT_IDX]?>');">
								<input type="radio" name="bo_mode_reply_<?=$comment_list[COMMENT_IDX]?>" id="bo_mode2_reply_<?=$comment_list[COMMENT_IDX]?>" value="BS" autocomplete="off" checked> 일반
							</label>
							<label class="btn btn-default " onclick="bo_mode_ig('on', 'reply', '<?=$comment_list[COMMENT_IDX]?>');">
								<input type="radio" name="bo_mode_reply_<?=$comment_list[COMMENT_IDX]?>" id="bo_mode1_reply_<?=$comment_list[COMMENT_IDX]?>" value="IG" autocomplete="off" > 가상
							</label>
						</div>
					</ul>
					<ul id="comm_name_wrap_reply_<?=$comment_list[COMMENT_IDX]?>" class="m-t-5 display-none">
						<input type="text" name="comm_name" id="comm_write_name_reply_<?=$comment_list[COMMENT_IDX]?>"  value="<?=$_view_board_site_name?>" style="width:100px !important;" placeholder="작성자명">
						<!-- <input type="text" name="comm_level" id="comm_level" value="<?=$_view_board_site_name?>" style="width:30px !important; text-align:center !important;"> 레벨 -->
					</ul>
					<ul class="m-t-5">
						작성일 : 
						<input type="text" name="comm_date_day" id="comm_date_day_reply_<?=$comment_list[COMMENT_IDX]?>"  value="<?=$_view_bo_date_ymd?>" style="width:80px; cursor:pointer;" class="comm-date-day text-center" readonly />
						<input type='text' name='comm_date_h' id='comm_date_h_reply_<?=$comment_list[COMMENT_IDX]?>' style="width:30px;" value="<?=$_view_bo_date_hh?>" class="text-center"> :
						<input type='text' name='comm_date_s' id='comm_date_s_reply_<?=$comment_list[COMMENT_IDX]?>' style="width:30px;" value="<?=$_view_bo_date_ii?>" class="text-center">
						<label><input type="checkbox" name="comm_date_modify_reply_<?=$comment_list[COMMENT_IDX]?>" id="comm_date_modify_reply_<?=$comment_list[COMMENT_IDX]?>" value="Y"> 작성일 지정</label>
					</ul>
				</div>
				<div class="comment-write-wrap m-t-5">
					<ul><textarea name="comm_body_<?=$comment_list[COMMENT_IDX]?>" id="comm_body_<?=$comment_list[COMMENT_IDX]?>"></textarea></ul>
					<ul class="comment-write-btn"><button onclick="commentSubmit('reply','<?=$comment_list[COMMENT_IDX]?>');"> 댓글에 답변  </button></ul>
				</div>
				<div class="comment-modify-btn">
					<button class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="playCancel('comment_comment_<?=$comment_list[COMMENT_IDX]?>');"> <i class="fas fa-redo-alt"></i> 등록취소  </button>
				</div>
			</div>
<? } ?>
<? } //while END ?>

</div>
			<div><?=$_view_paging?></div>
<script type="text/javascript"> 
<!-- 
var pdKey = "<?=$_key?>";

$(function(){
	$(".clbtn-wrap").mouseover(function(e) {
        $("div.clbtn-menu", this).show();
	}).mouseout(function(e) {
        $("div.clbtn-menu", this).hide();
	});

	$("#comm_date_day").datepicker();
	$(".comm-date-day").datepicker();
	
});

function commentComment(key){
	$("#comment_comment_"+key).show();
}

function playCancel(id){
	$("#"+id).hide();
}

//가상모드로 선택시
function bo_mode_ig(val, mode, key) {
	if( mode == "reply" ){
		if( val == "on" ){
			$("#comm_name_wrap_reply_"+ key).removeClass('display-none');
		}else{
			$("#comm_name_wrap_reply_"+ key).addClass('display-none');
		}
	}else{
		if( val == "on" ){
			$("#comm_name_wrap").removeClass('display-none');
		}else{
			$("#comm_name_wrap").addClass('display-none');
		}
	}
}

function commentDel(key){
	
	$.ajax({
		url: "processing.comment.php",
		data: {
			"a_mode":"commDel",
			"ajax_mode":"on",
			"b_code":bCode,
			"b_key":bKey,
			"c_key":key
		},
		type: "POST",
		dataType: "text",
		success: function(getdata){
			
			if (getdata){
				redatawa = getdata.split('|');
				ckcode = redatawa[1];
				ckmsg = redatawa[2];
				if(ckcode == "Processing_Complete"){
					//ajaxLoadingClose(ckmsg);
					showComment();
				}else if(ckcode == "Erorr"){
					$('#modal_alert_msg').html(ckmsg);
					$('#modal-alert').modal({show: true,backdrop:'static'});
/*
					ajaxLoadingErorrClose();

*/
				}else{
					//return false;
				}
			}
		},
		error: function(){
			$('#modal_alert_msg').html('에러');
			$('#modal-alert').modal({show: true,backdrop:'static'});
		}
	});
}

function commentSubmit(mode, key){
	//ajaxLoading();

	if( mode == "modify" ){
		var comm_body = $("#comm_body_"+ key).val();
		var a_mode = "commModify";
		var cKey = key;
		var replyMode = "off";
		var replyKey = "";

	}else if( mode == "reply" ){
		var comm_body = $("#comm_body_"+ key).val();
		var a_mode = "commWrite";
		var cKey = "";
		var replyMode = "on";
		var replyKey = key;
	}else{
		var comm_body = $("#comm_body").val();
		var a_mode = "commWrite";
		var cKey = "";
		var replyMode = "off";
		var replyKey = "";
	}
	
	if( mode == "reply" ){
		var comm_mode = $(':input:radio[name=bo_mode_reply_'+key+']:checked').val();
		var comm_write_name = $('#comm_write_name_reply_'+key).val();
		//var comm_level = $('#comm_level').val();
		var comm_date_modify = $("input:checked[name='comm_date_modify_reply_"+key+"']:checked").val();
		if( comm_date_modify == "undefined") comm_date_modify = "N";
		var comm_date_day = $('#comm_date_day_reply_'+key).val();
		var comm_date_h = $('#comm_date_h_reply_'+key).val();
		var comm_date_s = $('#comm_date_s_reply_'+key).val();
	}else{
		var comm_mode = $(':input:radio[name=bo_mode]:checked').val();
		var comm_write_name = $('#comm_write_name').val();
		//var comm_level = $('#comm_level').val();
		var comm_date_modify = $("input:checked[name='comm_date_modify']:checked").val();
		if( comm_date_modify == "undefined") comm_date_modify = "N";
		var comm_date_day = $('#comm_date_day').val();
		var comm_date_h = $('#comm_date_h').val();
		var comm_date_s = $('#comm_date_s').val();
	}

	if( comm_date_modify == "Y" ){
		if( comm_date_day == "" ){
			$('#modal_alert_msg').html("작성일을 입력해주세요.");
			$('#modal-alert').modal({show: true,backdrop:'static'});
			return false;
		}
		if( comm_date_h == "" ){
			$('#modal_alert_msg').html("작성 시간(시)을 입력해주세요.");
			$('#modal-alert').modal({show: true,backdrop:'static'});
			return false;
		}
		if( comm_date_s == "" ){
			$('#modal_alert_msg').html("작성 시간(분) 입력해주세요.");
			$('#modal-alert').modal({show: true,backdrop:'static'});
			return false;
		}
	}

/*
	if( comm_write_name == "" ){
		$('#modal_alert_msg').html("코멘트 작성자 이름을 입력해주세요.");
		$('#modal-alert').modal({show: true,backdrop:'static'});
		return false;
	}

	if( comm_level == "" ){
		$('#modal_alert_msg').html("코멘트 작성자 레벨을 입력해주세요.");
		$('#modal-alert').modal({show: true,backdrop:'static'});
		return false;
	}
*/
	$.ajax({
		url: "processing.comparison_comment.php",
		data: {
			"a_mode":a_mode,
			"ajax_mode":"on",
			"pd_key":pdKey,
			"c_key":cKey,
			"reply_mode":replyMode,
			"reply_key":replyKey,
			"comm_mode":comm_mode,
			"comm_write_name":comm_write_name,
			"comm_date_modify":comm_date_modify,
			"comm_date_day":comm_date_day,
			"comm_date_h":comm_date_h,
			"comm_date_s":comm_date_s,
			"comm_body":comm_body
		},
		type: "POST",
		dataType: "text",
		success: function(getdata){
			
			if (getdata){
				redatawa = getdata.split('|');
				ckcode = redatawa[1];
				ckmsg = redatawa[2];
				if(ckcode == "Processing_Complete"){
					//ajaxLoadingClose(ckmsg);
					showComment();
/*
					$('#modal_alert_msg').html(ckmsg);
					$('#modal-alert').modal({show: true,backdrop:'static'});
*/
				}else if(ckcode == "Erorr"){
					$('#modal_alert_msg').html(ckmsg);
					$('#modal-alert').modal({show: true,backdrop:'static'});
/*
					ajaxLoadingErorrClose();

*/
				}else{
					//return false;
				}
			}
		},
		error: function(){
			$('#modal_alert_msg').html('에러');
			$('#modal-alert').modal({show: true,backdrop:'static'});
		}
	});

}
//--> 
</script> 