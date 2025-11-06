<?
$pageGroup = "config";
$pageName = "config_developer";

include "../lib/inc_common.php";

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
</STYLE>
<div id="contents_head">
	<h1>개발자 설정</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">
			<form name='developerForm' id='developerForm' action='<?=_A_PATH_CONFIG_DEVELOPER_OK?>' method='post'>
			<input type="hidden" name="action_mode" value="developer">
			<table cellspacing="1" cellpadding="0" class="table-style">

				<tr>
					<th class="tds1">어드민 모드</th>
					<td class="tds2">
						<label><input type="radio" name="ws_mode" value="travel" <? if( _GLOB_WS_MODE=="travel" ) echo "checked"; ?>>여행사</label>
						<label><input type="radio" name="ws_mode" value="community" <? if( _GLOB_WS_MODE=="community" ) echo "checked"; ?>>커뮤니티</label>
						<label><input type="radio" name="ws_mode" value="homepage" <? if( _GLOB_WS_MODE=="homepage" ) echo "checked"; ?>>홈페이지</label>
					</td>
				</tr>
				<tr>
					<th class="tds1">사이트 코드</th>
					<td class="tds2">
						_GLOB_WS_CODE<br>
						<input type='text' name='ws_code' id='ws_code' value="<?=_GLOB_WS_CODE?>" >
					</td>
				</tr>

				<tr>
					<th class="tds1">어드민 사이트명</th>
					<td class="tds2">
						_A_GLOB_SITENAME<br>
						<input type='text' name='sitename' id='sitename' value="<?=_A_GLOB_SITENAME?>" >
