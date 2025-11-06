<?
$pageGroup = "onadb";
$pageName = "prd_comment";

include "../lib/inc_common.php";

	$_pn = securityVal($pn);

	$query_field = "A.*";
	$query_field .= ", B.CD_IMG, B.CD_NAME";

	$query = "select ".$query_field."
		from prd_comment A 
		left join "._DB_COMPARISON." B ON (B.CD_IDX = A.pc_pd_idx) ".$search_sql;



	$total_count = wepix_counter("prd_comment", $user_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$sort_query = "pc_idx DESC";

	$_query = $query." order by ".$sort_query." limit ".$from_record.", ".$list_num;
	$result = sql_query_error($_query);


	$paging_url = _A_PATH_MEMBER_LIST."?pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);


include "../layout/header.php";
?>
<style type="text/css">
.tl-img{ width:90px; }
.tl-img img{ width:100%; }
</style>

<div id="contents_head">
	<h1>오나디비 코멘트</h1>
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
							<th class="">상품</th>
							<th width="200px">상품명</th>
							<th class="">이메일</th>
							<th width="150px">닉네임</th>
							<th width="130px">등록정보</th>
						</tr>
						<?
						while($list = wepix_fetch_array($result)){

							$img_path = '/dist/img/'.$list['CD_IMG'];
							$_pc_reg_info = json_decode($list['pc_reg_info'], true);	

							if($list['pc_user_idx']){
								$_user_name = '<i style="font-size:16px; color:#999;" class="fas fa-user-circle"></i> <b>'.$_pc_reg_info['name'].'</b>';
							}else{
								$_user_name = $_pc_reg_info['name'];
							}

						?>
						<tr align="center" id="trid_<?=$list['pc_idx']?>" bgcolor="<?=$trcolor?>">
							<td class="tl-check"><input type="checkbox" name="key_check[]" value="<?=$list['pc_idx']?>" ></td>	
							<td class="tl-idx"><?= $list['pc_idx']?></td>
							<td class="tl-img"><img src="<?=$img_path?>"></td>
							<td class= "text-left"><?=$list['CD_NAME']?></td>
							<td class= "text-left"><?=$list['pc_body']?></td>
							<td><?=$_user_name?></td>
							<td>
								<div><?=date("y.m.d <b>H:i:s</b>", strtotime($list['pc_reg_date']))?></div>
								<div class="m-t-5">( <?= $_pc_reg_info['ip']?> )</div>
								<div class="m-t-5"><?= $_pc_reg_info['domain']?></div>
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