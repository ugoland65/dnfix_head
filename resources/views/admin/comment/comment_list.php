<div class="chat-my-list-box-wrap">
<?
	foreach ( $commentList as $list ){
?>
	<div class="chat-my-list-box" data-mode="<?=$list['mode']?>" data-tidx="<?=$list['tidx']?>">
		<ul>
			<div><img src="/data/uploads/<?=$list['ad_image']?>" alt=""></div>
		</ul>
		<ul>
			<p class="mode-text"><?=$list['target']['mode_text']?></p>
			<p class="subject"><?=$list['target']['subject']?></p>
			<p class="comment"><?=$list['comment']?></p>
			<p class="date"><?=$list['ad_name']?> | <?=date('y.m.d H:i',strtotime($list['reg_date']))?></p>
		</ul>
	</div>
<? } 
?>
</div>

<script> 
$(function(){

	$(".chat-my-list-box").click(function(){
		$(".chat-my-list-box-wrap > div.chat-my-list-box").removeClass('active');
		$(this).addClass('active');
		commentMain.chatLoad($(this).data('mode'),$(this).data('tidx'));
	});

});
</script> 