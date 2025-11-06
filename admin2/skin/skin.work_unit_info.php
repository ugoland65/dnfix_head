<?
if( $_idx ){

	$data = wepix_fetch_array(wepix_query_error("select * from work_unit WHERE idx = '".$_idx."' "));

	$_data = json_decode($data['info'], true);

}else{

}

	$_state_text['1'] = "신청중";
	$_state_text['2'] = "확인중";
	$_state_text['3'] = "승인";
	$_state_text['4'] = "반려";

?>

	<form id="form1">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="work_unit_modify" >
	<input type="hidden" name="idx" value="<?=$_idx?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" value="work_unit_reg" >
<? } ?>

	<table class="table-style border01 width-full">


		<tr>
			<th>부서</th>
			<td colspan="3">
				<label><input type="radio" name="dept" value="운영팀" <? if( !$data['dept'] || $data['dept'] == "운영팀" ) echo "checked"; ?> > 운영팀</label>
				<label><input type="radio" name="dept" value="배송팀" <? if( $data['dept'] == "배송팀" ) echo "checked"; ?> > 배송팀</label>
		</tr>
		<tr>
			<th>업무명</th>
			<td colspan="3">
				<input type='text' name='subject'  value="<?=$data['subject']?>" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th>분류</th>
			<td colspan="3">
				<select name="category">
					<? for ($i=0; $i<count($_work_unit_cate); $i++){ ?>
					<option value="<?=$_work_unit_cate[$i]["name"]?>" <? if( $data['category'] ==$_work_unit_cate[$i]["name"] ) echo "selected"; ?>><?=$_work_unit_cate[$i]["name"]?></option>
					<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>업무내용</th>
			<td colspan="3">
				<textarea name="body"><?=$data['body']?></textarea>
			</td>
		</tr>

		<tr>
			<th>업무상세</th>
			<td colspan="3">
				<table class="table-style border01">
					<tr>
						<th>소요시간</th>
						<td colspan="3"><input type='text' name='work_detail_time'  value="<?=$_data_hp_url?>" autocomplete="off" ></td>
					</tr>
				</table>
			</td>
		</tr>

	</table>
	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="workUnitReg.save(this);" >전송</button>
	</div>

<script type="text/javascript"> 
<!-- 
var workUnitReg = function() {

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
				url: "/ad/processing/work",
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