<?
	// 변수 초기화
	$_cmode = $_cmode ?? $_GET['cmode'] ?? $_POST['cmode'] ?? "notice";

	if( !$_cmode ) $_cmode = "notice";

	$_target_mb_text = "@".$_ad_idx;

	$_query = "select 
		COUNT( * ) as my_total
	from work_log A
		LEFT OUTER JOIN work_view_check B ON ( B.tidx = A.idx AND B.mb_idx = '".$_ad_idx."' AND B.mode= 'log'  ) 
	WHERE  A.reg_idx != '".$_ad_idx."' AND A.category = '공지사항' AND B.idx IS NULL ORDER BY A.idx desc";
	$main_my_count_data1 = sql_fetch_array(sql_query_error($_query));

	$_query = "select 
		COUNT( * ) as my_total
	from work_log A
		LEFT OUTER JOIN work_view_check B ON ( B.tidx = A.idx AND B.mb_idx = '".$_ad_idx."' AND B.mode= 'log'  ) 
	WHERE  A.reg_idx != '".$_ad_idx."' AND A.category = '업무요청' 
		AND A.state IN ('대기','확인')  
		AND A.target_mb LIKE CONCAT('%',  '".$_target_mb_text."', '%')
		AND B.idx IS NULL ORDER BY A.idx desc";
	$main_my_count_data2 = sql_fetch_array(sql_query_error($_query));

	$_query = "select 
		COUNT( * ) as my_total
	from work_log A
		LEFT OUTER JOIN work_view_check B ON ( B.tidx = A.idx AND B.mb_idx = '".$_ad_idx."' AND B.mode= 'log'  ) 
	WHERE  A.reg_idx != '".$_ad_idx."' AND A.category = '프로젝트' AND A.state IN ('대기','확인')  
		AND A.target_mb LIKE CONCAT('%',  '".$_target_mb_text."', '%')
		AND B.idx IS NULL ORDER BY A.idx desc";
	$main_my_count_data3 = sql_fetch_array(sql_query_error($_query));

	$_query = "select 
		COUNT( * ) as my_total
	from work_log A
		LEFT OUTER JOIN work_view_check B ON ( B.tidx = A.idx AND B.mb_idx = '".$_ad_idx."' AND B.mode= 'log'  ) 
	WHERE  A.reg_idx != '".$_ad_idx."' AND A.category = '기획안' AND A.state IN ('대기','확인')  
		AND A.target_mb LIKE CONCAT('%',  '".$_target_mb_text."', '%')
		AND B.idx IS NULL ORDER BY A.idx desc";
	$main_my_count_data4 = sql_fetch_array(sql_query_error($_query));

	/*
	$_query = "select 
		COUNT( * ) as my_total
	from work_comment A
		LEFT OUTER JOIN work_view_check B ON ( B.tidx = A.idx AND B.mb_idx = '".$_ad_idx."' AND B.mode = 'comment' ) 
	WHERE  A.mb_idx != '".$_ad_idx."' AND A.state = '대기' 
		AND A.mention_mb LIKE CONCAT('%',  '".$_target_mb_text."', '%')
		AND B.idx IS NULL ORDER BY A.idx desc";
	$main_my_count_data5 = sql_fetch_array(sql_query_error($_query));
	*/
?>

<style type="text/css">
.main-my-count-wrap{ width:100%; display:flex; gap:10px; }
.main-my-count-wrap > ul{ flex: 1; text-align:center; }
.main-my-count-box{ display:inline-block; width:100%; height:40px; line-height:40px; text-align:center; background-color:#fff; border:1px solid #ddd; border-radius:8px; position:relative;
	cursor:pointer; }

