<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\CommentController;
use App\Services\CommentService;

$Comment = new CommentController();

$result = $Comment->commentChatIndex();

// GET/POST 파라미터 초기화
$_mode = $_REQUEST['mode'] ?? '';
$_tidx = $_REQUEST['tidx'] ?? '';

// 세션에서 관리자 정보 가져오기
//$_ad_idx = $_SESSION['ad_idx'] ?? 0;

/*
	echo "<pre>";
	print_r($result['test']);
	echo "</pre>";
*/

$_reaction_icon['Good'] = "👍";
$_reaction_icon['Heart'] = "❤️";
$_reaction_icon['Clapping'] = "👏";
$_reaction_icon['Check'] = "✔️";

$CommentService = new CommentService();
$mentionTarget = $CommentService->getMentionTarget();


?>

<style type="text/css">
	.comment-loading {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: rgba(255, 255, 255, .7);
		text-align: center;
		z-index: 999999999;
		display: none;
		justify-content: center;
		align-items: center;
	}

	.comment-loading>div {
		display: inline-block;
		text-align: center;
	}

	.comment-loading>div>div.text {
		background: rgba(255, 255, 255, .9);
		font-weight: 600;
		font-size: 14px;
		margin-top: 10px;
	}

	.loader {
		display: inline-block;
		width: 50px;
		aspect-ratio: 1;
		border-radius: 50%;
		border: 8px solid;
		border-color: #000 #0000;
		animation: l1 1s infinite;
	}

	@keyframes l1 {
		to {
			transform: rotate(.5turn)
		}
	}

	.mention-list-wrap .mention-selected-remove {
		font-size: 10px;
		width: 16px;
		height: 16px;
		line-height: 1;
		padding: 0;
		margin-left: 1px;
		color: #d93025;
		border: 1px solid #ddd;
		border-radius: 50%;
		background: #fff;
		cursor: pointer;
	}
</style>

<div class="comment-loading">

	<div>
		<div class="loader"></div>
		<div class="text">댓글 등록중입니다. 잠시만 기다려 주세요.</div>
	</div>

</div>

<div class="chat-title">

	<div>
		<ul>
			<span><?= $result['title']['mode'] ?> | <?= $_mode ?>-<?= $_tidx ?></span>
			<? if ($_mode == "log") { ?>
				<button type="button" id="" class="btnstyle1 btnstyle1-xs" onclick="window.open('/admin/work/TaskRequestDetail/<?= $_tidx ?>','_blank');">게시물보기</button>
			<? } ?>
		</ul>
		<ul class="m-t-4"><b><?= $result['title']['name'] ?></b></ul>
	</div>

	<div class="chat-title-menu">
		<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="commentMain.commentViewCheckAll('<?= $_mode ?>','<?= $_tidx ?>');">댓글전부확인</button>
		<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="commentMain.chatLoad('<?= $_mode ?>','<?= $_tidx ?>');"><i class="fas fa-redo"></i> 새로고침</button>
	</div>

