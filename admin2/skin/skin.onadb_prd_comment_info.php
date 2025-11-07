<?
// 변수 초기화
$_idx = $_idx ?? "";
$data = [];
$_pc_reg_info = [];
$_user_name = "";

if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from prd_comment WHERE pc_idx = '".$_idx."' "));
	if (!is_array($data)) {
		$data = [];
	}

/*
	$_ad_data = json_decode($data['ad_data'], true);
	$_ad_data_arr = array(
		"address" => $_ad_address,
		"tel" => $_ad_tel,
		"contact" => array(
			"name" => $_ad_contact_name,
			"relationship" => $_ad_contact_relationship,
			"tel" => $_ad_contact_tel
		)
	);
*/
	$_pc_reg_info = json_decode($data['pc_reg_info'] ?? '{}', true);
	if (!is_array($_pc_reg_info)) {
		$_pc_reg_info = [];
	}

	if( $data['pc_user_idx'] ?? '' ){
		$_user_name = '<i style="font-size:16px; color:#999;" class="fas fa-user-circle"></i> <b>'.($_pc_reg_info['name'] ?? '').'</b>';
	}else{
		$_user_name = $_pc_reg_info['name'] ?? '';
	}

}else{


}



?>
<style type="text/css">
.ad-profile-img2{ width:100px; height:100px; box-sizing:border-box;  overflow:hidden; border-radius:50%; }
.ad-profile-img2 img{ width:100%; }
</style>

	<form id="onadb_commModify_form">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="onadb_commModify" >
	<input type="hidden" name="idx" value="<?=$_idx ?? ''?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" value="ad_reg" >
<? } ?>

	<table class="table-style border01 width-full">

		<tr>
			<th style="width:150px;">작성모드</th>
			<td>
				<select name="pc_score_mode" id="pc_score_mode">
					<option value="before" <? if( ($data['pc_score_mode'] ?? '') == "before" ) echo "selected"; ?>>일반 한줄평</option>
					<option value="after" <? if( ($data['pc_score_mode'] ?? '') == "after" ) echo "selected"; ?>>사용자 한줄평</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>사용자 평가</th>
			<td>
				<? 
					$_option = array("자극/기믹","유지관리","냄새/유분/소재","조임/탄력","마감/내구성","조형/패키지","진공");
					if (!is_array($_option)) {
						$_option = [];
					}
					for ($i=0; $i<count($_option); $i++){ 
						$_ii = $i + 1;
				?>
				<select name="score_<?=$_ii?>" id="score_<?=$_ii?>" disabled>
					<option value="0"><?=$_option[$i] ?? ''?></option>
					<? 
						for ($z=0; $z<10; $z++){ 
							$_zz = $z + 1;
					?>
					<option value="<?=$_zz?>"><?=$_zz?>점</option>
					<? } ?>
				</select>
				<? } ?>
			</td>
		</tr>
		<tr>
			<th>작성자</th>
			<td>
				<? 
					if( $data['pc_user_idx'] ?? '' ){ 
				?>
					<?=$_user_name ?? ''?>
				<? }else{ ?>
					<input type='text' name='ad_nick'  value="<?=$_pc_reg_info['name'] ?? ''?>" autocomplete="off" >
				<? } ?>
			</td>
		</tr>
		<tr>
			<th>개인평점</th>
			<td>
				<select name="pc_grade" id="pc_grade" class="">
					<option>개인평점</option>
					<? for ($z=1; $z<=10; $z++){  ?>
					<option value="<?=$z?>" <? if( ($data['pc_grade'] ?? 0) == $z ) echo "selected"; ?>  ><?=$z?>점</option>
					<? } ?>
				</select>
				<div class="admin-guide">
					<ul>- 개인평점 데이터만 수정되고 전체 평점에는 영향을 미치지 않습니다.</ul>
					<ul>- 상품의 평점은 [개인평점 갱신]를 눌어야 최종 변경됩니다.</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th>내용</th>
			<td>
				<textarea name="" rows="" cols=""><?=$data['pc_body'] ?? ''?></textarea>
			</td>
		</tr>
	</table>
	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdCommentInfo.save(this);" >전송</button>
	</div>

<script type="text/javascript"> 
<!-- 
var prdCommentInfo = function() {

	var B;

	var C = function() {
	};

	return {
		
		init : function() {

		},

		save : function(obj) {

			//$(obj).attr('disabled', true);

			var formData = $("#onadb_commModify_form").serializeArray();

			$.ajax({
				url: "/ad/processing/prd",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						prdInfo.mode('', 'onadb_comment');
						prdCommentList.modifyClose();
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
					//$(obj).attr('disabled', false);
				}
			});

		},


	};

}();

$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar2);
	}

});
//--> 
</script> 