.main-my-count-wrap > ul.active .main-my-count-box{ background-color:#333; border:1px solid #000; color:#fff; }

.main-my-count{ display:inline-block; position:absolute; top:-10px; right:5px; min-width:20px; height:20px; line-height:20px; border-radius:10px; color:#fff; font-size:10px; font-weight:600; background-color:#ff0000; }

.main-count-row_wrap{ background-color:#f7f7f7; border:1px solid #eee; padding:7px; margin:3px 0; cursor:pointer; border-radius:5px; }
.main-count-row_wrap:hover{ background-color:#fff; border:1px solid #ff0000; } 
.main-count-row_wrap.not-check{ background-color:#9ef7ff; border:1px solid #24d3e3; } 
.not-check-text{  font-size:12px; color:#ff0000; }
</style>

<div class="main-my-count-wrap">
	<ul class="<? if( $_cmode == "notice" ) echo "active"; ?>">
		<div class="main-my-count-box" onclick="main.myCount('notice');">
			<? if( $main_my_count_data1['my_total'] > 0 ){ ?><div class="main-my-count"><?=$main_my_count_data1['my_total']?></div><? } ?>
			공지사항
		</div>
	</ul>
	<ul class="<? if( $_cmode == "request" ) echo "active"; ?>">
		<div class="main-my-count-box" onclick="main.myCount('request');">
			<? if( $main_my_count_data2['my_total'] > 0 ){ ?><div class="main-my-count"><?=$main_my_count_data2['my_total']?></div><? } ?>
			업무요청
		</div>
	</ul>
	<ul class="<? if( $_cmode == "project" ) echo "active"; ?>">
		<div class="main-my-count-box" onclick="main.myCount('project');">
			<? if( $main_my_count_data3['my_total'] > 0 ){ ?><div class="main-my-count"><?=$main_my_count_data3['my_total']?></div><? } ?>
			프로젝트
		</div>
	</ul>
	<ul class="<? if( $_cmode == "plan" ) echo "active"; ?>">
		<div class="main-my-count-box" onclick="main.myCount('plan');">
			<? if( $main_my_count_data4['my_total'] > 0 ){ ?><div class="main-my-count"><?=$main_my_count_data4['my_total']?></div><? } ?>
			기획안
		</div>
	</ul>
	<!-- 
	<ul class="<? if( $_cmode == "mention" ) echo "active"; ?>">
		<div class="main-my-count-box" onclick="main.myCount('mention');">
			<? if( $main_my_count_data5['my_total'] > 0 ){ ?><div class="main-my-count"><?=$main_my_count_data5['my_total']?></div><? } ?>
			멘션 댓글
		</div>
	</ul>
	-->
</div>



<div class="m-t-10">
	<div class="main-notice-wrap">
		
		<?
		if( $_cmode == "mention" ){

			$_where = " WHERE mb_idx != '".$_ad_idx."' AND state = '대기' AND ( INSTR(mention_mb, '".$_target_mb_text."') ) ";
			$_query = "select 
				A.tidx as tidx_a, 
				A.reg_date as reg_date_a, 
				A.mb_idx as mb_idx_a 
				
				from work_comment A
				LEFT OUTER JOIN work_view_check B ON ( B.tidx = A.idx AND B.mb_idx = '".$_ad_idx."' AND B.mode = 'comment' ) 
				WHERE  A.mb_idx != '".$_ad_idx."' AND A.state = '대기' AND INSTR(A.mention_mb, '".$_target_mb_text."') AND B.idx IS NULL ORDER BY A.idx desc";
			$_result = sql_query_error($_query);
			while($_list = sql_fetch_array($_result)){

				$target_data = sql_fetch_array(sql_query_error("select subject from work_log WHERE idx = '".$_list['tidx_a']."' "));
				$data_ad = sql_fetch_array(sql_query_error("select ad_nick, ad_image from admin WHERE idx = '".$_list['mb_idx_a']."' "));

		?>
		<div class="main-count-row_wrap <?=$_row_class?>" onclick="location.href='/ad/staff/work_log_view/<?=$_list['tidx_a']?>'">
			<ul >(<?=$_list['tidx_a']?>) <?=$target_data['subject']?></ul>
			<ul class="m-t-3" ><span style="font-size:11px;"><?=date("y.m.d H:i", strtotime($_list['reg_date_a']))?></span> | <i class="fas fa-paper-plane"></i> <?=$data_ad['ad_nick']?></ul>
		</div>
		<?
			}

		}else{

			$link_url = "/ad/staff/work_log_view";

			if( $_cmode == "notice" ){ 
				
				$_where = " WHERE category = '공지사항' ";
				$_query = "select * from work_log ".$_where." ORDER BY idx desc limit 0, 5";
			
			}elseif( $_cmode == "request" ){
				
				$link_url = "/admin/work/TaskRequestDetail";

				$_where = " WHERE category = '업무요청' AND reg_idx != '".$_ad_idx."' AND state IN ('대기','확인') AND INSTR(target_mb, '".$_target_mb_text."') ";
				$_query = "select * from work_log ".$_where." ORDER BY idx desc";

			}elseif( $_cmode == "project" ){
				
				$_where = " WHERE category = '프로젝트' AND reg_idx != '".$_ad_idx."' AND state IN ('대기','확인') AND INSTR(target_mb, '".$_target_mb_text."') ";
				$_query = "select * from work_log ".$_where." ORDER BY idx desc";

			}elseif( $_cmode == "plan" ){
				
				$_where = " WHERE category = '기획안' AND reg_idx != '".$_ad_idx."' AND state IN ('대기','확인') AND INSTR(target_mb, '".$_target_mb_text."') ";
				$_query = "select * from work_log ".$_where." ORDER BY idx desc";
			
			}

			$_result = sql_query_error($_query);
			while($_list = sql_fetch_array($_result)){

				$work_view_check_data = sql_fetch_array(sql_query_error("select idx from work_view_check WHERE tidx = '".$_list['idx']."' AND mb_idx = '".$_ad_idx."' "));
				
				if (!is_array($work_view_check_data)) {
					$work_view_check_data = [];
				}
				
				$_row_class = "";
				$_check_state = "";
				if( empty($work_view_check_data['idx']) ){
					$_row_class = "not-check";
					$_check_state = " | <span class='not-check-text'>미확인</span>";
				}

					$_state_text = "";
					if( $_cmode == "request" ){ 
						$_state_text = " | <span class='state-text'>".$_list['state']."</span>";
					}
				
		?>
		<div class="main-count-row_wrap <?=$_row_class?>" onclick="location.href='<?=$link_url?>/<?=$_list['idx']?>'">
			<ul style="font-size:11px;"><b><?=date("y.m.d", strtotime($_list['reg_date']))?></b><?=$_state_text?><?=$_check_state?></ul>
			<ul class="m-t-3"><?=$_list['subject']?></ul>
		</div>
		<? } } ?>



	</div>
</div>
