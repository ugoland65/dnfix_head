<?
	// 변수 초기화
	$search_kind = $_GET['search_kind'] ?? $_POST['search_kind'] ?? "";
	$search_text = $_GET['search_text'] ?? $_POST['search_text'] ?? "";
	$_pn = $_GET['pn'] ?? $_POST['pn'] ?? $_pn ?? 1;
	
	$user_where = "";

    if( $search_kind && $search_kind !='') { 
        if($search_kind == 'id'){
            $user_where .= " and user_id like '%".$search_text."%'";
        }else if($search_kind == 'name'){
            $user_where .= " and user_nick like '%".$search_text."%'";
        }
    }

	$total_count = wepix_counter("user", $user_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$user_query = "select * from user ".$user_where." order by user_idx desc limit ".$from_record.", ".$list_num;
	$user_result = wepix_query_error($user_query);

	$paging_url = _A_PATH_MEMBER_LIST."?pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);
?>

<div class="total">Total : <span><b><?=number_format($total_count)?></b></span> &nbsp; | &nbsp;  <span><b><?=$_pn?></b></span> / <?=$total_page?> page</div>

<table class="table-style m-t-6">	
	<tr class="list">
		<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
		<th class="tl-idx">고유번호</th>
		<th class="tl-user-id">아이디</th>
		<th class="">이메일</th>
		<th width="100px">닉네임</th>
		<th class="tl-user-level">레벨</th>
		<th width="100px">포인트</th>
		<th width="100px">스코어</th>
		<th width="100px">가입일</th>
		<th>IP</th>
		<th width="60px">관리 </th>
	</tr>
	<?
	while($list = wepix_fetch_array($user_result)){

		$_user_join_data = json_decode($list['user_join_data'], true);	
	?>
	<tr align="center" id="trid_<?=$list['user_idx']?>" bgcolor="<?=$trcolor?>">
		<td class="tl-check"><input type="checkbox" name="key_check[]" value="<?=$list['user_idx']?>" ></td>	
		<td class="tl-idx"><?= $list['user_idx']?></td>
		<td class= "text-left"><b onclick="userModify('<?= $list['user_id']?>')" style="cursor:pointer;"><?= $list['user_id']?></b></td>
		<td class= "text-left"><?= $list['user_email']?></td>
		<td><?= $list['user_nick']?></td>
		<td class="tl-user-level"><?= $list['user_level']?></td>
		<td><?= $list['user_point']?></td>
		<td><?= $list['user_score']?></td>
		<td>
			<?=$_user_join_data['reg_date']?><br>
			(<?=$_user_join_data['domain']?>)
		</td>
		<td>
			<?=$_user_join_data['ip']?>
		</td>
		<td>
		<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='<?=_A_PATH_MEMBER_REG?>?mode=modify&key=<?=$list['user_idx']?>'"> 수정 </button>
		</td>
	<tr>
	<? }?>
</table>