<?
$pageGroup = "board";
$pageName = "board_view";

	include "../lib/inc_common.php";
	include 'board_inc.php';

	$bo_data =  wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD." where UID = '".$_b_key."'"));
	
	$_view_witer_id = $bo_data[BOARD_WITER_ID];
	$_view_witer_name = $bo_data[BOARD_WITER_NAME];
	$_view_category = $_ary_bc_category[$bo_data[BOARD_CATEGORY]];
	$_view_mode = $_bo_gv_mode[$bo_data[BOARD_MODE]];
	$_view_board_date = date("Y-m-d H:i", $bo_data[BOARD_DATE]);
	$_view_hit = $bo_data[BOARD_HIT];
	$_view_board_subject = $bo_data[BOARD_SUBJECT];
	$_view_board_body = nl2br($bo_data[BOARD_BODY]);
	$_view_board_comment = nl2br($bo_data[BOARD_COMMENT]);
	$_view_board_view_thumbnail = $bo_data[BOARD_THUMBNAIL];
	$_view_modify_date = ( $bo_data[BOARD_MODIFY_DATE] > 0 ) ? " | 최종수정 : ".date("Y-m-d H:i", $bo_data[BOARD_MODIFY_DATE])." (".$bo_data[BOARD_MODIFY_ID].")" : "";
	//$_view_board_ip = ( $bo_data[BOARD_IP] ) ? "IP : <b>".$bo_data[BOARD_IP]."</b>" : "";
	$_view_board_ip = $bo_data[BOARD_IP];
	$_view_board_ip_show = $bo_data[BOARD_IP_SHOW];

	$_return_url = securityVal($returnUrl);
	if( $_return_url ){
		$_back_url = $_return_url;
	}else{
		$_back_url = _A_PATH_BOARD_LIST."?b_code=".$_b_code;
	}

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.board-body{
	height:300px !important;
	padding:10px !important;
	box-sizing:border-box; 
	vertical-align:top;
}
</STYLE>
<div id="contents_head">
	<h1>게시판 보기 (<?=$_view_bc_name?>) </h1>
	<div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_BOARD_REG?>?b_code=<?=$_b_code?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="full-wrap">

            <form name='formN1' action='board_ok.php'>
            <input type='hidden' name='action_mode'>
            <input type='hidden' name='uid' value='<?=$b_key?>'>
            <input type='hidden' name='b_code' value='<?=$b_code?>'>

			<table cellspacing="1px" cellpadding="0" border="0" class="table-style2 board-view">
<?
//카테고리 사용시
if( $_show_bc_category_active == "Y" ){
?>
				<tr>
					<th>분류</th>
					<td ><?=$_view_category?></td>
				</tr>
<? } ?>
				<tr>
					<th>모드</th>
					<td ><?=$_view_mode?></td>
				</tr>
				<tr>
					<th>정보</th>
					<td >
						<div class="display-table">
							<ul id="board_witer_name_wrap" class="display-table-cell p-r-50" >
								작성자 : <b><?=$_view_witer_name?> (<?=$_view_witer_id?>)</b>
							</ul>
							<ul class="display-table-cell p-r-50">
								작성일 : <b><?=$_view_board_date?></b>
								<?=$_view_modify_date?>
							</ul>
							<ul class="display-table-cell p-r-50">
								조회수 : <b><?=$_view_hit?></b>
							</ul>



							<ul class="display-table-cell p-r-50">
								IP : <b><?=$_view_board_ip?></b>

<?
if( is_ipv6($_view_board_ip)==true ){
	echo is_ipv6($_view_board_ip);
}
?>

							</ul>
							<ul class="display-table-cell p-r-50">
								비회원 노출 IP : <b><?=$_view_board_ip_show?></b>
							</ul>
						</div>
					</td>
				</tr>

				<tr>
                    <th>제목</th>
					<td  class="bold"><?=$_view_board_subject?></td>
				</tr>
            </table>
            </form>

