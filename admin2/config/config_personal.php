<?
$pageGroup = "config";
$pageName = "config_personal";

include "../lib/inc_common.php";

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
</STYLE>

<div id="contents_head">
	<h1>개인 정보</h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">

			<form name='adminForm' id='adminForm' action='<?=_A_PATH_CONFIG_OK?>' method='post'>
				<input type="hidden" name="action_mode" value="adminModify">


			<table cellspacing="1" cellpadding="0" class="table-style">
				
				<tr>
					<th class="tds1">아이디</th>
					<td class="tds2"><b><input type='text'  readonly name='ad_id' id='ad_id' value="<?=$_ad_id?>"></b></td>
				</tr>

				<tr>
					<th class="tds1">패스워드</th>
					<td class="tds2">
						<div class="pw-reg-box">
							<ul><label><input type="checkbox" name="new_pw" value="Y"> 패스워드 변경</label></ul>
							<ul><input type='password' name='a_pw' id='a_pw' placeholder="패스워드"></ul>
							<ul><input type='password' name='a_pw2' id='a_pw2' placeholder="패스워드 확인"></ul>
						</div>
					</td>
				</tr>

				<tr>
					<th class="tds1">이름</th>
					<td class="tds2"><input type='text' name='ad_name' id='ad_name' value="<?=$_ad_name?>" ></td>
				</tr>
				<tr>
					<th class="tds1">영문 이름</th>
					<td class="tds2"><input type='text' name='ad_name_eg' id='ad_name_eg' value="<?=$_ad_name_eg?>" ></td>
				</tr>

				<tr>
					<th class="tds1">닉네임</th>
					<td class="tds2"><input type='text' name='ad_nick' id='ad_nick' value="<?=$_ad_nick?>" ></td>
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
					<td class="tds2"><input type='text' name='ad_mail' id='ad_mail' value="<?=$_ad_mail?>" ></td>
				</tr>

				<tr>
					<th class="tds1">카카오 아이디</th>
					<td class="tds2"><input type='text' name='ad_kakao' id='ad_kakao' value="<?=$_ad_kakao?>" ></td>
				</tr>

				<tr>
					<th class="tds1">라인 아이디</th>
					<td class="tds2"><input type='text' name='ad_line' id='ad_line' value="<?=$_ad_line?>" ></td>
				</tr>


			</table>
			</form>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">

				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doAdminSubmit();" > 
						<i class="far fa-check-circle"></i>
						수정하기
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