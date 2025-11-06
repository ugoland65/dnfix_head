<?
$pageGroup = "member";
$pageName = "admin_list";

include "../lib/inc_common.php";

	$admin_where = " where 1=1 ";
	$admin_data = wepix_fetch_array(wepix_query_error("select * from "._DB_ADMIN." where AD_ID = '".$_ad_id."' "));

	if( $admin_data[AD_LEVEL] != "100"  ){
		 $admin_where .= " and AD_LEVEL < 100";
	}

    if( $search_kind && $search_kind !='') { 
        if($search_kind == 'id'){
            $admin_where .= " and AD_ID like '%".$search_text."%'";
        }else if($search_kind == 'name'){
            $admin_where .= " and AD_NAME like '%".$search_text."%'";
        }
    }

	$total_count = wepix_counter(_DB_ADMIN, $admin_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$admin_query = "select * from "._DB_ADMIN." ".$admin_where." order by AD_IDX desc limit ".$from_record.", ".$list_num;
	$admin_result = wepix_query_error($admin_query);

	$page_link_text = _A_PATH_MEMBER_A_LIST."?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>운영자 목록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<form name='search' method='post' action="admin_list.php">
		<div class="search-wrap">
			<ul class="td search-img"></ul>
			<ul class="td search-body">
<?
/*
						<select name="search_kind" id="search_kind" style="width:120px;">
							<option value=''>Search Criteria</option>
							<option value='id' <?if($search_kind == 'id'){echo "selected";}?>>ID</option>
							<option value='name' <?if($search_kind == 'name'){echo "selected";}?>>Name</option>
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
						<th>영문이름</th>
						<th width="80px">등급</th>
						<th width="80px">언어</th>
						<th width="60px">관리 <?=$admin_data[AD_LEVEL]?></th>
					</tr>
					<?
					$state['0'] = '승인대기';
					$state['1'] = '승인완료';
					$state['2'] = '차단';
					while($admin_list = wepix_fetch_array($admin_result)){
						$_view2_gd_state = $state[$admin_list[GD_STATE]];
						$_view2_gd_reg_date = date("y-m-d", $admin_list[GD_REG_DATE]);
						if( $admin_list[GD_SUPER] == "Y" ){
							$_view2_gd_super = '<i class="fas fa-crown"></i>';
						}else{
							$_view2_gd_super = "";
						}
						
						if( $admin_list[GD_STATE] == "2" ){
							$trcolor = "#eee";
						}else{
							$trcolor = "#fff";
						}
					?>
					<tr align="center" id="trid_<?=$admin_list[AD_IDX]?>" bgcolor="<?=$trcolor?>">
						<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$admin_list[AD_IDX]?>" ></td>	
						<td class="list-idx"><?=$admin_list[AD_IDX]?></td>
						<td class="list-nick"><b onclick="userModify('<?=$admin_list[AD_ID]?>','admin')" style="cursor:pointer;"><?=$admin_list[AD_NICK]?></b></td>
						<td class="list-id"><?=$admin_list[AD_ID]?></td>
						<td><?=$admin_list[AD_NAME]?></td>
						<td><?=$admin_list[AD_NAME_EG]?></td>
						<td><?=$admin_list[AD_LEVEL]?></td>
						<td><?=$bva_system_language_ko[$admin_list[AD_LANG]]?></td>
						<td>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='<?=_A_PATH_MEMBER_A_REG?>?mode=modify&key=<?=$admin_list[AD_IDX]?>'"> 수정 </button>
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