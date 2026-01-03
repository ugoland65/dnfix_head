<style type="text/css">
.ad-profile-img2{ width:100px; height:100px; box-sizing:border-box;  overflow:hidden; border-radius:50%; }
.ad-profile-img2 img{ width:100%; }
</style>

	<form id="form1">

    <?php
        if( !empty($admin['idx']) ){ 
    ?>
	<input type="hidden" name="idx" value="<?=$admin['idx']?>" >
    <?php } ?>

	<table class="table-style border01 width-full">

	<?php
        if( !empty($admin['idx']) ){ 
    ?>
		<tr>
			<th style="width:150px;">아이디</th>
			<td colspan="3">
				<?=$admin['ad_id']?>
			</td>
		</tr>
		<tr>
			<th>패스워드</th>
			<td colspan="3">
				<input type='text' name='new_ad_pw'  value="" autocomplete="off" >
				<label><input type="checkbox" name="new_pw_change" value="ok" > 패스워드 변경시 체크</label>
			</td>
		</tr>
	<?php 
        } else { 
    ?>
		<tr>
			<th>아이디</th>
			<td colspan="3">
				<input type='text' name='ad_id'  value="" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th>패스워드</th>
			<td colspan="3">
				<input type='text' name='ad_pw'  value="" autocomplete="off" >
			</td>
		</tr>
	<?php } ?>

		<tr>
			<th>이름</th>
			<td colspan="3">
                <?php
                    if( !empty($admin['idx']) AND $auth['ad_level'] < 100){ 
                ?>
                <?=$admin['ad_name'] ?? '이름 미입력'?>
                <?php 
                    } else { 
                ?>
				<input type='text' name='ad_name'  value="<?=$admin['ad_name' ?? '']?>" autocomplete="off" >
                <?php } ?>
			</td>
		</tr>

        <tr>
			<th>재직상태</th>
			<td colspan="3">
                <?php
                    if( $auth['ad_level'] == 100){ 
                ?>
                    <label><input type="radio" name="ad_work_status" value="재직중" <? if( $admin['ad_work_status'] == '재직중' ) echo "checked";?> > 재직중</label>
                    <label><input type="radio" name="ad_work_status" value="휴직" <? if( $admin['ad_work_status'] == '휴직' ) echo "checked";?> > 휴직</label>
                    <label><input type="radio" name="ad_work_status" value="퇴사" <? if( $admin['ad_work_status'] == '퇴사' ) echo "checked";?> > 퇴사</label>
                    <label><input type="radio" name="ad_work_status" value="기타" <? if( $admin['ad_work_status'] == '기타' ) echo "checked";?> > 기타(오토, 위치, 기타)</label>
                <?php 
                    } else { 
                ?>
                    <?=$admin['ad_work_status'] ?? '재직상태 미입력'?>
                <?php } ?>
            </td>
        </tr>

        <tr>
			<th>직책</th>
			<td>
                <?php
                    if( $auth['ad_level'] == 100){ 
                ?>
                    <input type='text' name='ad_role'  value="<?=$admin['ad_role' ?? '']?>" autocomplete="off" >
                <?php 
                    } else { 
                ?>
                    <?=$admin['ad_role'] ?? '직책 미입력'?>
                <?php } ?>
            </td>
			<th>직함</th>
			<td>
                <?php
                    if( $auth['ad_level'] == 100){ 
                ?>
                    <input type='text' name='ad_title'  value="<?=$admin['ad_title' ?? '']?>" autocomplete="off" >
                <?php 
                    } else { 
                ?>
                    <?=$admin['ad_title'] ?? '직함 미입력'?>
                <?php } ?>
            </td>
        </tr>

        <tr>
			<th>부서</th>
			<td>
                <?php
                    if( $auth['ad_level'] == 100){ 
                ?>
                    <input type='text' name='ad_department'  value="<?=$admin['ad_department' ?? '']?>" autocomplete="off" >
                <?php 
                    } else { 
                ?>
                    <?=$admin['ad_department'] ?? '부서 미입력'?>
                <?php } ?>
            </td>
			<th>사번</th>
			<td>
                <?php
                    if( $auth['ad_level'] == 100){ 
                ?>
                    <input type='text' name='ad_employee_id'  value="<?=$admin['ad_employee_id' ?? '']?>" autocomplete="off" >
                <?php 
                    } else { 
                ?>
                    <?=$admin['ad_employee_id'] ?? '사번 미입력'?>
                <?php } ?>
            </td>
        </tr>

        <tr>
			<th>고용형태</th>
			<td colspan="3">
                <?php
                    if( $auth['ad_level'] == 100){ 
                ?>
                    <label><input type="radio" name="ad_job_type" value="임원" <? if( $admin['ad_job_type'] == '임원' ) echo "checked";?> > 임원</label>
                    <label><input type="radio" name="ad_job_type" value="정직원" <? if( $admin['ad_job_type'] == '정직원' ) echo "checked";?> > 정직원</label>
                    <label><input type="radio" name="ad_job_type" value="계약직" <? if( $admin['ad_job_type'] == '계약직' ) echo "checked";?> > 계약직</label>
                    <label><input type="radio" name="ad_job_type" value="파트타임" <? if( $admin['ad_job_type'] == '파트타임' ) echo "checked";?> > 파트타임</label>
                    <label><input type="radio" name="ad_job_type" value="기타" <? if( $admin['ad_job_type'] == '기타' ) echo "checked";?> > 기타(오토, 위치, 기타)</label>
                <?php 
                    } else { 
                ?>
                    <?=$admin['ad_job_type'] ?? '고용형태 미입력'?>
                <?php } ?>
            </td>
        </tr>

		<tr>
			<th>입사일</th>
			<td colspan="3">
                <?php
                    if( $auth['ad_level'] == 100){ 
                ?>
				    <div class="calendar-input" style="display:inline-block;"><input type="text" name="ad_joining"  value="<?=$admin['ad_joining']?>" style="width:100px;"  autocomplete="off" ></div>
                <?php 
                    } else { 
                ?>
                    <?=$admin['ad_joining'] ?? '입사일 미입력'?>
                <?php } ?>
			</td>
		</tr>

		<tr>
			<th>닉네임</th>
			<td>
				<input type='text' name='ad_nick'  value="<?=$admin['ad_nick']?>" autocomplete="off" >
			</td>
			<th>생년월일</th>
			<td>
				<div class="calendar-input" style="display:inline-block;"><input type="text" name="ad_birth"  value="<?=$admin['ad_birth']?>" style="width:100px;"  autocomplete="off" ></div>
			</td>
		</tr>

		<tr>
			<th>프로필 이미지</th>
			<td colspan="3">

				<div class="ad-profile-img2"><img src="/data/uploads/<?=$admin['ad_image']?>" alt=""></div>

				<div class="m-t-5">
					<input name="upload_file" id="upload_file_profile" type="file" >
				</div>

				<div class="m-t-5">
					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="staffInfo.fileUpload('profile');" >프로필 이미지 업로드</button>
				</div>

				※ 최대 200px x 200px 이상시 자동 리사이징됨
			</td>
		</tr>

		<tr>
			<th>주소</th>
			<td colspan="3">
				<input type='text' name='ad_address'  value="<?=$admin['ad_data']['address']?>" autocomplete="off" class="width-full" >
			</td>
		</tr>
		<tr>
			<th>연락처</th>
			<td colspan="3">
				<input type='text' name='ad_tel'  value="<?=$admin['ad_data']['tel']?>" autocomplete="off" >
			</td>
		</tr>

		<tr>
			<th>비상연락처</th>
			<td colspan="3">
				<table class="table-style border01">
					<tr>
						<th>이름</th>
						<td><input type='text' name='ad_contact_name'  value="<?=$admin['ad_data']['contact']['name']?>" autocomplete="off" ></td>
						<th>관계</th>
						<td><input type='text' name='ad_contact_relationship'  value="<?=$admin['ad_data']['contact']['relationship']?>" autocomplete="off" ></td>
					</tr>
					<tr>
						<th>연락처</th>
						<td colspan="3"><input type='text' name='ad_contact_tel'  value="<?=$admin['ad_data']['contact']['tel']?>" autocomplete="off" ></td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<th>구글 아이디</th>
			<td colspan="3">
				<input type='text' name='ad_google'  value="<?=$admin['ad_google']?>"  class="width-full" >
			</td>
		</tr>
		<tr>
			<th>텔레그램 토큰</th>
			<td colspan="3">
                <?php
                    if( $auth['ad_level'] == 100){ 
                ?>
				<input type='text' name='ad_telegram_token'  value="<?=$admin['ad_telegram_token']?>"  class="width-full" >
                <?php 
                    } else { 
                ?>
                    <?=$admin['ad_telegram_token'] ?? '텔레그램 토큰 미입력'?>
                <?php } ?>
			</td>
		</tr>
		<tr>
			<th>라인 토큰</th>
			<td colspan="3">
                <?php
                    if( $auth['ad_level'] == 100){ 
                ?>
				<input type='text' name='ad_line_token'  value="<?=$admin['ad_line_token']?>"  class="width-full" >
                <?php 
                    } else { 
                ?>
                    <?=$admin['ad_line_token'] ?? '라인 토큰 미입력'?>
                <?php } ?>
			</td>
		</tr>
	</table>
	</form>

	<!-- 파일등록 -->
	<form name='file_upload_form'  id='file_upload_form' method='post' enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="a_mode" value="adProfileFile">
		<input type="hidden" name="smode" id="file_upload_mode" >
		<input type="hidden" name="idx" value="<?=$_idx?>">
	</form>

	<div class="m-t-10 text-center">
    <?php
        if( !empty($admin['idx']) ){ 
    ?>
    	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="staffInfo.save(this);" >수정</button>
    <?php } else { ?>
    	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="staffInfo.createStaff();" >생성</button>
    <?php } ?>
	</div>

<script type="text/javascript">
    var staffInfo = function() {

        /**
         * 직원 생성
         */
        function createStaff() {
           
            var formData = $("#form1").serializeArray();

            ajaxRequest("/admin/staff/create", formData, {})
                .then(function(res){
                    if( res.success ){
                        alert(res.message);
                        window.location.reload();
                    } else {
                        alert(res.message);
                    }
                })
                .catch(function(err){
                    alert(err.message);
                });
            
        }

        /**
         * 직원 수정
         */
        function save(obj) {
            var formData = $("#form1").serializeArray();
            ajaxRequest("/admin/staff/update", formData, {})
                .then(function(res){
                    if( res.success ){
                        alert(res.message);
                        window.location.reload();
                    } else {
                        alert(res.message);
                    }
                })
                .catch(function(err){
                    alert(err.message);
                });
        }


        return {
            createStaff,
            save
        }
    }();
</script>