<?
$pageGroup = "member";
$pageName = "admin_reg";

include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_ad_idx = securityVal($key);

	if( $_mode == "modify" ){
		$admin_data = wepix_fetch_array(wepix_query_error("select * from "._DB_ADMIN." where AD_IDX = '".$_ad_idx."' "));
		$_ary_ad_birth = explode("-",$admin_data[AD_BIRTH]);
		$_view_ad_birth_y = $_ary_ad_birth[0];
		$_view_ad_birth_m = $_ary_ad_birth[1];
		$_view_ad_birth_d = $_ary_ad_birth[2];
		
		$_ary_ad_phone = explode("-",$admin_data[AD_PHONE]);
		$_view_ad_phone_1 = $_ary_ad_phone[0];
		$_view_ad_phone_2 = $_ary_ad_phone[1];
		$_view_ad_phone_3 = $_ary_ad_phone[2];

		$page_title_text = "운영자 수정";
		$submit_btn_text = "운영자 수정";
	}else{
		$page_title_text = "운영자 등록";
		$submit_btn_text = "운영자 등록";
	}

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
</STYLE>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">

			<form name='adminForm' id='adminForm' action='<?=_A_PATH_MEMBER_OK?>' method='post'>
			<? if( $_mode == "modify" ){ ?>
				<input type="hidden" name="a_mode" value="adminModify">
				<input type="hidden" name="ad_idx" value="<?=$admin_data[AD_IDX]?>">
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="adminNew">
			<? } ?>

			<table cellspacing="1" cellpadding="0" class="table-style">
				
				<tr>
					<th class="tds1">아이디</th>
					<td class="tds2">
					<!--
						<? if( $_mode == "modify" ){ ?>
							<b><?=$admin_data[AD_ID]?></b>
						<? }else{ ?>
							<input type='text' name='ad_id' id='ad_id' value="<?=$admin_data[AD_ID]?>" >
						<? } ?>
					-->
						<input type='text' name='ad_id' id='ad_id' <?if( $_mode == "modify" ){?> readonly <?}?> value="<?=$admin_data[AD_ID]?>" >
					</td>
				</tr>

				<tr>
					<th class="tds1">패스워드</th>
					<td class="tds2">
						<div class="pw-reg-box">
						<!--
							<? if( $_mode == "modify" ){ ?>
							<ul><label><input type="checkbox" name="new_pw" value="Y"> 패스워드 변경</label></ul>
							<? } ?>
						-->
							
								<ul <? if( $_mode != "modify" ){ ?> style='display:none;' <? } ?>>
								<label><input type="checkbox" name="new_pw" <?if( $_mode != "modify" ){?> checked <?}?> value="Y"> 패스워드 변경</label></ul>
							<ul><input type='password' name='a_pw' id='a_pw' placeholder="패스워드"></ul>
							<ul><input type='password' name='a_pw2' id='a_pw2' placeholder="패스워드 확인"></ul>
						</div>
					</td>
				</tr>

				<tr>
					<th class="tds1">이름</th>
					<td class="tds2"><input type='text' name='ad_name' id='ad_name' value="<?=$admin_data[AD_NAME]?>" ></td>
				</tr>
				<tr>
					<th class="tds1">영문 이름</th>
					<td class="tds2"><input type='text' name='ad_name_eg' id='ad_name_eg' value="<?=$admin_data[AD_NAME_EG]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">닉네임</th>
					<td class="tds2"><input type='text' name='ad_nick' id='ad_nick' value="<?=$admin_data[AD_NICK]?>" ></td>
				</tr>
				<tr>
					<th class="tds1">시스템 언어</th>
					<td class="tds2">
						<label><input type="radio" name="ad_lang" value="kor" <? if($_ad_lang=="kor") echo "checked"; ?>>한국어</label>&nbsp;&nbsp;
						<label><input type="radio" name="ad_lang" value="usa" <? if($_ad_lang=="usa") echo "checked"; ?>>영어</label>&nbsp;&nbsp;
						<label><input type="radio" name="ad_lang" value="tha" <? if($_ad_lang=="tha") echo "checked"; ?>>태국어</label>&nbsp;&nbsp;
						<label><input type="radio" name="ad_lang" value="vnm" <? if($_ad_lang=="vnm") echo "checked"; ?>>베트남어</label>
					</td>
				</tr>

				<tr>
					<th class="tds1">생년월일</th>
					<td class="tds2">					 
						<select  name='ad_birth1' id='ad_birth1' style="width:60px;"  >
						<?
						for($i = 1950 ; $i <= $gva_nowtime_y;  $i++ ){
						?>
							<option value='<?=$i?>' <? if( $_view_ad_birth_y == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?> 
						</select> 년
						<select  name='ad_birth2' id='ad_birth2' style="width:40px;"  >
						<?
						for($i = 1 ; $i <= 12 ;  $i++ ){
						?>
							<option value='<?=$i?>'<? if( $_view_ad_birth_m == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?> 
						</select> 월
						<select  name='ad_birth3' id='ad_birth3' style="width:40px;"  >
						<?
						for($i = 1 ; $i <= 31 ;  $i++ ){
						?>
							<option value='<?=$i?>' <? if( $_view_ad_birth_d == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?> 
						</select> 일
					</td>      
				</tr>

				<tr>
					<th class="tds1">핸드폰 번호</th>
					<td class="tds2">
						<input type='text' name='ad_phone1' id='ad_phone1' value="<?=$_view_ad_phone_1?>" style="width:40px;"> - 
						<input type='text' name='ad_phone2' id='ad_phone2' value="<?=$_view_ad_phone_2?>" style="width:40px;"> -
						<input type='text' name='ad_phone3' id='ad_phone3' value="<?=$_view_ad_phone_3?>" style="width:40px;">
					</td>
				</tr>

				<tr>
					<th class="tds1">이메일</th>
					<td class="tds2"><input type='text' name='ad_mail' id='ad_mail' value="<?=$admin_data[AD_MAIL]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">카카오 아이디</th>
					<td class="tds2"><input type='text' name='ad_kakao' id='ad_kakao' value="<?=$admin_data[AD_KAKAO]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">라인 아이디</th>
					<td class="tds2"><input type='text' name='ad_line' id='ad_line' value="<?=$admin_data[AD_LINE]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">등급</th>
					<td class="tds2">
						<select name='ad_level' id='ad_level' >
						<?
						for($i=1; $i<=10; $i++ ){
						?>
							<option value='<?=$i?>' <? if( $admin_data[AD_LEVEL] == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?>
							<option value='100' <? if( $admin_data[AD_LEVEL]=="100"  ) echo "selected"; ?>>100</option>
						</select>
					</td>
				</tr>

				<tr>
					<th class="tds1">메모</th>
					<td class="tds2">
						<textarea name='ad_memo' id='ad_memo' ><?=$admin_data[AD_MEMO]?></textarea>
					</td>
				</tr>

			</table>
			</form>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_MEMBER_A_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doAdminSubmit();" > 
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
function doAdminSubmit(){
	var form = document.adminForm;
 	if(sand()){
		form.submit();
	 }
}

// 유효성 검사
  function sand(){

	    var form1 = document.adminForm;
        //아이디 입력여부
  	    if (form1.ad_id.value == "") {
			alert("아이디를 입력하지 않았습니다.");
			form1.ad_id.focus();
			return false;
		}

		if(form1.new_pw.checked == true){
			//비밀번호 입력여부 체크
			if (form1.a_pw.value == "") {
				alert("비밀번호를 입력하지 않았습니다.");
				form1.a_pw.focus();
				return false;
			}
	 
			//비밀번호와 비밀번호 확인 일치여부 체크
			if (form1.a_pw.value != form1.a_pw2.value) {
				alert("비밀번호가 일치하지 않습니다")
				form1.a_pw.value = "";
				form1.a_pw.focus();
			   return false;
			} 
		}

		/*****이름 유효성 검사 *****/
        if (form1.ad_name.value == "") {
            alert("이름을 입력하지 않았습니다.");
            form1.ad_name.focus();
            return false;
        }

        if (form1.ad_name_eg.value == "") {
            alert("영문이름을 입력하지 않았습니다.");
            form1.ad_name_eg.focus();
            return false;
        }


        if (form1.ad_nick.value == "") {
            alert("닉네임을 입력하지 않았습니다.");
            form1.ad_nick.focus();
            return false;
        }

	return true;

  }

//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>