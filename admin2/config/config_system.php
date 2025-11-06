<?
$pageGroup = "config";
$pageName = "config_system";

include "../lib/inc_common.php";

	$setting_data = wepix_fetch_array(wepix_query_error("select * from "._DB_SETTING." where SET_CODE = '"._GLOB_SITE_CODE."' "));

	$_view_setting_site_name = $setting_data[SET_SITE_NAME];
	$_view_setting_domain = $setting_data[SET_DOMAIN];

	$_view_setting_privacy_policy = $setting_data[SET_JOIN_PRIVACY_POLICY];
	$_view_setting_terms_of_use = $setting_data[SET_JOIN_TERMS_OF_USE];

	$_view_setting_marketing = $st_data[SET_JOIN_MARKETING];
	$_view_setting_join_nick_indispensable = $setting_data[SET_JOIN_NICK_INDISPENSABLE];
	
	$_view_setting_email_certify_active = $setting_data[SET_JOIN_EMAIL_CERTIFY_ACTIVE];
	$_view_setting_email_host = $setting_data[SET_EMAIL_HOST];
	$_view_setting_email_port = $setting_data[SET_EMAIL_PORT];
	$_view_setting_email_account = $setting_data[SET_EMAIL_ACCOUNT];
	$_view_setting_email_account_pw = $setting_data[SET_EMAIL_ACCOUNT_PW];
	$_view_setting_email_name = $setting_data[SET_EMAIL_NAME];
	$_view_setting_email_add = $setting_data[SET_EMAIL_ADD];

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>환경 설정</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<form name='form1' action='<?=_A_PATH_CONFIG_OK?>' method='post'>
		<input type='hidden' name='action_mode' value='configSystem'>
		<table cellspacing="1" cellpadding="0" class="table-style2 basic-form">
			<tr>
                <th>코드</th>
                <td colspan="3"><?=_GLOB_SITE_CODE?></td>
            </tr>
			<tr>
                <th>사이트 이름</th>
                <td colspan="3"><input type='text' name='site_name' id='site_name' value="<?=$_view_setting_site_name?>" style="width:200px;"></td>
            </tr>
			<tr>
                <th>사이트 도메인</th>
                <td colspan="3"><input type='text' name='domain' id='site_name' value="<?=$_view_setting_domain?>" style="width:200px;"></td>
            </tr>
            <tr>
                <th>개인정보 처리방침</th>
                <td colspan="3">
                    <!--<textarea name="personal_information" id="ir1" rows="10" cols="100" style="width:100%; height:400px; display:none;"><?=$data[SET_PERSONAL_INFORMATION]?></textarea>-->
                    <textarea name="st_privacy_policy" ><?=$_view_setting_privacy_policy?></textarea>
                </td>
            </tr>
            <tr>
                <th>이용약관</th>
                <td colspan="3">
                    <textarea name="st_terms_of_use"><?=$_view_setting_terms_of_use?></textarea>
                </td>
            </tr>
			<tr>
                <th>닉네임 사용</th>
                <td colspan="3">
                    <label><input type='checkbox' name="join_nick_indispensable" value="Y" <?if($_view_setting_join_nick_indispensable == 'Y'){ echo "checked"; }?>>필수</label>
                </td>
            </tr>
			<tr>
                <th>이메일 인증</th>
                <td colspan="3">
                    <label><input type='checkbox' name="email_certify_active" value="Y" <?if($_view_setting_email_certify_active == 'Y'){ echo "checked"; }?>>사용</label>
                </td>
            </tr>

			<tr>
                <th>이메일 세팅</th>
                <td colspan="3">
					<div>
						이메일 이름 : <input type='text' name='email_name' id='email_name' value="<?=$_view_setting_email_name?>" style="width:200px;">
						이메일 주소 : <input type='text' name='email_add' id='email_add' value="<?=$_view_setting_email_add?>" style="width:200px;">
					</div>
					<div class="m-t-5">
						호스트 : <input type='text' name='email_host' id='email_host' value="<?=$_view_setting_email_host?>" style="width:200px;">
						포트 : <input type='text' name='email_port' id='email_port' value="<?=$_view_setting_email_port?>" style="width:200px;">
						계정 : <input type='text' name='email_account' id='email_account' value="<?=$_view_setting_email_account?>" style="width:200px;">
						계정 비밀번호 : <input type='text' name='email_account_pw' id='email_account_pw' value="<?=$_view_setting_email_account_pw?>" style="width:200px;">
					</div>
                </td>
            </tr>

            
            </table>
			<div class="submitBtnWrap">
				<input type='button' value='수정' onclick="submit();" class="submitBtn">
			</div>
			</form>
	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>