</div>
<div class="chat-wrap">

	<div class="chat-list-wrap">
		<?
		foreach ($result['comment'] as $key => $comment) {
			if ($comment['mb_idx'] == $_ad_idx) {
				//if( $comment['mb_idx'] == 16 ){
				$myc = true;
			} else {
				$myc = false;
			}
		?>
			<div class="chat-box clickable-area" data-idx="<?= $comment['idx'] ?>">

				<? if (!$myc) { ?>
					<ul class="chat-profile">
						<div class="comment-mb-profile"><img src="/data/uploads/<?= $comment['ad_image'] ?>" alt=""></div>
						<span style="font-size:11px;"><?= $comment['idx'] ?></span>
					</ul>
				<? } ?>

				<ul class="chat-body <? if ($myc) echo "myc" ?>">
					<div class="comment-box">
						<? if ($comment['is_reply']) { ?>
							<div class="reply-box">
								<ul><?= $comment['reply_data']['name'] ?></ul>
								<ul><?= $comment['reply_data']['summary'] ?></ul>
							</div>
						<? } ?>
						<div>
							<span style="font-weight:600" id='comment_name_<?= $comment['idx'] ?>'><?= $comment['ad_nick'] ?></span>
							<span style="font-size:11px;"><?= date("y.m.d H:i", strtotime($comment['reg_date'])) ?> | <?= $comment['state'] ?></span>
						</div>
						<div class="m-t-5 comment-body" id='comment_body_<?= $comment['idx'] ?>'>
							<?php
								$commentText = nl2br(htmlspecialchars($comment['comment'] ?? '', ENT_QUOTES, 'UTF-8'));
								$commentText = preg_replace(
									'~(https?://[^\s<]+)~i',
									'<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
									$commentText
								);
							?>
							<?= $commentText ?>
						</div>

						<? if (!empty($comment['mention'])) { ?>
							<div class="m-t-5">
								<?
								foreach ($comment['mention'] as $mention) {
								?>
									<div class="mention-unit">
										@<?= $mention['name'] ?>
										<span id="mention_viewCheck_<?= $comment['idx'] ?>_<?= $mention['mb_idx'] ?>">
											<? if ($mention['mb_idx'] == $_ad_idx) { ?>
												<? if (!$mention['viewCheck']) { ?>
													<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-xs" onclick="commentMain.commentviewCheck('<?= $_mode ?>', '<?= $comment['idx'] ?>')">댓글확인처리</button>
											<?
												} else {
													echo ": 확인 <span class='viewCheck-date'>(" . date("y.m.d H:i", strtotime($mention['viewCheckDate'])) . ")</span>";
												}
											} else {
												if ($mention['viewCheck']) {
													echo ": 확인 <span class='viewCheck-date'>(" . date("y.m.d H:i", strtotime($mention['viewCheckDate'])) . ")</span>";
												} else {
													echo ": 미확인";
												}
											} ?>
										</span>
									</div>
								<? } ?>
							</div>
						<? } ?>

						<? if (!empty($comment['reaction'])) { ?>
							<div class="m-t-5">
								<?
								foreach ($comment['reaction'] as $reaction) {
								?>
									<div class="reaction-unit">
										<?= $_reaction_icon[$reaction['mode']] ?> <?= $reaction['mb_name'] ?>
									</div>
								<? } ?>
							</div>
						<? } ?>

					</div>

					<!-- 커스텀 컨텍스트 메뉴 hidden -->
					<div id="" class="context-menu hidden">
						<ul>
							<li onclick="replyOn('<?= $comment['idx'] ?>','<?= $comment['mb_idx'] ?>')">답장하기</li>
							<!-- <li id="">리액션(작업중)</li> -->
							<li onclick="commentMain.commentReaction('<?= $_mode ?>', '<?= $_tidx ?>', '<?= $comment['idx'] ?>','Good');"><?= $_reaction_icon['Good'] ?></li>
							<li onclick="commentMain.commentReaction('<?= $_mode ?>', '<?= $_tidx ?>', '<?= $comment['idx'] ?>','Heart');"><?= $_reaction_icon['Heart'] ?></li>
							<li onclick="commentMain.commentReaction('<?= $_mode ?>', '<?= $_tidx ?>', '<?= $comment['idx'] ?>','Clapping');"><?= $_reaction_icon['Clapping'] ?></li>
							<li onclick="commentMain.commentReaction('<?= $_mode ?>', '<?= $_tidx ?>', '<?= $comment['idx'] ?>','Check');"><?= $_reaction_icon['Check'] ?></li>
						</ul>
					</div>

				</ul>
			</div>
		<?
		}
		?>
	</div>

