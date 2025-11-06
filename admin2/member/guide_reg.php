<?
$pageGroup = "member";
$pageName = "guide_reg";

include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_gd_idx = securityVal($key);

	if( $_mode == "modify" ){
		$guide_data = wepix_fetch_array(wepix_query_error("select * from "._DB_GUIDE." where GD_IDX = '".$_gd_idx."' "));
		$_ary_gd_birth = explode("-",$guide_data[GD_BIRTH]);
		$_view_gd_birth_y = $_ary_gd_birth[0];
		$_view_gd_birth_m = $_ary_gd_birth[1];
		$_view_gd_birth_d = $_ary_gd_birth[2];
		
		$_ary_gd_phone = explode("-",$guide_data[GD_PHONE]);
		$_view_gd_phone_1 = $_ary_gd_phone[0];
		$_view_gd_phone_2 = $_ary_gd_phone[1];
		$_view_gd_phone_3 = $_ary_gd_phone[2];

		$page_title_text = "가이드 수정";
		$submit_btn_text = "가이드 수정";
	}else{
		$page_title_text = "가이드 등록";
		$submit_btn_text = "가이드 등록";
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

			<form name='guideForm' id='guideForm' action='<?=_A_PATH_MEMBER_OK?>' method='post'>
			<? if( $_mode == "modify" ){ ?>
				<input type="hidden" name="a_mode" value="guideModify">
				<input type="hidden" name="gd_idx" value="<?=$guide_data[GD_IDX]?>">
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="guideNew">
			<? } ?>

			<? if( $_mode == "modify" ){ ?>
			<div class="guide-profile-wrap" >
				<div class="guide-profile" style="background:url('/data/guide/profile/<?=$guide_data[GD_PROFILE_THUM]?>') no-repeat; background-size:100% 100%;"></div>
			</div>
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
						<input type='text' name='gd_id' id='gd_id' <?if( $_mode == "modify" ){?> readonly <?}?> value="<?=$guide_data[GD_ID]?>" >
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
							<ul><input type='password' name='g_pw' id='g_pw' placeholder="패스워드"></ul>
							<ul><input type='password' name='g_pw2' id='g_pw2' placeholder="패스워드 확인"></ul>
						</div>
					</td>
				</tr>

				<tr>
					<th class="tds1">이름</th>
					<td class="tds2"><input type='text' name='gd_name' id='gd_name' value="<?=$guide_data[GD_NAME]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">닉네임</th>
					<td class="tds2"><input type='text' name='gd_nick' id='gd_nick' value="<?=$guide_data[GD_NICK]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">생년월일</th>
					<td class="tds2">					 
						<select  name='gd_birth1' id='gd_birth1' style="width:60px;"  >
						<?
						for($i = 1950 ; $i <= $gva_nowtime_y;  $i++ ){
						?>
							<option value='<?=$i?>' <? if( $_view_gd_birth_y == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?> 
						</select> 년
						<select  name='gd_birth2' id='gd_birth2' style="width:40px;"  >
						<?
						for($i = 1 ; $i <= 12 ;  $i++ ){
						?>
							<option value='<?=$i?>'<? if( $_view_gd_birth_m == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?> 
						</select> 월
						<select  name='gd_birth3' id='gd_birth3' style="width:40px;"  >
						<?
						for($i = 1 ; $i <= 31 ;  $i++ ){
						?>
							<option value='<?=$i?>' <? if( $_view_gd_birth_d == $i ) echo "selected"; ?>> <?=$i?> </option>
						<?}?> 
						</select> 일
					</td>      
				</tr>

				<tr>
					<th class="tds1">핸드폰 번호</th>
					<td class="tds2">
						<input type='text' name='gd_phone1' id='gd_phone1' value="<?=$_view_gd_phone_1?>" style="width:40px;"> - 
						<input type='text' name='gd_phone2' id='gd_phone2' value="<?=$_view_gd_phone_2?>" style="width:40px;"> -
						<input type='text' name='gd_phone3' id='gd_phone3' value="<?=$_view_gd_phone_3?>" style="width:40px;">
					</td>
				</tr>
                <tr>
					<th class="tds1">CHAT ID</th>
					<td class="tds2"><input type='text' name='gd_chat_id' id='gd_chat_id' value="<?=$guide_data[GD_CHAT_ID]?>" ></td>
				</tr>
				<tr>
					<th class="tds1">이메일</th>
					<td class="tds2"><input type='text' name='gd_mail' id='gd_mail' value="<?=$guide_data[GD_MAIL]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">카카오 아이디</th>
					<td class="tds2"><input type='text' name='gd_kakao' id='gd_kakao' value="<?=$guide_data[GD_KAKAO]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">라인 아이디</th>
					<td class="tds2"><input type='text' name='gd_line' id='gd_line' value="<?=$guide_data[GD_LINE]?>" ></td>
				</tr>
				<tr>
					<th class="tds1">마스터</th>
					<td class="tds2">
						<label><input type="radio" name="gd_super"  value="Y" <? if( $guide_data[GD_SUPER]=="Y") echo "checked"; ?>> 마스터</label>
						<label><input type="radio" name="gd_super"  value="N" <? if( $guide_data[GD_SUPER]=="N" OR $guide_data[GD_SUPER] =="" ) echo "checked"; ?>> 일반</label>
					</td>
				</tr>
				<tr>
					<th class="tds1">노출</th>
					<td class="tds2">
						<label><input type="radio" name="gd_active"  value="Y" <? if( $guide_data[GD_VIEW_YN]=="Y" OR $guide_data[GD_VIEW_YN] =="") echo "checked"; ?>> Y </label>
						<label><input type="radio" name="gd_active"  value="N" <? if( $guide_data[GD_VIEW_YN]=="N" ) echo "checked"; ?>> N</label>
					</td>
				</tr>
				<tr>
					<th class="tds1">메모</th>
					<td class="tds2">
						<textarea name='gd_memo' id='gd_memo' ><?=$guide_data[GD_MEMO]?></textarea>
					</td>
				</tr>

				<tr>
					<th class="tds1">승인 상태</th>
					<td class="tds2">
						<label><input type="radio" name="gd_state" value="0" <? if( $guide_data[GD_STATE]=="0" OR !$guide_data[GD_STATE] ) echo "checked"; ?> >대기</label>
						<label><input type="radio" name="gd_state"  value="1" <? if( $guide_data[GD_STATE]=="1") echo "checked"; ?>> 승인</label>
						<label><input type="radio" name="gd_state"  value="2" <? if( $guide_data[GD_STATE]=="2") echo "checked"; ?>> 차단</label>
					</td>
				</tr>
			</table>
			</form>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_MEMBER_G_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doGuideSubmit();" > 
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
function doGuideSubmit(){
	var form = document.guideForm;
	if(sand()){
		form.submit();
	}
}
// 유효성 검사
  function sand(){

	    var form1 = document.guideForm;
        //아이디 입력여부
  	    if (form1.gd_id.value == "") {
			alert("아이디를 입력하지 않았습니다.");
			form1.gd_id.focus();
			return false;
		}

		if(form1.new_pw.checked == true){
			//비밀번호 입력여부 체크
			if (form1.g_pw.value == "") {
				alert("비밀번호를 입력하지 않았습니다.");
				form1.g_pw.focus();
				return false;
			}
	 
			//비밀번호와 비밀번호 확인 일치여부 체크
			if (form1.g_pw.value != form1.g_pw2.value) {
				alert("비밀번호가 일치하지 않습니다")
				form1.g_pw.value = "";
				form1.g_pw.focus();
			   return false;
			} 
		}

		/*****이름 유효성 검사 *****/
        if (form1.gd_name.value == "") {
            alert("이름을 입력하지 않았습니다.");
            form1.ad_name.focus();
            return false;
        }


        if (form1.gd_nick.value == "") {
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