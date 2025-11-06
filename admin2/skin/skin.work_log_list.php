<?

	$_target_mb_text = "@".$_ad_idx;

	if( $_work_log_cate == "all" ){
		$_where = "  ";
	}elseif( $_work_log_cate ){
		$_where = " WHERE category = '".$_work_log_cate."' ";
	}
	
	if( $_call_mode == "my" ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " reg_idx = '".$_ad_idx."' ";
		
		if( $_state == "ing" ){
			$_where .= " AND state IN ('대기','확인') ";
		}

	}

	if( $_call_mode == "call" ){
		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " reg_idx != '".$_ad_idx."' AND INSTR(target_mb, '".$_target_mb_text."') ";

		if( $_check == "no" ){
			$_where .= " AND C.idx IS NULL ";
		}elseif( $_check == "ing" ){
			$_where .= " AND C.idx AND state IN ('대기','확인') ";
		}

	}else{

	}



		$total_count = wepix_counter("work_log", $_where);

	if( $_pn == "" ) $_pn = 1;

	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "workLogMain.list", "");

	$_query = "select 
		A.*,
		B.ad_nick, B.ad_image 
		from work_log A
		left join admin B ON (B.idx = A.reg_idx  ) ";

	if( $_call_mode == "call" ){
		$_query .= "LEFT OUTER JOIN work_view_check C ON ( C.tidx = A.idx AND C.mb_idx = '".$_ad_idx."' AND C.mode= 'log'  ) ";
	}

/*
	$_query .= $_where." ORDER BY idx desc limit ".$from_record.", ".$list_num;
	$_result = sql_query_error($_query);
*/
$_query .= $_where . " ORDER BY 
    (CASE WHEN A.state IN ('완료', '반려') THEN 1 ELSE 0 END) ASC, 
    idx DESC 
    limit " . $from_record . ", " . $list_num;
$_result = sql_query_error($_query);

	$_order_sheet_state_text[1] = "작성중";
	$_order_sheet_state_text[2] = "주문전송";
	$_order_sheet_state_text[4] = "입금완료";
	$_order_sheet_state_text[5] = "입고완료";
	$_order_sheet_state_text[7] = "주문종료";

	$query = "select 
		COUNT( CASE WHEN category = '업무일지' THEN 1 END ) as my_count1,
		COUNT( CASE WHEN category = '프로젝트' THEN 1 END ) as my_count2,
		COUNT( CASE WHEN category = '기획안' THEN 1 END ) as my_count3,
		COUNT( CASE WHEN category = '업무요청' THEN 1 END ) as my_count4,

		COUNT( CASE WHEN category = '프로젝트' AND state IN ('대기','확인') THEN 1 END ) as my_ing_count2,
		COUNT( CASE WHEN category = '기획안' AND state IN ('대기','확인') THEN 1 END ) as my_ing_count3,
		COUNT( CASE WHEN category = '업무요청' AND state IN ('대기','확인') THEN 1 END ) as my_ing_count4

		from work_log WHERE reg_idx = '".$_ad_idx."' ";
	$my_count_1 = sql_fetch_array(sql_query_error($query));

	$query = "select 

		COUNT( CASE WHEN A.category = '프로젝트' AND A.state IN ('대기','확인') AND B.idx IS NULL THEN 1 END ) as my_count2,
		COUNT( CASE WHEN A.category = '기획안' AND A.state IN ('대기','확인') AND B.idx IS NULL THEN 1 END ) as my_count3,
		COUNT( CASE WHEN A.category = '업무요청' AND A.state IN ('대기','확인') AND B.idx IS NULL THEN 1 END ) as my_count4,

		COUNT( CASE WHEN A.category = '프로젝트' AND A.state IN ('대기','확인') AND B.idx THEN 1 END ) as my_ing_count2,
		COUNT( CASE WHEN A.category = '기획안' AND A.state IN ('대기','확인') AND B.idx  THEN 1 END ) as my_ing_count3,
		COUNT( CASE WHEN A.category = '업무요청' AND A.state IN ('대기','확인') AND B.idx THEN 1 END ) as my_ing_count4

	from work_log A
		LEFT OUTER JOIN work_view_check B ON ( B.tidx = A.idx AND B.mb_idx = '".$_ad_idx."' AND B.mode= 'log'  ) 
	WHERE  A.reg_idx != '".$_ad_idx."' AND INSTR(A.target_mb, '".$_target_mb_text."')  ORDER BY A.idx desc ";
	$my_count_2 = sql_fetch_array(sql_query_error($query));


