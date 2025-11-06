<?
$pageGroup = "member";
$pageName = "guide_list";

include "../lib/inc_common.php";



	$guide_where = " where 1=1 ";

    if( $search_kind && $search_kind !='') { 
        if($search_kind == 'id'){
            $guide_where .= " and GD_ID like '%".$search_text."%'";
        }else if($search_kind == 'name'){
            $guide_where .= " and GD_NAME like '%".$search_text."%'";
        }else if($search_kind == 'nick'){
            $guide_where .= " and GD_NICK like '%".$search_text."%'";
        }
    }


	$total_count = wepix_counter(_DB_GUIDE, $guide_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$guide_query = "select * from "._DB_GUIDE." ".$guide_where." order by GD_IDX desc limit ".$from_record.", ".$list_num;
	$guide_result = wepix_query_error($guide_query);

	$page_link_text = _A_PATH_MEMBER_G_LIST."?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>



<div id="contents_head">
	<h1>가이드 목록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<form name='search' method='post' action="guide_list.php">
		<div class="search-wrap">
			<ul class="td search-img"></ul>
			<ul class="td search-body">
<?
/*
						<select name="search_kind" id="search_kind" style="width:120px;">
							<option value=''>Search Criteria</option>
							<option value='id' <?if($search_kind == 'id'){echo "selected";}?>>ID</option>
							<option value='name' <?if($search_kind == 'name'){echo "selected";}?>>Name</option>
							<option value='nick' <?if($search_kind == 'nick'){echo "selected";}?>>Nick</option>
						</select>
					<input type='text' id='search_text' style="width:160px;" name='search_text' value='<?=$search_text?>' placeholder="검색어를 입력해주세요"></li>
*/
?>
			</ul>
            <ul class="td search-button">
				<input type="submit" value="Searching">
			</ul>
		</div>
		</form>

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list-box">
			<div class="table-wrap">
				<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr>
						<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
						<th class="list-idx">고유번호</th>
						<th class="list-nick">닉네임</th>
						<th class="list-id">아이디</th>
						<th width="80px">이름</th>
						<th>Chit ID</th>
						<th width="80px">상태</th>
						<th width="80px">승인아이디</th>
<!--					<th width="80px">가입날자</th>-->
						<th width="50px">마스터</th>
						<th width="60px">관리 </th>
					</tr>
					<?
					$state['0'] = '승인대기';
					$state['1'] = '승인완료';
					$state['2'] = '차단';
					while($guide_list = wepix_fetch_array($guide_result)){
						$_view2_gd_state = $state[$guide_list[GD_STATE]];
						$_view2_gd_reg_date = date("y-m-d", $guide_list[GD_REG_DATE]);
						if( $guide_list[GD_SUPER] == "Y" ){
							$_view2_gd_super = '<i class="fas fa-crown"></i>';
						}else{
							$_view2_gd_super = "";
						}
						
						if( $guide_list[GD_STATE] == "2" ){
							$trcolor = "#eee";
						}else{
							$trcolor = "#fff";
						}
					?>
					<tr align="center" id="trid_<?=$guide_list[GD_IDX]?>" bgcolor="<?=$trcolor?>">
						<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$guide_list[GD_IDX]?>" ></td>	
						<td class="list-idx"><?=$guide_list[GD_IDX]?></td>
						<td class="list-nick"><b><?=$guide_list[GD_NICK]?></b></td>
						<td class="list-id"><?=$guide_list[GD_ID]?></td>
						<td><?=$guide_list[GD_NAME]?></td>
						<td><?=$guide_list[GD_CHAT_ID]?></td>
						<td><?=$_view2_gd_state?></td>
						<td><?=$guide_list[GD_STATE_ID]?></td>
				<!--	<td><?=$_view2_gd_reg_date?></td>-->
						<td><?=$_view2_gd_super?></td>
						<td>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='<?=_A_PATH_MEMBER_G_REG?>?mode=modify&key=<?=$guide_list[GD_IDX]?>'"> 수정 </button>
						</td>
					<tr>
					<? }?>

				</table>
			</div>
		</div><!-- #list-box -->
		<div class="paging-wrap"><?=$view_paging?></div>

	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>