<STYLE TYPE="text/css">
	.bo-view-tab{ }
	.bo-view-tab .nav-tabs { border-bottom-color:#b3b3b3 !important;  }
	.bo-view-tab .nav-tabs li {  }
	.bo-view-tab .nav-tabs li a{ width:150px !important; text-align:center !important; background-color:#eeeeee !important;
		border-color:#b3b3b3 !important; 
	}
	.bo-view-tab .nav-tabs li.active a{ background-color:#fff !important; font-weight:bold !important; color:#007ae6 !important; border-bottom-color:#fff !important;  }
	.bo-view-body,
	.bo-view-comment,
	.bo-view-check{ padding:10px; box-sizing:border-box; }
</STYLE>

		<div role="tabpanel" class="bo-view-tab m-t-10">
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#bo_body" aria-controls="bo_body" role="tab" data-toggle="tab">본문</a></li>
				<li role="presentation" id="bo_comment_wrap"><a href="#bo_comment" aria-controls="bo_comment" role="tab" data-toggle="tab">댓글 ( <b><?=number_format($_view_board_comment) ?></b> 건 )</a></li>
<?
//읽음확인 기능
if( $_show_bc_view_check_active == "Y" ){
?>
				<li role="presentation" id="bo_view_check_wrap"><a href="#bo_view_check_tap" aria-controls="bo_view_check_tap" role="tab" data-toggle="tab">읽음확인</a></li>
<? } ?>

<?
//썸네일 있을경우
if( $_view_board_view_thumbnail ){
?>
				<li role="presentation" id="bo_view_thumbnail"><a href="#bo_view_thumbnail_tap" aria-controls="bo_view_thumbnail_tap" role="tab" data-toggle="tab">썸네일</a></li>
<? } ?>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active " id="bo_body">
					<div class="bo-view-body">
					<?=$_view_board_body?>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane fade " id="bo_comment">
					<div class="bo-view-comment" id="comment">
					</div>
				</div>
<?
//읽음확인 기능
if( $_show_bc_view_check_active == "Y" ){
?>
				<div role="tabpanel" class="tab-pane fade " id="bo_view_check_tap">
					<div class="bo-view-check" id="view_check">
					</div>
				</div>
<? } ?>

<?
//썸네일 있을경우
if( $_view_board_view_thumbnail ){
?>
				<div role="tabpanel" class="tab-pane fade " id="bo_view_thumbnail_tap">
					<div class="bo-view-thumbnail" id="view_thumbnail">
						<img src="<?=$_view_board_view_thumbnail?>" alt="">
					</div>
				</div>
<? } ?>
		  </div>
		</div>
	</div>

		<div class="submitBtnWrap">
			<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=$_back_url?>'" > 
				<i class="fas fa-list"></i>
				목록
			</button>
			<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_BOARD_REG?>?b_mode=modify&b_code=<?=$_b_code?>&b_key=<?=$_b_key?>'"  > 
				<i class="fas fa-hammer"></i>
				수정
			</button>
			<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="boardDel()" > 
				<i class="fas fa-trash-alt"></i>
				삭제
			</button>

                <?
				/*
                if( $show_answer != "N" ){
                    if($coment_count == 0){
                ?> 
                  <input type='button' value='답변 등록' onclick="doAnswer('new_answer');" class="listBtn">
                  <?}else{?>
                    <input type='button' value='답변 수정' onclick="doAnswer('modify_answer');" class="listBtn">
                  <?}?>
                <?}
				*/
				?>
                
		</div>

</div>

<script type="text/javascript"> 
<!-- 
var bCode = "<?=$_b_code?>";
var bKey = "<?=$_b_key?>";

$(function(){
 
<?
//읽음확인 기능
if( $_show_bc_view_check_active == "Y" ){
?>
    $('#bo_view_check_wrap a').click(function(){
		showViewCheck();
    });
<? } ?>

    $('#bo_comment_wrap a').click(function(){
		showComment();
    });
});


function showComment(pn){
	if( pn== "" ) pn ="";
	$.ajax({
		url: "ajax.comment.php",
		data: {
			"b_code":bCode,
			"b_key":bKey,
			"pn":pn
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#comment').html(getdata);
		},
		error: function(){
		}
	});
}
//showComment();

function showViewCheck(cPage){
	if( cPage== "" ) cPage ="";
	$.ajax({
		url: "ajax.view_check.php",
		data: {
			"b_code":bCode,
			"b_key":bKey,
			"c_page":cPage
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#view_check').html(getdata);
		},
		error: function(){
		}
	});
}


function boardDel(){

	$('#modal_alert_title').html('<i class="fas fa-exclamation-circle"></i> 경고');
	$('#modal_alert_msg').html(
		"해당 게시물을 삭제합니다<br>"
		+ "삭제후 데이터는 복구 되지 않습니다.<br>"
		+ "댓글이 있을경우 댓글도 삭제됩니다.<br>"
		+ "<br>정말 삭제하시겠습니까?'"
	);
	$("#modal_footer_btn_wrap").addClass('display-none');
	$("#modal_footer_confirm_btn_wrap").removeClass('display-none');
	$('#modal-alert').modal({show: true,backdrop:'static'});

	$("#confirm-ok").on("click", function(){
		location.href="<?=_A_PATH_BOARD_OK_NEW?>?a_mode=boardDel&b_code="+ bCode +"&b_key="+ bKey;
		/*
		callback(true);
		$("#modal-alert").modal('hide');
		*/
	});

/*
	if(confirm('해당 게시물을 삭제합니다\n삭제후 데이터는 복구 되지 않습니다.\n정말 삭제하시겠습니까?')){

	}
*/
}
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>