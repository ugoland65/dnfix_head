<?
$pageGroup = "member";
$pageName = "member_list";

include "../lib/inc_common.php";

	$_pn = securityVal($pn);

	$user_where = " where USE_KIND = 'U' ";

    if( $search_kind && $search_kind !='') { 
        if($search_kind == 'id'){
            $user_where .= " and USE_ID like '%".$search_text."%'";
        }else if($search_kind == 'name'){
            $user_where .= " and USE_NAME like '%".$search_text."%'";
        }
    }

	$total_count = wepix_counter(_DB_MEMBER, $user_where);
	
	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$user_query = "select * from "._DB_MEMBER." ".$user_where." order by USE_IDX desc limit ".$from_record.", ".$list_num;
	$user_result = wepix_query_error($user_query);

	$paging_url = _A_PATH_MEMBER_LIST."?pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);


	$setting_data = wepix_fetch_array(wepix_query_error("select * from "._DB_SETTING." where SET_CODE = '"._GLOB_SITE_CODE."' "));
	$_view_setting_email_certify_active = $setting_data[SET_JOIN_EMAIL_CERTIFY_ACTIVE];

include "../layout/header.php";
?>
<div id="contents_head">
	<h1><?=_LG_PT_MEMBER_LIST?></h1>
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
							<th width="30px"></th>
							<th class="tl-user-id">아이디</th>
<?
//실명사용
if( _GLOB_SYS_REAL_NAME=="on" ){
?>
							<th width="100px">이름</th>
<? } ?>
							<th class="tl-user-level">레벨</th>
							<th width="150px">닉네임</th>
							<th width="100px">상태</th>
						<!--	<th width="80px">생년월일</th>-->
							<th width="80px">가입날자</th>
							<th width="60px">관리 </th>
						</tr>
						<?
						while($user_list = wepix_fetch_array($user_result)){
							$use_req_date = date("y-m-d", $user_list[USE_REG_DATE]);
							$_view2_user_sex = $bva_gender[$user_list[USE_SEX]];
							
							if($user_list[USE_JOIN_MODE]=="kakao"){
								$_view2_join_mode_icon = '<i style="font-size:13px; color:#f89800;" class="fas fa-comment"></i>';
							}elseif($user_list[USE_JOIN_MODE]=="facebook"){
								$_view2_join_mode_icon = '<i style="font-size:15px; color:#4267b2;" class="fab fa-facebook-square"></i>';
							}else{
								$_view2_join_mode_icon = '<i style="font-size:16px; color:#999;" class="fas fa-user-circle"></i>';
							}
							
							//이메일 인증 사용시
							if( $_view_setting_email_certify_active == 'Y'){
								$_view2_user_state =  $bva_user_state[$user_list[USE_STATE]];
							}else{
								$_view2_user_state =  $user_list[USE_STATE];
							}
							
						?>
						<tr align="center" id="trid_<?=$user_list[USE_IDX]?>" bgcolor="<?=$trcolor?>">
							<td class="tl-check"><input type="checkbox" name="key_check[]" value="<?=$user_list[USE_IDX]?>" ></td>	
							<td class="tl-idx"><?= $user_list[USE_IDX]?></td>
							<td ><?=$_view2_join_mode_icon?></td>
							<td class="tl-user-id text-left"><b onclick="userModify('<?= $user_list[USE_ID]?>')" style="cursor:pointer;"><?= $user_list[USE_ID]?></b></td>
<?
//실명사용
if( _GLOB_SYS_REAL_NAME=="on" ){
?>
							<td><?= $user_list[USE_NAME]?></td>
<? } ?>
							<td class="tl-user-level"><?= $user_list[USE_LEVEL]?></td>
							<td><b><?= $user_list[USE_NICKNAME]?></b></td>
						<!--<td><?= $user_list[USE_BIRTH]?></td>-->
							<td><?=$_view2_user_state?></td>
							<td><?= $use_req_date?></td>
							<td>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='<?=_A_PATH_MEMBER_REG?>?mode=modify&key=<?=$user_list[USE_IDX]?>'"> 수정 </button>
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