</div>
<form id="form_comment">
	<input type="hidden" name="mode" value="<?= $_mode ?>">
	<input type="hidden" name="tidx" value="<?= $_tidx ?>">

	<input type="hidden" name="reply_idx" id="reply_idx">
	<input type="hidden" name="reply_mb_idx" id="reply_mb_idx">
	<input type="hidden" name="reply_name" id="reply_name">
	<input type="hidden" name="reply_summary" id="reply_summary">

	<div class="mention-wrap">
		<div id="reply_mode_wrap" class="reply-mode hidden">
			<ul>
				<p><i class="fas fa-reply"></i></p>
				<p>답변</p>
			</ul>
			<ul>
				<p id="reply_name_text">답변자명</p>
				<p id="reply_summary_text">답변</p>
			</ul>
			<ul onclick="replyOff()">
				<i class="fas fa-times"></i>
			</ul>
		</div>
		<div class="mention-list">
			<button type="button" class="mention-close" aria-label="멘션 목록 닫기">&times;</button>
			<?
			foreach ($mentionTarget as $member) {
			?>
				<label><input type="checkbox" name="target_mb_idx[]" value="<?= $member['idx'] ?>"> <?= $member['ad_name'] ?></label>
			<? } ?>
		</div>
	</div>

	<div id="mention_list" class="mention-list-wrap"></div>
	<div class="write-wrap">
		<ul><textarea name="comment" id="comment" style="width:100%; height:70px;" placeholder="댓글입력"></textarea></ul>
		<ul>
			<button type="button" id="mentionBtn" class="btnstyle1 btnstyle1-xs" onclick="">@멘션</button>
			<button type="button" id="chatBtn" class="btnstyle1 btnstyle1-primary  m-t-3" onclick="commentMain.createComment()">댓글입력</button>
		</ul>
	</div>
</form>

