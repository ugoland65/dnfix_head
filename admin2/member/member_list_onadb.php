<?
$pageGroup = "member";
$pageName = "member_list_onadb";

include "../lib/inc_common.php";

	$_pn = securityVal($pn);

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


include "../layout/header.php";
?>
<div id="contents_head">
	<h1>오나디비 회원</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_PARTNER_AG_REG?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn">
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="doGroup('new','600','500');">선택회원 삭제</button>
			</ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">

					<table class="table-list">	
						<tr>
							<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
							<th class="tl-idx">고유번호</th>
							<th class="tl-user-id">아이디</th>
							<th class="">이메일</th>
							<th width="100px">닉네임</th>
							<th class="tl-user-level">레벨</th>
							<th width="100px">포인트</th>
							<th width="100px">스코어</th>
							<th width="100px">가입일</th>
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
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='<?=_A_PATH_MEMBER_REG?>?mode=modify&key=<?=$list['user_idx']?>'"> 수정 </button>
							</td>
						<tr>
						<? }?>
					</table>

				</div>
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
				<div id="list_box_layout2_filter_wrap">
				</div>
			</ul>
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>
	</div>
</div>

<?
include "../layout/footer.php";
exit;
?>