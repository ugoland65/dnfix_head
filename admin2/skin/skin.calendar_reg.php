<?
if( $_idx ){

	$data = wepix_fetch_array(wepix_query_error("select * from calendar WHERE idx = '".$_idx."' "));

	$_json_data = json_decode($data['data'], true);

	$_date = date("Y-m-d", strtotime($data['date_s']));
	$_date_s = date("Y-m-d H:i:s", strtotime($data['date_s']));
	$_date_e = date("Y-m-d H:i:s", strtotime($data['date_e']));

}else{

	$_date = date("Y-m-d", strtotime($_y."-".$_m."-".$_d." 00:00:00"));
	$_date_s = date("Y-m-d H:i:s", strtotime($_y."-".$_m."-".$_d." 00:00:00"));
	$_date_e = date("Y-m-d H:i:s", strtotime($_y."-".$_m."-".$_d." 00:00:00"));

}

	$_calendar_open = array("전체공개","개인");

	$_calendar_kind = array("회의","방문미팅","외부미팅","일정","체크","중요","행사","개인");
?>

	<form id="form1">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="calendar_modify" >
	<input type="hidden" name="idx" value="<?=$_idx?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" value="calendar_reg" >
<? } ?>

	<table class="table-style border01 width-full">
		<tr>
			<th style="width:120px">날짜</th>
			<td>
				<?=$_date ?>
			</td>
		</tr>
		<tr>
			<th style="width:120px">일시</th>
			<td>
				<input type="datetime-local" name="date_s" value="<?=$_date_s?>"> ~ <input type="datetime-local" name="date_e" value="<?=$_date_e?>">
			</td>
		</tr>
		<tr>
			<th>제목</th>
			<td>
				<input type='text' name='subject'  value="<?=$data['subject']?>" autocomplete="off" class="width-full">
			</td>
		</tr>
		<tr>
			<th>모드</th>
			<td>

				<div class="m-t-5">
					<label><input type="radio" name="open" value="전체공개" <? if( !$data['open'] || $data['open'] == "전체공개" ) echo "checked"; ?>> 전체공개</label>
					<label><input type="radio" name="open" value="개인" <? if( $data['open'] == "개인" ) echo "checked"; ?>> 개인</label>
				</div>

				<div class="m-t-7">
					종류 : 
					<select name="kind">
						<? for ($i=0; $i<count($_calendar_kind); $i++){ ?>
						<option value="<?=$_calendar_kind[$i]?>" <? if( $data['kind'] == $_calendar_kind[$i] ) echo "selected"; ?>><?=$_calendar_kind[$i]?></option>
						<? } ?>
					</select>

					상태 : 
					<select name="state">
						<option value="I" <? if( $data['state'] == "I" ) echo "selected"; ?>>진행</option>
						<option value="E" <? if( $data['state'] == "E" ) echo "selected"; ?>>완료</option>
						<option value="C" <? if( $data['state'] == "C" ) echo "selected"; ?>>취소</option>
					</select>
				</div>
			</td>
		</tr>

		<tr>
			<th>참여자</th>
			<td>
<?
	$_query = "select * from admin ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);
	while($_list = wepix_fetch_array($_result)){
		
		$_checked = "";
		$_target_mb_text = "@".$_list['idx'];
		if (strstr($data['target_mb'], $_target_mb_text)){
			$_checked = "checked";
		}
?>
				<label><input type="checkbox" name="target_mb_id[]" value="<?=$_list['idx']?>" <?=$_checked?>> <?=$_list['ad_nick']?></label>
<? } ?>
			</td>
		</tr>

		<tr>
			<th>메모</th>
			<td>
				<textarea name="memo"><?=$data['memo']?></textarea>
			</td>
		</tr>

	</table>
	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="calendarReg.save(this);" >전송</button>
	</div>

<script type="text/javascript"> 
<!-- 
var calendarReg = function() {

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
				url: "/ad/processing/calendar",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "캘린더 등록", "캘린더 등록이 저장되었습니다.");
						calendarWindow.close();
						calendar.view();
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