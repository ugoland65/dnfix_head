<?
// 변수 초기화
$_idx = $_GET['idx'] ?? $_POST['idx'] ?? "";

if( $_idx ){

	$data = wepix_fetch_array(wepix_query_error("select * from partners WHERE idx = '".$_idx."' "));

	// 배열 검증
	if (!is_array($data)) {
		$data = [];
	}

	$_data = json_decode($data['info'] ?? '{}', true);
	
	// JSON 디코딩 결과 검증
	if (!is_array($_data)) {
		$_data = [];
	}

	$_data_nation = $_data['nation'] ?? '';

	$_data_hp_url = $_data['hp']['url'] ?? '';
	$_data_hp_id = $_data['hp']['id'] ?? '';
	$_data_hp_pw = $_data['hp']['pw'] ?? '';

	$_data_info_tel = $_data['info']['tel'] ?? '';
	$_data_info_email = $_data['info']['email'] ?? '';

	$_data_keeper_name = $_data['keeper']['name'] ?? '';
	$_data_keeper_rank = $_data['keeper']['rank'] ?? '';
	$_data_keeper_tel = $_data['keeper']['tel'] ?? '';

	// 날짜 처리 - 빈 값 체크
	if (!empty($data['date_s'])) {
		$_date_s = date("Y-m-d", strtotime($data['date_s']));
	} else {
		$_date_s = date("Y-m-d");
	}
	
	if (!empty($data['date_e'])) {
		$_date_e = date("Y-m-d", strtotime($data['date_e']));
	} else {
		$_date_e = date("Y-m-d");
	}

}else{
	$_target_name = $_ad_name ?? '';

	$_date_s = date("Y-m-d");
	$_date_e = date("Y-m-d");

	// 신규 등록 시 변수 초기화
	$data = [];
	$_data_nation = '';
	$_data_hp_url = '';
	$_data_hp_id = '';
	$_data_hp_pw = '';
	$_data_info_tel = '';
	$_data_info_email = '';
	$_data_keeper_name = '';
	$_data_keeper_rank = '';
	$_data_keeper_tel = '';

}

	$_state_text['1'] = "신청중";
	$_state_text['2'] = "확인중";
	$_state_text['3'] = "승인";
	$_state_text['4'] = "반려";

?>

	<form id="form1">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="partners_modify" >
	<input type="hidden" name="idx" value="<?=$_idx ?? ''?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" value="partners_reg" >
<? } ?>

	<table class="table-style border01 width-full">

		<tr>
			<th>거래처명</th>
			<td colspan="3">
				<input type='text' name='name'  value="<?=$data['name'] ?? ''?>" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th>종류</th>
			<td colspan="3">
				<select name="category">
					<? 
					$_partners_cate_count = isset($_partners_cate) && is_array($_partners_cate) ? count($_partners_cate) : 0;
					for ($i=0; $i<$_partners_cate_count; $i++){ 
						if (!isset($_partners_cate[$i]) || !is_array($_partners_cate[$i])) continue;
					?>
					<option value="<?=$_partners_cate[$i]["name"] ?? ''?>" <? if( ($data['category'] ?? '') == ($_partners_cate[$i]["name"] ?? '') ) echo "selected"; ?>><?=$_partners_cate[$i]["name"] ?? ''?></option>
					<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>국가</th>
			<td colspan="3">
				<label><input type="radio" name="nation" value="한국" <? if( empty($_data_nation) || $_data_nation == "한국" ) echo "checked"; ?> > 한국</label>
				<label><input type="radio" name="nation" value="일본" <? if( $_data_nation == "일본" ) echo "checked"; ?> > 일본</label>
				<label><input type="radio" name="nation" value="중국" <? if( $_data_nation == "중국" ) echo "checked"; ?> > 중국</label>
				<label><input type="radio" name="nation" value="달러" <? if( $_data_nation == "달러" ) echo "checked"; ?> > 그외 달러 국가</label>
			</td>
		</tr>

		<tr>
			<th>홈페이지</th>
			<td colspan="3">
				<table class="table-style border01">
					<tr>
						<th>주소</th>
						<td colspan="3"><input type='text' name='hp_url'  value="<?=$_data_hp_url ?? ''?>" autocomplete="off" ></td>
					</tr>
					<tr>
						<th>아이디</th>
						<td><input type='text' name='hp_id'  value="<?=$_data_hp_id ?? ''?>" autocomplete="off" ></td>
						<th>패스워드</th>
						<td><input type='text' name='hp_pw'  value="<?=$_data_hp_pw ?? ''?>" autocomplete="off" ></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>연락처</th>
			<td colspan="3">
				<table class="table-style border01">
					<tr>
						<th>전화번호</th>
						<td><input type='text' name='tel'  value="<?=$_data_info_tel ?? ''?>" autocomplete="off" ></td>
						<th>이메일</th>
						<td><input type='text' name='email'  value="<?=$_data_info_email ?? ''?>" autocomplete="off" ></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>담당자</th>
			<td colspan="3">
				<table class="table-style border01">
					<tr>
						<th>이름</th>
						<td><input type='text' name='keeper_name'  value="<?=$_data_keeper_name ?? ''?>" autocomplete="off" ></td>
						<th>직급</th>
						<td><input type='text' name='keeper_rank'  value="<?=$_data_keeper_rank ?? ''?>" autocomplete="off" ></td>
					</tr>
					<tr>
						<th>연락처</th>
						<td><input type='text' name='keeper_tel'  value="<?=$_data_keeper_tel ?? ''?>" autocomplete="off" ></td>
						<th></th>
						<td></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>메모</th>
			<td colspan="3">
				<textarea name="memo"><?=$data['memo'] ?? ''?></textarea>
			</td>
		</tr>

	</table>
	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="partnersReg.save(this);" >전송</button>
	</div>

<script type="text/javascript"> 
<!-- 
var partnersReg = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		save : function(obj) {

			$(obj).attr('disabled', true);

			var formData = $("#form1").serializeArray();

			$.ajax({
				url: "/ad/processing",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						//toast2("success", "충/환전 설정", "설정이 저장되었습니다.");
						alert("등록되었습니다.");
						location.reload();
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		}
	};

}();

$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

});
//--> 
</script> 