?>
<style type="text/css">
.work-log-mb-profile,
.work-log-list-mb-profile{ display:inline-block; width:22px; height:22px; margin-right:2px; vertical-align:middle; border:1px solid #999; overflow:hidden; border-radius:50%; }
.work-log-mb-profile img,
.work-log-list-mb-profile img{ width:100%; }

.table-style tr.tr-end td{ background-color:#eee !important; }
.table-style tr.tr-normal td{ background-color:#fff !important; }

.work-log-dashboard-wrap{}
.work-log-dashboard-wrap > ul{ width:130px; display:inline-block; border:1px solid #999; border-radius:5px; overflow:hidden; vertical-align:top; }

.work-log-dashboard-wrap > ul > div > ul{ padding:5px 10px; text-align:left; cursor:pointer; background-color:#fff; border-bottom:1px solid #ccc; }
.work-log-dashboard-wrap > ul > div > ul:last-child{ border-bottom:none; }
.work-log-dashboard-wrap > ul > div > ul:hover{ background-color:#fffabf; }
.work-log-dashboard-wrap > ul > div > ul.title{ background-color:#b9d7ff; padding:5px 10px; text-align:center; color:#000; font-size:14px; font-weight:600; border-bottom:1px solid #5788ca; }
.work-log-dashboard-wrap > ul > div > ul b{ float:right; color:#ff0000; }
</style>

<div class="work-log-dashboard-wrap">
	<ul>
		<div>
			<ul class="title">나의 업무일지</ul>
			<ul>전체 <?=$my_count_1['my_count1']?></ul>
		</div>
	</ul>
	<ul>
		<div>
			<ul class="title">나의 프로젝트</ul>
			<? if( $my_count_1['my_count2'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=프로젝트:call_mode=my'">내가작성 <b><?=$my_count_1['my_count2']?></b></ul><? } ?>
			<? if( $my_count_1['my_ing_count2'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=프로젝트:call_mode=my:state=ing'">내 미완료 <b><?=$my_count_1['my_ing_count2']?></b></ul><? } ?>
			<? if( $my_count_2['my_count2'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=프로젝트:call_mode=call:check=no'">참여 미체크 <b><?=$my_count_2['my_count2']?></b></ul><? } ?>
			<? if( $my_count_2['my_ing_count2'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=프로젝트:call_mode=call:check=no'">참여 처리중 <b><?=$my_count_2['my_ing_count2']?></b></ul><? } ?>
		</div>
	</ul>
	<ul>
		<div>
			<ul class="title">나의 기획안</ul>
			<? if( $my_count_1['my_count3'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=기획안:call_mode=my'">내가작성 <b><?=$my_count_1['my_count3']?></b></ul><? } ?>
			<? if( $my_count_1['my_ing_count3'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=기획안:call_mode=my:state=ing'">내 미완료 <b><?=$my_count_1['my_ing_count3']?></b></ul><? } ?>
			<? if( $my_count_2['my_count3'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=기획안:call_mode=call:check=no'">참여 미체크 <b><?=$my_count_2['my_count3']?></b></ul><? } ?>
			<? if( $my_count_2['my_ing_count3'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=기획안:call_mode=call:check=ing'">참여 처리중 <b><?=$my_count_2['my_ing_count3']?></b></ul><? } ?>
		</div>
	</ul>
	<ul>
		<div>
			<ul class="title">나의 업무요청</ul>
			<? if( $my_count_1['my_count4'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=업무요청:call_mode=my'">나의요청 <b><?=$my_count_1['my_count4']?></b></ul><? } ?>
			<? if( $my_count_1['my_ing_count4'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=업무요청:call_mode=my:state=ing'">내 미완료 <b><?=$my_count_1['my_ing_count4']?></b></ul><? } ?>
			<? if( $my_count_2['my_count4'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=업무요청:call_mode=call:check=no'">참여 미체크 <b><?=$my_count_2['my_count4']?></b></ul><? } ?>
			<? if( $my_count_2['my_ing_count4'] ){ ?><ul onclick="location.href='/ad/staff/work_log/cate=업무요청:call_mode=call:check=ing'">참여 처리중 <b><?=$my_count_2['my_ing_count4']?></b></ul><? } ?>
		</div>
	</ul>
</div>

<div class="total">Total : <span><b><?=number_format($total_count)?></b></span> &nbsp; | &nbsp;  <span><b><?=$_pn?></b></span> / <?=$total_page?> page</div>
<table class="table-style m-t-10">	
	<tr class="list">
		<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
		<th class="list-idx">고유번호</th>
		<th class="">카테고리</th>
		<th class="">제목</th>
		<th class="">상태</th>
		<th class="">작성자</th>
		<th class="">참여자</th>
		<th>작성일</th>
		<th>비고</th>
	</tr>
	<?
	while($list = sql_fetch_array($_result)){

		$_reg = json_decode($list['reg'], true);

		if( $list['state'] == "완료" ){
			$_tr_class = "tr-end";
		}elseif( $list['state'] == "확인" ){
			$_tr_class = "";
		}elseif( $list['state'] == "반려" ){
			$_tr_class = "tr-end";
		}else{
			$_tr_class = "tr-normal";
		}

	?>
	<tr align="center" id="trid_<?=$list['idx']?>" class="<?=$_tr_class?>">
		<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list['idx']?>" ></td>	
		<td class="list-idx"><?=$list['idx']?></td>
		<td class=""><?=$list['category']?></td>
		<td class="text-left" style="font-size:14px;">
			<a href="/ad/staff/work_log_view/<?=$list['idx']?>"><?=$list['subject']?></a>
			<? if( $list['cmt_s_count'] > 0 ){ ?> (<?=$list['cmt_s_count']?>)<? } ?>
			<? if( $list['cmt_b_count'] > 0 ){ ?><button type="button"class="btnstyle1 btnstyle1-success btnstyle1-sm" >답변있음</button><? } ?>
		</td>
		<td class=""><?=$list['state']?></td>
		<td class=""><div class="work-log-mb-profile"><img src="/data/uploads/<?=$list['ad_image']?>" alt=""></div><?=$_reg['reg']['name']?></td>
		<td class="">
			<?
			if( $list['target_mb'] ){
				$_this_target_mb_idx = explode("@", $list['target_mb']);
				for ($i=1; $i<count($_this_target_mb_idx); $i++){ 
					$_this_addata = sql_fetch_array(sql_query_error("select ad_nick, ad_image from admin WHERE idx = '".$_this_target_mb_idx[$i]."' "));
			?>

				<? if( count($_this_target_mb_idx) == 2 ){ ?>
					<div class="work-log-list-mb-profile" ><img src="/data/uploads/<?=$_this_addata['ad_image']?>" alt=""></div> <?=$_this_addata['ad_nick']?>
				<? }else{ ?>
					<div class="work-log-list-mb-profile" data-toggle="tooltip" data-placement="top" title="<?=$_this_addata['ad_nick']?>"><img src="/data/uploads/<?=$_this_addata['ad_image']?>" alt=""></div>
				<? } ?>

			<? } } ?>

		</td>
		<td class=""><?=date("y.m.d H:i", strtotime($list['reg_date']))?></td>
		<td class="text-left">
			<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="footerGlobal.comment('log','<?=$list['idx']?>')" >
				댓글
				<? if( $list['cmt_s_count'] > 0 ) { ?> : <b><?=$list['cmt_s_count']?></b><? } ?>
			</button>
		</td>
	<tr>
	<? } ?>
</table>

<div id="hidden_pageing_ajax_data" style="display:none;"><?=$view_page?></div>
<script type="text/javascript"> 
pageingAjaxShow();
</script> 