<?
$pageGroup = "showdang";
$pageName = "tag";

include "../lib/inc_common.php";

	$_mode = $mode ;
	//$_serch_query = "where BD_KIND_CODE = '".$_mode."'";
	$total_count = wepix_counter("hashtag", $_serch_query);
	
	$list_num = 300;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$query = "select * from hashtag ".$_serch_query." order by hg_name_before asc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$page_link_text = "tag.php?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>해시테그 관리</h1>
    <div id="head_write_btn">

	</div>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">
			
			<table class="table-style">	
				<tr  id="<?=$list['hg_idx']?>_<?=$list['hg_idx']?>">
					<th class="list-checkbox tds1"><input type="checkbox" name="" onclick="select_all()"></th>
					<th>고유번호</th>
					<th>테그명</th>
					<th>변경 테그명</th>
					<th>제거</th>
					<th>링크</th>
				</tr>
			<?
			while($list = wepix_fetch_array($result)){
			?>
				<tr id="<?=$list['hg_idx']?>">
					<td class="list-checkbox tds2">
					</td>
					<td class="">
						<?=$list['hg_idx']?>
					</td>
					<td class="">
						<?=$list['hg_name_before']?>
					</td>
					<td class="">
						<?=$list['hg_name']?>
					</td>
					<td class="">
						<? if( $list['hg_del_mode'] == "Y" ){ ?>
						제거
						<? } ?>
					</td>
					<td class="">
						<?=$list['hg_url']?>
					</td>
				</tr>
			<? } ?>
			</table>

		</div>

	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>
