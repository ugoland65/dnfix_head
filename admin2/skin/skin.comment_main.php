<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\CommentController;

$Comment = new CommentController();

$result = $Comment->commentMainIndex();

/*
	echo "<pre>";
	print_r($result);
	echo "</pre>";
*/

//달력 데이터를 생성했다면 idx를 생성한 idx로 대체해준다
if( empty($_idx) && !empty($result['CalendarInsert']['idx']) ){
	$_idx = $result['CalendarInsert']['idx'];
}


?>
<style type="text/css">




.cm-wrap{ height:100%; min-height:650px; display:flex; overflow:hidden; border:1px solid #ddd; }
.cm-wrap > ul{

	&:nth-child(1){
		width:350px;
		border-right:1px solid #ddd;
	}
	&:nth-child(2){
		flex:1;
		position: relative;
	}
}

.chat-my-list-title{ height:49px; border-bottom:1px solid #bbb; }

.chat-my-list-wrap{ width:100%; height:600px; overflow-y: scroll;  padding:10px; box-sizing:border-box; }
.chat-my-list-wrap::-webkit-scrollbar{ width:5px; height:5px; border-left:solid 1px rgba(255,255,255,.1)}
.chat-my-list-wrap::-webkit-scrollbar-thumb{  background:#aaa;    }

.chat-my-list-wrap > div{ display:flex; flex-direction: column; gap:6px; }
.chat-my-list-wrap > div > div{ 
	display:flex; align-items: center; width:100%; background:#f5f5f5; border: 1px solid #f1f1f1;  border-radius:5px; padding:10px; cursor:pointer; 

	&:hover{
		background:#eee;
		border: 1px solid #e1e1e1;
	}
}

.chat-my-list-box.active{ 
	background-color: #9ef7ff;
    border: 1px solid #24d3e3; 

	&:hover{
		background:#9ef7ff;
	}

}

.chat-my-list-wrap > div > div > ul{
	&:nth-child(1){
		width:50px;

		div{
			display:inline-block;
			width:45px;
			height:45px;
			border-radius:50%;
			background:#eee;
			border:1px solid #aaa;
			vertical-align:middle;
			overflow:hidden;

			img{ width:100%; }
		}

	}
	&:nth-child(2){
		flex:1;
		p.mode-text{ font-size:11px; color:#0969da; }
		p.subject{ font-size:13px; margin-top:3px; color:#000;
			width:250px;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis; 
		}
		p.comment{ font-size:12px; margin-top:3px; color:#555;
			width: 250px;
			overflow: hidden;
			text-overflow: ellipsis;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
		}
		p.date{ font-size:12px; margin-top:3px; color:#222;
		}
	}
}


.chat-title{ position: relative; width:100%; height:49px; border-bottom:1px solid #bbb; padding:6px 0 0 15px; box-sizing:border-box; }
.chat-title b{ font-size: 15px; font-weight:600; }
.chat-title-menu{ position: absolute; top:10px; right:10px; }

.chat-wrap{ width:100%; height:100%; max-height:490px; padding:15px 80px 15px 40px; background-color: #ddd; overflow-y: scroll; }
.chat-wrap::-webkit-scrollbar{ width:5px; height:5px; border-left:solid 1px rgba(255,255,255,.1)}
.chat-wrap::-webkit-scrollbar-thumb{  background:#aaa;   }

.write-wrap{ width:100%; height:110px; display:flex; gap:7px; padding:25px 15px 0; background-color: #ddd; }
.write-wrap > ul{
	&:nth-child(1){
		flex:1;
	}
	&:nth-child(2){
		width:90px;
	}
}

.mention-wrap{ position: relative; }

.reply-mode{ position: absolute; display:flex; align-items: center; height:50px; top: -35px; left:15px; width:calc(100% - 40px); padding:10px; border-radius:5px; background:rgba(255,255,255,.8);
	border:1px solid #ccc; }
.reply-mode > ul{
	&:nth-child(1){
		width:50px;
		text-align: center;
		border-right:1px solid #ddd;

	}
	&:nth-child(2){
		flex:1;
		padding-left:10px;

		p#reply_name_text{ font-size:13px; color:#111; }
		p#reply_summary_text{ font-size:12px; margin-top:3px; color:#555;
			width: 100%;
			overflow: hidden;
			text-overflow: ellipsis;
			display: -webkit-box;
			-webkit-line-clamp: 1;
			-webkit-box-orient: vertical;
		}

	}
	&:nth-child(3){
		width:50px;
		text-align: center;
		border-left:1px solid #ddd;
		cursor:pointer;
		i {
			font-size: 17px;
		}
	}
}

.mention-list{ position: absolute; top: -40px; left:15px; width:calc(100% - 40px); padding:10px; border-radius:5px; background:rgba(0,0,0,.8);
    display: none; /* 초기 상태는 숨김 */
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease;
}
.mention-list.active {
    display: grid;
	grid-template-columns: repeat(7, 1fr); /* 한 줄에 8개씩 */
	gap: 8px; /* 요소 간의 간격 */
    opacity: 1;
    transform: translateY(0);
}

.mention-list label{ color:#fff; }

.write-wrap textarea{ background:#fff; }

.chat-list-wrap{ display:flex; flex-direction: column; gap:6px; }

.chat-box{ display:table; }
.chat-box > ul{ display:table-cell; vertical-align:top; }
.chat-box > ul.chat-profile{ width:50px;  }
.chat-box > ul.chat-body{ position: relative; }
.chat-box > ul.chat-body.myc{ text-align:right; }

.context-menu {
  position: absolute;
  background-color: #fff;
  border: 1px solid #ccc;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  width: 150px;
  z-index: 9999999999;
  top:10px;
  left:80px;
}

.chat-box > ul.chat-body.myc .context-menu{
  left:auto;
  right:80px;
}

.context-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.context-menu ul li {
  padding: 10px;
  cursor: pointer;
  text-align: center;
  border-bottom: 1px solid #ccc;
}

.context-menu ul li:last-child {
  border-bottom: none;
}

.context-menu ul li:hover {
  background-color: #007BFF;
  color: #fff;
}


.comment-box{ position: relative; display:inline-block; background-color:#fff; border:1px solid #ccc; padding:10px 15px;  border-radius:10px;  }
.chat-box > ul.chat-body.myc .comment-box{  background-color:#ffc; }
.chat-box > ul.chat-body.contextOn .comment-box{  background-color: #9ef7ff; border: 1px solid #24d3e3;  }

.comment-box:after {
	content: '';
	position: absolute;
	left: 0;
	top: 18px;
	width: 0;
	height: 0;
	border: 8px solid transparent;
	border-right-color: #fff;
	border-left: 0;
	border-top: 0;
	margin-top: -5px;
	margin-left: -8px;
}

.chat-box > ul.chat-body.myc .comment-box:after {
	left: auto;
	right: 0;
	border: 8px solid transparent;
	border-left-color: #ffc;
	border-right: 0;
	border-top: 0;
	margin-top: -5px;
	margin-right: -8px;
}

.chat-box > ul.chat-body.contextOn .comment-box:after {
	display:none;
	left: auto;
	right: 0;
	border: 8px solid transparent;
	border-left-color: #9ef7ff;
	border-right: 0;
	border-top: 0;
	margin-top: -5px;
	margin-right: -8px;
}

.comment-box .reply-box{
	padding:5px 5px 5px 8px;
	margin-bottom:5px;
	border-left:3px solid #5788ca;
	background:rgba(0,0,0,.08);
}
.comment-box .reply-box > ul{ font-size: 12px; color:#666; }

.comment-box .comment-body{
    overflow-wrap: break-word; /* 단어 단위 줄바꿈 */
    word-wrap: break-word; /* 단어 단위 줄바꿈 (IE 지원) */
    box-sizing: border-box; /* 박스 크기 계산 */
	max-width:410px;
}

.comment-mb-profile{ display:inline-block; width:40px; height:40px; border:1px solid #999; overflow:hidden; border-radius:50%; }
.comment-mb-profile img{ width:100%; }

.mention-unit{ display:inline-block; background-color:#f7f7f7; border:1px solid #bbb; padding:5px;border-radius:5px; font-size:12px;  }
.reaction-unit{  display:inline-block; background-color:#fff; border:1px solid #e1e1e1; padding:5px;border-radius:5px; font-size:12px;  }

.chat-body-empty{ width:100%; text-align:center; padding:200px 0; }
</style>
<div class="cm-wrap">
	<ul>
		<div class="chat-my-list-title">
		</div>
		<div class="chat-my-list-wrap" id="chat_my_list_wrap">

		</div>
	</ul>
	<ul id="chat_body">

		<div class="chat-body-empty">
			좌측에서 댓글을 선택해 주세요.
		</div>

	</ul>
</div>

<?
/*
	echo "<pre>";
	print_r($result);
	echo "</pre>";
*/
?>

<script type="text/javascript"> 
<!-- 
var chatWrap = document.querySelector('.chat-wrap');

function scrollToBottom() {
	chatWrap.scrollTop = chatWrap.scrollHeight;
}

var commentMain = (function() {

	const API_ENDPOINTS = {
		createComment: "/ad/proc/Admin/Comment/createComment",
		commentViewCheck: "/ad/proc/Admin/Comment/commentViewCheck",
		commentViewCheckAll: "/ad/proc/Admin/Comment/commentViewCheckAll",
		commentReaction: "/ad/proc/Admin/Comment/commentReaction",
	};

	const _mode = '<?=$_mode?>';
	const _tidx = '<?=$_idx?>';

	return {

		// 초기화
		init() {
			console.log('wishList module initialized.');
		},

		//쳇 리스트 로드
		chatListLoad(mode, tidx) {

			ajaxRequest("/ad/ajax/comment_list", { mode, tidx }, { dataType: 'html' })
				.then((getdata) => {
					$('#chat_my_list_wrap').html(getdata); // 데이터 삽입
				})
				.catch((error) => {
					dnAlert('Error', '에러', 'red');
				});
			
		},

		chatLoad(mode, tidx) {

			ajaxRequest("/ad/ajax/comment_chat", { mode, tidx }, { dataType: 'html' })
				.then((getdata) => {
					$('#chat_body').html(getdata); // 데이터 삽입
				})
				.catch((error) => {
					dnAlert('Error', '에러', 'red');
				});
			
		},

		commentviewCheck(mode, tidx){

			ajaxRequest(API_ENDPOINTS.commentViewCheck, { mode, tidx })
				.done(res => {
					if (res.status === "success") {
						/*
						this.chatLoad(_mode, _tidx);
						scrollToBottom();
						*/
						$("#mention_viewCheck_"+ tidx +"_"+ res.return_mb_idx ).html(": 확인 <span class='viewCheck-date'>("+ res.return_time +")</span>");
					} else {
						dnAlert('Error', res.message || '상태 변경 실패', 'red'); // 실패 시 메시지 표시
					}
				})
				.catch(error => {
					dnAlert('Error', '상태 변경 실패', 'red');
					throw new Error('AJAX 요청 실패');
				});

		},

		commentViewCheckAll(mode, tidx){

			ajaxRequest(API_ENDPOINTS.commentViewCheckAll, { mode, tidx })
				.done(res => {
					if (res.status === "success") {
						this.chatLoad(mode, tidx);
						this.chatListLoad(mode, tidx);
					} else {
						dnAlert('Error', res.message || '상태 변경 실패', 'red'); // 실패 시 메시지 표시
					}
				})
				.catch(error => {
					dnAlert('Error', '상태 변경 실패', 'red');
					throw new Error('AJAX 요청 실패');
				});

		},

		createComment() {

			$('.comment-loading').css({"display":"flex"});

			var form = $('#form_comment')[0];
			var formData = new FormData(form);


			ajaxRequest(API_ENDPOINTS.createComment, formData, { 
				processData: false, 
				contentType: false 
			})
			.done(res => {
				if (res.status === "success") {
					this.chatLoad(res.mode, res.tidx);
					this.chatListLoad(res.mode, res.tidx);
					$('#comment').val(''); // 내용 초기화
					scrollToBottom();
				} else {
					dnAlert('Error', res.message || '상태 변경 실패', 'red'); // 실패 시 메시지 표시
				}
			})
			.catch(error => {
				dnAlert('Error', '상태 변경 실패', 'red');
				throw new Error('AJAX 요청 실패');
			})
			.always(() => {
				$('.comment-loading').css({"display":"none"});
			});
		},

		commentReaction( mode, tidx, idx, reaction_mode ) {
		
			ajaxRequest(API_ENDPOINTS.commentReaction, { mode, tidx, idx, reaction_mode })
				.done(res => {
					if (res.status === "success") {
						this.chatLoad(mode, tidx);
						this.chatListLoad(mode, tidx);
					} else {
						dnAlert('Error', res.message || '상태 변경 실패', 'red'); // 실패 시 메시지 표시
					}
				})
				.catch(error => {
					dnAlert('Error', '상태 변경 실패', 'red');
					throw new Error('AJAX 요청 실패');
				});

		}


	}

})();

commentMain.chatListLoad('<?=$_mode?>','<?=$_idx?>');

<? if( !empty($_mode) && !empty($_idx) ){ ?>
commentMain.chatLoad('<?=$_mode?>','<?=$_idx?>');
<? } ?>

$(function(){

	scrollToBottom();

    const $mentionBtn = $('#mentionBtn');
    const $mentionList = $('.mention-list');
    let isVisible = false;

    $mentionBtn.click(function() {
        if (!isVisible) {
            $mentionList.css('display', 'grid').addClass('active');
        } else {
            $mentionList.removeClass('active');
            $mentionList.css('display', 'none');
        }
        isVisible = !isVisible;
    });

});
//--> 
</script> 