<script type="text/javascript">
	//답장활성화
	function replyOn(idx, mb_idx) {

		var reply_name = $("#comment_name_" + idx).text();
		var reply_summary = $("#comment_body_" + idx).text();

		$("#reply_idx").val(idx);
		$("#reply_mb_idx").val(mb_idx);
		$("#reply_name").val(reply_name);
		$("#reply_summary").val(reply_summary);
		$("#reply_name_text").html(reply_name);
		$("#reply_summary_text").html(reply_summary);
		$("#reply_mode_wrap").removeClass("hidden");

	}

	function replyOff() {

		$("#reply_idx").val("");
		$("#reply_mb_idx").val("");
		$("#reply_name").val("");
		$("#reply_summary").val("");
		$("#reply_name_text").html("");
		$("#reply_summary_text").html("");
		$("#reply_mode_wrap").addClass("hidden");

	}

	// chatWrap 변수와 scrollToBottom 함수 정의
	var chatWrap = null;

	function scrollToBottom() {
		if (chatWrap && chatWrap.scrollHeight !== undefined) {
			chatWrap.scrollTop = chatWrap.scrollHeight;
		}
	}

	$(function() {

		chatWrap = document.querySelector('.chat-wrap');
		scrollToBottom();

		const $mentionBtn = $('#mentionBtn');
		const $mentionList = $('.mention-list');
		const $mentionClose = $('.mention-close');
		const $mentionSelectedList = $('#mention_list');
		let isVisible = false;

		function renderMentionSelected() {
			const items = [];
			$mentionList.find('input[name="target_mb_idx[]"]:checked').each(function() {
				const $input = $(this);
				const mbIdx = $input.val();
				const name = $.trim($input.closest('label').text());
				items.push({
					mbIdx,
					name
				});
			});

			$mentionSelectedList.empty();
			if (!items.length) {
				return;
			}

			items.forEach(({
				mbIdx,
				name
			}) => {
				const $li = $('<li>')
					.addClass('mention-selected-item')
					.attr('data-mb-idx', mbIdx)
					.text('@' + name + ' ');
				const $remove = $('<button>')
					.attr('type', 'button')
					.addClass('mention-selected-remove')
					.attr('aria-label', name + ' 멘션 삭제')
					.html('&times;');
				$li.append($remove);
				$mentionSelectedList.append($li);
			});
		}

		function setMentionVisible(visible) {
			if (visible) {
				$mentionList.css('display', 'grid').addClass('active');
			} else {
				$mentionList.removeClass('active');
				$mentionList.css('display', 'none');
			}
			isVisible = visible;
		}

		$mentionBtn.click(function() {
			setMentionVisible(!isVisible);
		});

		$mentionClose.click(function() {
			setMentionVisible(false);
		});

		$mentionList.on('change', 'input[name="target_mb_idx[]"]', function() {
			renderMentionSelected();
		});

		$mentionSelectedList.on('click', '.mention-selected-remove', function() {
			const mbIdx = $(this).closest('li').data('mb-idx');
			$mentionList
				.find('input[name="target_mb_idx[]"][value="' + mbIdx + '"]')
				.prop('checked', false);
			renderMentionSelected();
		});

		$(document).on('click', function(event) {
			if (!isVisible) {
				return;
			}
			const $target = $(event.target);
			if ($target.closest('.mention-list').length || $target.closest('#mentionBtn').length) {
				return;
			}
			setMentionVisible(false);
		});

		$(document).on('keydown', function(event) {
			if (isVisible && event.key === 'Escape') {
				setMentionVisible(false);
			}
		});

	});

	$(document).ready(function() {

		let currentIdx = null; // 현재 우클릭한 div의 data-idx 값 저장
		let lastClickedBox = null; // 방금 우클릭한 .chat-box > ul.chat-body .comment-box

		// 우클릭 이벤트 바인딩
		$(document).on("contextmenu", ".chat-box > ul.chat-body .comment-box", function(event) {
			event.preventDefault(); // 기본 우클릭 메뉴 비활성화

			// 현재 클릭한 요소의 data-idx 값 저장
			const $chatBox = $(this).closest(".chat-box"); // 현재 클릭한 .chat-box
			const $chatBody = $chatBox.find(".chat-body"); // 해당 .chat-box 안의 .chat-body
			const $contextMenu = $chatBox.find(".context-menu"); // 해당 .chat-box 안의 .context-menu

			currentIdx = $chatBox.data("idx"); // .chat-box의 data-idx 값 저장
			lastClickedBox = $chatBox; // 현재 우클릭한 .chat-box 저장

			// 다른 메뉴 숨기기 및 contextOn 클래스 제거
			$(".context-menu").addClass("hidden");
			$(".chat-body").removeClass("contextOn");

			// 메뉴 표시 및 contextOn 클래스 추가
			$contextMenu.removeClass("hidden");
			$chatBody.addClass("contextOn");

			// 메뉴의 위치를 우클릭한 위치로 설정 (필요시 추가)
			/*
			$contextMenu.css({
			  top: `${event.clientY}px`,
			  left: `${event.clientX}px`,
			});
			*/
		});

		// 메뉴 항목 클릭 이벤트
		/*
		  $(document).on("click", ".context-menu ul li", function () {
		    const action = $(this).text();
		    const chatBoxIdx = $(this).closest(".chat-box").data("idx");
		    alert(`Action: ${action}, chat-box ID: ${chatBoxIdx}`);

		    // 메뉴 숨기기 및 contextOn 클래스 제거
		    $(this).closest(".context-menu").addClass("hidden");
		    $(this).closest(".chat-box").find(".chat-body").removeClass("contextOn");
		    lastClickedBox = null; // 초기화
		  });
		*/

		// 메뉴 외부를 클릭하면 메뉴 숨기기 및 contextOn 클래스 제거
		$(document).on("click", function(event) {
			if (
				lastClickedBox && // 이전에 우클릭한 .chat-box가 있어야 함
				!$(event.target).closest(".chat-box > ul.chat-body .comment-box").length // 현재 클릭한 위치가 지정된 우클릭 영역 외부인지 확인
			) {
				// 해당 .context-menu 숨기기
				lastClickedBox.find(".context-menu").addClass("hidden");
				// 해당 .chat-body에서 contextOn 클래스 제거
				lastClickedBox.find(".chat-body").removeClass("contextOn");
				lastClickedBox = null; // 초기화
			}
		});
	});
</script>