<style type="text/css">
.comment-list-wrap{  }

.comment-list-box{
	width:100%; display:table; margin-top:10px;
}
.comment-list-box > ul{ display:table-cell; vertical-align:top; }
.comment-list-box > ul.left{ width:50px; }
.comment-list-box > ul.left2{ width:70px; }
.comment-list-box > ul.right{ width:20px; }

.cbody{  
	position: relative;
	background-color:#fff; border:1px solid #bbb; padding:10px; margin-bottom:5px; border-radius:10px; 
	
	&.myc{  background-color:#ffc; }
}

/*
.cbody::after {
  content: '';
  position: absolute;
  top: 10px;
  left: auto;
  right: 100%; 
  border-width: 5px;
  border-style: solid;
  border-color: transparent #fff transparent transparent;
}
*/

.comment-mb-profile{ display:inline-block; width:40px; height:40px; border:1px solid #999; overflow:hidden; border-radius:50%; }
.comment-mb-profile img{ width:100%; }
.mention-wrap{ display:inline-block; background-color:#f7f7f7; border:1px solid #bbb; padding:5px;border-radius:5px; font-size:12px;  }
</style>
<?
	$_where = " WHERE mode = '".$_work_comment_mode."' AND kind = 'S' AND tidx = '".$_tidx."' ";
	$_query = "select 
		A.*,
		B.ad_nick, B.ad_image 
		from work_comment A
		left join admin B ON (B.idx = A.mb_idx  ) 
		".$_where." ORDER BY grpno ASC, grpord ASC";
	$_result = sql_query_error($_query);
	while($_list = wepix_fetch_array($_result)){
?>

	<div class="comment-list-wrap">
		<div class="comment-list-box">
			
			<? if( $_list['mb_idx'] == $_ad_idx ){ ?>
			<ul class="left2">

			</ul>
			<? }else{ ?>
			<ul class="left text-center">
				<div class="comment-mb-profile"><img src="/data/uploads/<?=$_list['ad_image']?>" alt=""></div>
				<span style="font-size:11px;"><?=$_list['idx']?></span>
			</ul>
			<? } ?>

			<ul class="cbody <? if( $_list['mb_idx'] == $_ad_idx ){ ?> myc <? }else{ ?>p-l-7<? } ?> ">
				<div>
					<ul>
						<span style="font-weight:600"><?=$_list['ad_nick']?></span> 
						<span style="font-size:11px;"><?=date("y.m.d H:i", strtotime($_list['reg_date']))?> | <?=$_list['state']?></span></ul>
				</div>
				<div class="m-t-5" style="line-height:140%;">
					<?=nl2br($_list['comment'])?>
				</div>
				<div>
					<ul class="m-t-5">
						<?
							$_this_target_mb_idx = explode("@", $_list['mention_mb']);
							for ($i=1; $i<count($_this_target_mb_idx); $i++){ 
								$_this_addata = sql_fetch_array(sql_query_error("select ad_nick  from admin WHERE idx = '".$_this_target_mb_idx[$i]."' "));
								//$_this_work_view_check = sql_fetch_array(sql_query_error("select * from work_view_check WHERE  mode = 'comment' AND tidx = '".$_list['idx']."' AND mb_idx = '".$_ad_idx."' "));
								$_this_work_view_check = sql_fetch_array(sql_query_error("select * from work_view_check WHERE  mode = 'comment' AND tidx = '".$_list['idx']."' AND mb_idx = '".$_this_target_mb_idx[$i]."' "));
						?>
						<div class="mention-wrap" >
							@<?=$_this_addata['ad_nick']?>
							<? if( $_this_target_mb_idx[$i] == $_ad_idx ){ ?>
								<? if( !$_this_work_view_check['idx'] ){ ?>
								<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-info btnstyle1-xs" onclick="workLogView.commentviewCheck(this, '<?=$_list['idx']?>')" >댓글확인처리</button>
								<?
								}else{
									echo ": 확인 <span style='font-size:11px;'>(".date("y.m.d H:i", strtotime($_this_work_view_check['reg_date'])).")</span>";
								}
							}else{ 
								if( $_this_work_view_check['idx'] ) {
									echo ": 확인 <span style='font-size:11px;'>(".date("y.m.d H:i", strtotime($_this_work_view_check['reg_date'])).")</span>";
								}else{
									echo ": 미확인";
								} 
							} ?>

						</div>
						<? } ?>
					</ul>
					<ul class="m-t-5 display-none">
						<button type="button" id="" class="btnstyle1 btnstyle1-xs" onclick="workLogView.commentReMake(this, '<?=$_list['idx']?>')" ><i class="far fa-comment-dots"></i> 답변</button>
					</ul>

				<div>
			</ul>

			<? if( $_list['mb_idx'] == $_ad_idx ){ ?>

			<? }else{ ?>
				<ul class="right">

				</ul>
			<? } ?>

		</div>

		<div class="" id="comment_re_<?=$_list['idx']?>" data-state="off" >

		</div>

	</div>
<? } ?>