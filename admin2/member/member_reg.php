<?
$pageGroup = "member";
$pageName = "member_req";

include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_gd_idx = securityVal($key);

	if( $_mode == "modify" ){

		$user_data = wepix_fetch_array(wepix_query_error("select * from "._DB_MEMBER." where USE_IDX = '".$_gd_idx."' "));
		$_ary_use_birth = explode("-",$user_data[USE_BIRTH]);
		$_view_use_birth_y = $_ary_use_birth[0];
		$_view_use_birth_m = $_ary_use_birth[1];
		$_view_use_birth_d = $_ary_use_birth[2];
		
		$_ary_use_phone = explode("-",$user_data[USE_PHONE]);
		$_view_use_phone_1 = $_ary_use_phone[0];
		$_view_use_phone_2 = $_ary_use_phone[1];
		$_view_use_phone_3 = $_ary_use_phone[2];

		$page_title_text = _LG_PT_MEMBER_MOD;
		$submit_btn_text = "유저 수정";

	}else{

		$page_title_text = _LG_PT_MEMBER_REG;
		$submit_btn_text = "유저 등록";

	}

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
.guide-profile-wrap{ width:100%; height:150px; margin-bottom:10px; }
.guide-profile{ width:150px; height:150px; border:1px solid #9096a3; border-radius:10px;  }
</STYLE>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">

			<form name='userForm' id='userForm' action='<?=_A_PATH_MEMBER_OK?>' method='post'>
			<? if( $_mode == "modify" ){ ?>
				<input type="hidden" name="a_mode" value="userModify">
				<input type="hidden" name="use_idx" value="<?=$user_data[USE_IDX]?>">
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="userNew">
			<? } ?>

			<table cellspacing="1" cellpadding="0" class="table-style">
				
				<tr>
					<th class="tds1">아이디</th>
					<td class="tds2">
					<!--
						<? if( $_mode == "modify" ){ ?>
							<b><?=$guide_data[GD_ID]?></b>
						<? }else{ ?>
							<input type='text' name='gd_id' id='gd_id' value="<?=$guide_data[GD_ID]?>" >
						<? } ?> 
					-->
						<input type='text' name='use_id' id='use_id' <?if( $_mode == "modify" ){?> readonly <?}?> value="<?=$user_data[USE_ID]?>" >
					</td>
				</tr>

				<tr>
					<th class="tds1">이름</th>
					<td class="tds2"><input type='text' name='use_name' id='use_name' value="<?=$user_data[USE_NAME]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">영문이름</th>
					<td class="tds2"><input type='text' name='use_name_en_l' id='use_name_en_l' value="<?=$user_data[USE_NAME_EG_L]?>" >
									 <input type='text' name='use_name_en_f' id='use_name_en_f' value="<?=$user_data[USE_NAME_EG_F]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">생년월일</th>
					<td class="tds2">					 
						<select  name='use_birth1' id='use_birth1' style="width:60px;"  >
						<?
						for($i = 1950 ; $i <= $gva_nowtime_y;  $i++ ){
						?>
							<option value='<?=$i?>' <? if( $_view_use_birth_y == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?> 
						</select> 년
						<select  name='use_birth2' id='use_birth2' style="width:40px;"  >
						<?
						for($i = 1 ; $i <= 12 ;  $i++ ){
						?>
							<option value='<?=$i?>'<? if( $_view_use_birth_m == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?> 
						</select> 월
						<select  name='use_birth3' id='use_birth3' style="width:40px;"  >
						<?
						for($i = 1 ; $i <= 31 ;  $i++ ){
						?>
							<option value='<?=$i?>' <? if( $_view_use_birth_d == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?> 
						</select> 일
					</td>      
				</tr>

				<tr>
					<th class="tds1">핸드폰 번호</th>
					<td class="tds2">
						<input type='text' name='use_phone1' id='use_phone1' value="<?=$_view_use_phone_1?>" style="width:40px;"> - 
						<input type='text' name='use_phone2' id='use_phone2' value="<?=$_view_use_phone_2?>" style="width:40px;"> -
						<input type='text' name='use_phone3' id='use_phone3' value="<?=$_view_use_phone_3?>" style="width:40px;">
					</td>
				</tr>
				<tr>
					<th class="tds1">이메일</th>
					<td class="tds2"><input type='text' name='use_mail' id='use_mail' value="<?=$user_data[USE_MAIL]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">카카오 아이디</th>
					<td class="tds2"><input type='text' name='use_kakao' id='use_kakao' value="<?=$user_data[USE_KAKAO]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">라인 아이디</th>
					<td class="tds2"><input type='text' name='use_line' id='use_line' value="<?=$user_data[USE_LINE]?>" ></td>
				</tr>
				<tr>
					<th class="tds1">메모</th>
					<td class="tds2">
						<textarea name='use_memo' id='use_memo' ><?=$user_data[USE_MEMO]?></textarea>
					</td>
				</tr>

			</table>
			</form>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_MEMBER_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doUserSubmit();" > 
						<i class="far fa-check-circle"></i>
						<?=$submit_btn_text?>
					</button>
				</ul>
			</div>
		
		</div>

		<div style="height:60px;"></div>
	</div>
</div>

<script type="text/javascript"> 
<!-- 
// Submit
function doUserSubmit(){

	var form = document.userForm;
	form.submit();
	
}


</script> 

<?
include "../layout/footer.php";
exit;
?>