<!-- 
						<div class="explanation">
							<ul>
								<li>하단 카피라이터</li>
							</ul>
						</div>
 -->
					</td>
				</tr>

				<tr>
					<th class="tds1">어드민 디자인</th>
					<td class="tds2">
						<table cellspacing="1" cellpadding="0" class="table-style">
							<tr>
								<th class="tds1">어드민 상단로고</th>
								<td class="tds2">
									<input type='text' name='logofile' id='logofile' value="<?=_A_GLOB_LOGOFILE?>" >파일명
								</td>
							</tr>
							<tr>
								<th class="tds1">어드민 로그인 로고</th>
								<td class="tds2">
									<input type='text' name='logofile_login' id='logofile_login' value="<?=_A_GLOB_LOGOFILE_LOGIN?>" >파일명
								</td>
							</tr>
							<tr>
								<th class="tds1">브라우저 타이틀</th>
								<td class="tds2">
									<input type='text' name='browser_title' id='browser_title' value="<?=_A_GLOB_BROWSER_TITEL?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">하단 카피라이트</th>
								<td class="tds2">
									<input type='text' name='copyright' id='copyright' value="<?=_A_GLOB_COPYRIGHT?>" >
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<th class="tds1">사용메뉴</th>
					<td class="tds2">
						여행사(너바나)
						<table cellspacing="1" cellpadding="0" class="table-style">
							<tr>
								<th class="tds1">부킹관리</th>
								<td class="tds2">
									<input type="checkbox" name="gnb_active_booking" value="on" <? if( _A_GLOB_GNB_ACTIVE_BOOKING=="on" ) echo "checked"; ?>> 사용
								</td>
							</tr>
							<tr>
								<th class="tds1">회원관리</th>
								<td class="tds2">
									가이드 : <input type="checkbox" name="gnb_active_member_guide" value="on" <? if( _A_GLOB_GNB_ACTIVE_MEMBER_GUIDE=="on" ) echo "checked"; ?>> 사용
								</td>
							</tr>
							<tr>
								<th class="tds1">파트너관리</th>
								<td class="tds2">
									<input type="checkbox" name="gnb_active_partner" value="on" <? if( _A_GLOB_GNB_ACTIVE_PARTNER=="on" ) echo "checked"; ?>> 사용
								</td>
							</tr>
							<tr>
								<th class="tds1">가격비교</th>
								<td class="tds2">
									<input type="checkbox" name="gnb_active_comparison" value="on" <? if( _A_GLOB_GNB_ACTIVE_COMPARISON=="on" ) echo "checked"; ?>> 사용
								</td>
							</tr>
							<tr>
								<th class="tds1">상품관리</th>
								<td class="tds2">
									<div>
										<ul>일반상품 : <input type="checkbox" name="gnb_active_product2" value="on" <? if( _A_GLOB_GNB_ACTIVE_PRODUCT2=="on" ) echo "checked"; ?>> 사용</ul>
										<ul>여행사상품 : <input type="checkbox" name="gnb_active_product" value="on" <? if( _A_GLOB_GNB_ACTIVE_PRODUCT=="on" ) echo "checked"; ?>> 사용</ul>
									</div>
								</td>
							</tr>
							<tr>
								<th class="tds1">설정관리</th>
								<td class="tds2">
									이동경로 지정 : <input type='text' name='gnb_dir_config' id='gnb_dir_config' value="<?=_A_GLOB_GNB_DIR_CONFIG?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">OSI 메뉴</th>
								<td class="tds2">
									<input type="checkbox" name="gnb_active_osi" value="on" <? if( _A_GLOB_GNB_ACTIVE_OSI=="on" ) echo "checked"; ?>> 사용
								</td>
							</tr>
						</table>

					</td>
				</tr>

				<tr>
					<th class="tds1">회원</th>
					<td class="tds2">
						<table cellspacing="1" cellpadding="0" class="table-style">
							<tr>
								<th class="tds1">아이디를 이메일로 사용</th>
								<td class="tds2">
									<input type="checkbox" name="sys_user_email_id" value="on" <? if( _GLOB_SYS_USER_EMAIL_ID=="on" ) echo "checked"; ?>> 사용
								</td>
							</tr>
							<tr>
								<th class="tds1">실명 사용</th>
								<td class="tds2">
									<input type="checkbox" name="sys_real_name" value="on" <? if( _GLOB_SYS_REAL_NAME=="on" ) echo "checked"; ?>> 사용
								</td>
							</tr>
						</table>
					</td>
				</tr>



				<tr>
					<th class="tds1">서버</th>
					<td class="tds2">
						<table cellspacing="1" cellpadding="0" class="table-style">
							<tr>
								<th class="tds1">계정용량</th>
								<td class="tds2">
									<input type='text' name='d_capacity_total' id='d_capacity_total' value="<?=_A_GLOB_D_CAPACITY_TOTAL?>" style="width:200px"> MB
								</td>
							</tr>
							<tr>
								<th class="tds1">계정 표시용량</th>
								<td class="tds2">
									<input type='text' name='d_capacity_text' id='d_capacity_text' value="<?=_A_GLOB_D_CAPACITY_TEXT?>" >
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<th class="tds1">시스템</th>
					<td class="tds2">
						<table cellspacing="1" cellpadding="0" class="table-style">
							<tr>
								<th class="tds1">전용함수파일</th>
								<td class="tds2">
									<input type='text' name="individual_function" id="individual_function" value="<?=_GLOB_SYS_INDIVIDUAL_FUNCTION?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">전용변수파일</th>
								<td class="tds2">
									<input type='text' name="individual_variable" id="individual_variable" value="<?=_GLOB_SYS_INDIVIDUAL_VARIABLE?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">설치폴더</th>
								<td class="tds2">
									어드민 ( _GLOB_SYS_FOLDER_DIR_ADMIN )<br><input type='text' name="sys_folder_dir_admin" id="sys_folder_dir_admin" value="<?=_GLOB_SYS_FOLDER_DIR_ADMIN?>" >
									PC ( _GLOB_SYS_FOLDER_DIR_PC )<br> <input type='text' name="sys_folder_dir_pc" id="sys_folder_dir_pc" value="<?=_GLOB_SYS_FOLDER_DIR_PC?>" >
									모바일 ( _GLOB_SYS_FOLDER_DIR_MOBILE )<br> <input type='text' name="sys_folder_dir_mobile" id="sys_folder_dir_mobile" value="<?=_GLOB_SYS_FOLDER_DIR_MOBILE?>" >
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<th class="tds1">프론트</th>
					<td class="tds2">
						<table cellspacing="1" cellpadding="0" class="table-style">
							<tr>
								<th class="tds1">메인 페이지</th>
								<td class="tds2">
									PC ( _GLOB_INDEX_PATH ) <br><input type='text' name='index_path' id='index_path' value="<?=_GLOB_INDEX_PATH?>" >
									모바일 ( _GLOB_INDEX_PATH_MOBILE ) <br><input type='text' name='index_path_mobile' id='index_path_mobile' value="<?=_GLOB_INDEX_PATH_MOBILE?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">스킨</th>
								<td class="tds2">
									PC ( _GLOB_SKIN_NAME ) <br><input type='text' name='skin_name' id='skin_name' value="<?=_GLOB_SKIN_NAME?>" >
									모바일 ( _GLOB_SKIN_NAME_MOBILE ) <br><input type='text' name='skin_name_mobile' id='skin_name_mobile' value="<?=_GLOB_SKIN_NAME_MOBILE?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">스킨개별설정</th>
								<td class="tds2">
									/skin/스킨폴더명/<b>config.skin.php</b>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</form>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">
<!-- 
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_MEMBER_A_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
 -->
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doSubmit();" > 
						<i class="far fa-check-circle"></i>
						수정하기
					</button>
				</ul>
			</div>
		
		</div>

	</div>
</div>

<script type="text/javascript"> 
<!-- 
// Submit
function doSubmit(){
	$("#developerForm").submit();
}
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>