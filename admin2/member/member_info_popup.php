<?
$pageGroup = "member";
$pageName = "member_info_popup";

include "../lib/inc_common.php";

	$_id = securityVal($id);
	$_mode = securityVal($mode);

	if( $_mode == "admin"){
		$admin_data = wepix_fetch_array(wepix_query_error("select * from "._DB_ADMIN." where AD_ID = '".$_id."' "));

		$_show_id = $admin_data[AD_ID];
		$_show_name = $admin_data[AD_NAME];
		$_show_nick = $admin_data[AD_NICK];
		$_show_level = $admin_data[AD_LEVEL];

	}else{
		$user_data = wepix_fetch_array(wepix_query_error("select * from "._DB_MEMBER." where USE_ID = '".$_id."' "));

		$_show_id = $user_data[USE_ID];
		$_show_name = $user_data[USE_NAME];
		$_show_nick = $user_data[USE_NICKNAME];
		$_show_level = $user_data[USE_LEVEL];
	}



	$popup_browser_title = "회원관리 - ( ".$_id." )";

include "../layout/header_popup.php";
?>
<STYLE TYPE="text/css">
.crm-header{ width:100%; height:30px; background-color:#303742; }
.crm-wrap{ width:100%; height:calc(100% - 30px); }
.crm-menu-wrap{ width:200px; border-right:1px solid #9c9fae; }
.crm-gap{ width:5px; border-right:1px solid #9c9fae; }
.crm-body{ padding:20px 20px 20px; box-sizing:border-box; background-color:#dddddd; }

.crm-user-icon{ width:100%; padding:15px 0 0 0; box-sizing:border-box; }
.crm-user-info{ width:100%; box-sizing:border-box; }
.crm-user-nick{ font-family: 'Godo', sans-serif; font-size:15px;  }

.crm-menu{ width:100%; border-top:1px solid #9c9fae; }
.crm-menu ul{ width:100%; height:30px; line-height:30px; padding:0 0 0 15px; box-sizing:border-box; border-bottom:1px solid #9c9fae;}

.crm-title{ height:30px; line-height:30px; font-family: 'Godo', sans-serif; font-size:17px; }
</STYLE>
<div class="crm-header">
</div>
<div class="crm-wrap display-table">
	<ul class="crm-menu-wrap display-table-cell v-align-top">
		<div class="crm-user-icon text-center">
			<i style="font-size:70px; color:#999;" class="fas fa-user-circle"></i>
		</div>
		<div class="crm-user-info m-t-10">
			<ul class="crm-user-nick text-center"><?=$_show_nick?></ul>
			<ul class="crm-user-id m-t-5 text-center"><?=$user_data[USE_ID]?></ul>
		</div>
		<div class="crm-menu m-t-20">
			<ul>CRM 메인</ul>
			<ul>회원상세정보</ul>
			<ul>접속로그</ul>
		</div>
	</ul>
	<ul class="crm-gap display-table-cell"></ul>
	<ul class="crm-body display-table-cell v-align-top">
		<div id="crm_body">
		
<STYLE TYPE="text/css">
.crm-detail-info{}
</STYLE>
<div class="crm-title">회원상세 정보</div> 
<div class="crm-detail-info">
	<? if($user_data[USE_MOD_DATE] > 0 ) echo "최종수정 : ".date("Y.m.d H:i:s",$user_data[USE_MOD_DATE]); ?>
	<form name='userForm' id='userForm' action='<?=_A_PATH_MEMBER_OK?>' method='post'>
	<input type="hidden" name="a_mode" value="userModifyPopup">
	<input type="hidden" name="user_idx" value="<?=$user_data[USE_IDX]?>">
	<input type="hidden" name="user_id" value="<?=$_show_id?>">
	<table class="table-style">
		<tr>
			<th class="tds1">아이디</th>
			<td class="tds2" colspan="3"><?=$_show_id?></td>
		</tr>
		<tr>
			<th class="tds1">이름</th>
			<td class="tds2">
				<input type='text' name='user_name' id='user_name' value="<?=$_show_name?>" >
			</td>
			<th class="tds1">닉네임</th>
			<td class="tds2">
				<input type='text' name='user_nickname' id='user_nickname' value="<?=$_show_nick?>" >
			</td>
		</tr>

		<tr>
			<th class="tds1">레벨</th>
			<td class="tds2">
				<input type='text' name='user_level' id='user_level' value="<?=$user_data[USE_LEVEL]?>" >
			</td>
			<th class="tds1">상태</th>
			<td class="tds2">
				<? if( $user_data[USE_STATE] == "21" ){ ?>
					탈퇴회원 | <?=$user_data[USE_WITHDRAW_DATE]?> 
				<?}else{?>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="doWithdraw();">회원탈퇴</button>
				<? } ?>
			</td>
		</tr>
		<tr>
			<td colspan="4" style="border:none !important; margin:0 !important; padding:0 !important; height:5px; "></td>
		</tr>
		<tr>
			<th class="tds1">비밀번호</th>
			<td class="tds2" colspan="3">
				<input type='text' name='user_pw' id='user_pw' style="width:200px;">
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="doPwModify();">비밀번호 변경</button>
			</td>
		</tr>
		<tr>
			<td colspan="4" style="border:none !important; margin:0 !important; padding:0 !important; height:5px; "></td>
		</tr>
		<tr>
			<th class="tds1">회원인증</th>
			<td class="tds2" colspan="3">
				<select name="user_state">
					<option value="0" <? if($user_data[USE_STATE]==0) echo "selected"; ?>>탈퇴</option>
					<option value="1" <? if($user_data[USE_STATE]==1) echo "selected"; ?>>미인증</option>
					<option value="2" <? if($user_data[USE_STATE]==2) echo "selected"; ?>>인증 : 1단계</option>
					<option value="3" <? if($user_data[USE_STATE]==3) echo "selected"; ?>>인증 완료</option>
				</select>
			</td>
		</tr>

	</table>
	</form> 

</div>
<div class="text-center m-t-10">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-submit" onclick="goSave();" > 
		<i class="far fa-check-circle"></i> 수정
	</button>
</div>

<script type="text/javascript"> 
<!-- 
function goSave(){
	$("#userForm").submit();
}

function doPwModify(){

	var user_pw = $("#user_pw").val();

	if( user_pw == "" ||  user_pw == null ){
		$('#modal_alert_msg').html("비밀번호를 입력해주세요!");
		$('#modal-alert').modal({show: true,backdrop:'static'});
		return false; 
	}

	$.ajax({
		url: "<?=_A_PATH_MEMBER_OK?>",
		data: {
			"a_mode":"userCrmPwModify",
			"ajax_mode":"on",
			"user_idx":"<?=$user_data[USE_IDX]?>",
			"user_pw":user_pw
		},
		type: "POST",
		dataType: "text",
		success: function(getdata){
			if (getdata){
				redatawa = getdata.split('|');
				ckcode = redatawa[1];
				ckmsg = redatawa[2];
				if(ckcode == "Processing_Complete"){
					$("#user_pw").val("");
					$('#modal_alert_msg').html(ckmsg);
					$('#modal-alert').modal({show: true,backdrop:'static'});
				}else if(ckcode == "Erorr"){
					//ajaxLoadingErorrClose();
					$('#modal_alert_msg').html(ckmsg);
					$('#modal-alert').modal({show: true,backdrop:'static'});
				}else{

				}
			}
		},
		error: function(){
		}
	});
}

//회원탈퇴
function doWithdraw(){

	$.ajax({
		url: "<?=_A_PATH_MEMBER_OK?>",
		data: {
			"a_mode":"userWithdraw",
			"ajax_mode":"on",
			"user_idx":"<?=$user_data[USE_IDX]?>"
		},
		type: "POST",
		dataType: "text",
		success: function(getdata){
			if (getdata){
				redatawa = getdata.split('|');
				ckcode = redatawa[1];
				ckmsg = redatawa[2];
				if(ckcode == "Processing_Complete"){
					$('#modal_alert_msg').html(ckmsg);
					$('#modal-alert').modal({show: true,backdrop:'static'});
				}else if(ckcode == "Erorr"){
					//ajaxLoadingErorrClose();
					$('#modal_alert_msg').html(ckmsg);
					$('#modal-alert').modal({show: true,backdrop:'static'});
				}else{

				}
			}
		},
		error: function(){
		}
	});
}
//--> 
</script> 

		</div>
	</ul>
</div>
<?
include "../layout/footer_popup.php";
exit;
?>