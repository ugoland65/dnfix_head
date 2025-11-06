<?
if( $_idx ){

	$data = wepix_fetch_array(wepix_query_error("select * from schedule_sttaf WHERE idx = '".$_idx."' "));

	$_data = json_decode($data['data'], true);
	$_reg_name = $_data['reg']['name'];
	$_reg_date = date("Y.m.d H:i", strtotime($_data['reg']['date']));
	$_target_name = $_data['target']['name'];

	$_date_s = date("Y-m-d", strtotime($data['date_s']));
	$_date_e = date("Y-m-d", strtotime($data['date_e']));

}else{
	$_target_name = $_ad_name;

	$_date_s = date("Y-m-d");
	$_date_e = date("Y-m-d");

}

	$_state_text['1'] = "신청중";
	$_state_text['2'] = "확인중";
	$_state_text['3'] = "승인";
	$_state_text['4'] = "반려";

/*
	echo "<pre>";
	print_r($_data);
	echo "</pre>";
*/
?>

	<form id="form_staff">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="staff_holiday_modify" >
	<input type="hidden" name="idx" value="<?=$_idx?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" value="staff_holiday_reg" >
<? } ?>

	<table class="table-style border01 width-full">
		<tr>
			<th style="width:100px;">신청인</th>
			<td colspan="3">
				<? 
				if( $_ad_level > 9 ){
					$_result = sql_query_error("select * from admin ".$_where." ORDER BY idx DESC");
					while($_list = wepix_fetch_array($_result)){
						$_staff[] = array(
							"idx" => $_list['idx'],
							"name" => $_list['ad_name']
						);
					}
				?>
					<select name="tidx">
						<? for ($i=0; $i<count($_staff); $i++){ ?>
						<option value="<?=$_staff[$i]['idx']?>" <? if( $data['tidx'] == $_staff[$i]['idx'] ) echo "selected"; ?> ><?=$_staff[$i]['name']?></option>
						<? } ?>
					</select>
				<? }else{ ?>
					<input type="hidden" name="tidx" value="<?=$_ad_idx?>" >
					<?=$_target_name?>
				<? } ?>
			</td>
		</tr>
		<tr>
			<th>희망 시작일</th>
			<td>
				<div class="calendar-input">
					<input type='text' name='date_s'  value="<?=$_date_s?>" autocomplete="off" > 
				</div>
			</td>
			<th style="width:100px;">희망 종료일</th>
			<td>
				<div class="calendar-input">
					<input type='text' name='date_e'  value="<?=$_date_e?>" autocomplete="off" >
				</div>
				<div>
					※1일경우 미등록
				<div>
			</td>
		</tr>
		<tr>
			<th>종류</th>
			<td colspan="3">
				<label><input type="radio" name="mode" value="휴가" <? if( !$data['mode'] || $data['mode'] == "휴가" ) echo "checked"; ?> > 휴가</label>
				<label><input type="radio" name="mode" value="월차" <? if( !$data['mode'] || $data['mode'] == "월차" ) echo "checked"; ?> > 월차</label>
				<label><input type="radio" name="mode" value="오전반차" <? if( $data['mode'] == "오전반차" ) echo "checked"; ?> > 오전반차</label>
				<label><input type="radio" name="mode" value="오후반차" <? if( $data['mode'] == "오후반차" ) echo "checked"; ?> > 오후반차</label>
				<label><input type="radio" name="mode" value="유급휴가" <? if( $data['mode'] == "유급휴가" ) echo "checked"; ?> > 유급휴가</label>
				<label><input type="radio" name="mode" value="포상휴가" <? if( $data['mode'] == "포상휴가" ) echo "checked"; ?> > 포상휴가</label>
				<label><input type="radio" name="mode" value="조퇴" <? if( $data['mode'] == "조퇴" ) echo "checked"; ?> > 조퇴</label>
				<div>
					※유급휴가 : 예비군
				<div>
			</td>
		</tr>
		<tr>
			<th>신청사유</th>
			<td colspan="3">
				<input type='text' name='memo'  value="<?=$data['memo']?>" autocomplete="off" >
			</td>
		</tr>

		<? if( $_ad_level == 100 ){ ?>
		<tr>
			<th>상태변경</th>
			<td colspan="3">
				<select name="state">
					<option value="1" <? if( $data['state'] == "1" ) echo "selected"; ?> >신청중</option>
					<option value="2" <? if( $data['state'] == "2" ) echo "selected"; ?> >확인중</option>
					<option value="3" <? if( $data['state'] == "3" ) echo "selected"; ?> >승인</option>
					<option value="4" <? if( $data['state'] == "4" ) echo "selected"; ?> >반려</option>
				</select>
			</td>
		</tr>
		<? }else{ ?>
			<input type="hidden" name="state" value="<?= $data['state']?>" >
		<? } ?>

		<? if( $_idx ){ ?>
		<tr>
			<th>Log</th>
			<td colspan="3">
				등록일 : <?=$_data['reg']['date']?> | <?=$_data['reg']['name']?>
			</td>
		</tr>
		<? } ?>

	</table>
	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="staffHolidayReg2.save(this);" >신청</button>
	</div>

<script type="text/javascript"> 
var staffHolidayReg2 = function() {

	return {

		init : function() {

		},
		save : function( obj ) {

			//$(obj).attr('disabled', true);

			var formData = $("#form_staff").serializeArray();

			$.ajax({
				url: "/ad/processing/staff",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){

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
					//$(obj).attr('disabled', false);
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
</script> 