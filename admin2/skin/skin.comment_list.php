<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\CommentController;

$Comment = new CommentController(); 

$result = $Comment->commentListIndex();

/*
	echo "<pre>";
	print_r($result);
	echo "</pre>";
*/
?>

<div class="chat-my-list-box-wrap">
<?

	foreach ( $result['list'] as $list ){
?>
	<?
/*
	echo "<pre>";
	print_r($list);
	echo "</pre>";
*/
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

<script type="text/javascript"> 
<!-- 
$(function(){

	$(".chat-my-list-box").click(function(){
		$(".chat-my-list-box-wrap > div.chat-my-list-box").removeClass('active');
		$(this).addClass('active');
		commentMain.chatLoad($(this).data('mode'),$(this).data('tidx'));
	});

});
//--> 
</script> 