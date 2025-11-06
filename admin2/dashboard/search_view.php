<?
$pageGroup = "dashboard";
$pageName = "serch";

include "../lib/inc_common.php";
include "../layout/header.php";

	$_pn = securityVal($pn);
	$search_sql = "where st_id > 0 AND st_ip NOT IN ('119.193.168.78','210.221.8.92','58.76.23.36') ";

	if(!$s_day){
		$today_date = date('Y-m-d'); 
		$day_of_the_week = date('w'); 
		$_day_of_the_date = strtotime($today_date." -".$day_of_the_week."days");
		$s_day = date('Y-m-d', $_day_of_the_date);
		$e_day = date('Y-m-d', strtotime("+10 day" , $_day_of_the_date));
	}

	$total_count = wepix_counter("serch_text", $search_sql);

	$list_num = 30;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page  = @ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
	$counter = $total_count - (($_pn - 1) * $list_num);
	
//	$query = "SELECT * FROM serch_text ".$search_sql." ORDER BY st_id DESC LIMIT ".$from_record.", ".$list_num;
	$query = "SELECT * FROM serch_text ".$search_sql." ORDER BY st_id DESC LIMIT ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);
	
	$rank_query = "SELECT st_word,count(st_word) as word_count FROM serch_text  WHERE st_ip != '119.193.168.78' GROUP BY st_word ORDER BY word_count DESC LIMIT 0,20";
	$rank_result = wepix_query_error($rank_query);

	$paging_url = "/admin2/dashboard/search_view.php?&pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);


?>
<div id="contents_head">
	<h1>검색 통계</h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
		<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">
<!-- 
					<table class="table-list" style='width:300px !important ; float:left; margin-right:60px;'>
						<tr>
							<th>순위</th>
							<th>검색어</th>
							<th>갯수</th>
						</tr>
						<?	
							$_num = 0;
							while($list = wepix_fetch_array($rank_result)){ 
							$_num++;
						?>
						<tr>
							<td><?=$_num?> 위</td>
							<td><?=$list[st_word]?></td>
							<td><?=$list[1]?></td>
						</tr>
						<?
							}
						?>
					</table>
 -->
					<table class="table-list"  >
						<tr>
							<th> 도메인</th>
							<th>검색어</th>
							<th>날짜</th>
							<th>IP</th>
							<th>Device</th>
							<th>검색결과</th>
						</tr>
						<?	
							$_row_num = 0;
							while($serch_list = wepix_fetch_array($result)){ 
							$_row_num++;

							if( $serch_list[st_result] == 0 ){
								$trcolor = "#ffcbcb";
							}else{
								$trcolor = "#ffffff";
							}
						?>
						<tr bgcolor="<?=$trcolor?>">
							<td><?=$serch_list[st_domain]?></td>
							<td><?=$serch_list[st_word]?></td>
							<td><?=$serch_list[st_date]?> // <?=$serch_list[st_time]?></td>
							<td><?=$serch_list[st_ip]?></td>
							<td><?=$serch_list[st_device]?></td>
							<td><?=$serch_list[st_result]?></td>
						</tr>
						<?
							}
						?>
					</table>



				</div><!-- #list_box2 -->
			</ul>
			
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>
	</div>
 </div>

<?
include "../layout/footer.php";
exit;
?>