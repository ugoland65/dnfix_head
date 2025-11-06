	<form id="form_prdGroupingReg">
	<input type="hidden" name="a_mode" value="prdGrouping_reg" >

	<table class="table-style border01 width-full">
		<tr>
			<th style="width:100px;">그룹핑 모드</th>
			<td>
				<select name="pg_mode" onchange="prdGroupingReg.pgModeOnchange(this.value)">
					<option value="sale" >데이할인</option>
					<option value="period" >기간할인</option>
					<option value="event" >기획전</option>
					<option value="qty" >수량 체크</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>그룹핑 제목</th>
			<td>
				<input type='text' name='pg_subject'  value="" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th>진행일</th>
			<td>
				<div class="calendar-input" style="display:none; width:105px;" id="pg_sday_wrap"><input type="text" name="pg_sday"  id="pg_sday" value="<?=$data['pg_sday']?>" style="width:90px;" placeholder="시작일" autocomplete="off" > ~ </div>
				<div class="calendar-input" style="display:inline-block;"><input type="text" name="pg_day"  id="pg_day" value="<?=$data['pg_day']?>" style="width:90px;"  autocomplete="off" ></div>
			</td>
		</tr>
	</table>
	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdGroupingReg.save(this);" >등록</button>
	</div>

<script type="text/javascript"> 
<!-- 
var prdGroupingReg = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		
		pgModeOnchange : function( mode ) {

			if( mode == "period" ){
				$("#pg_sday_wrap").css({'display':'inline-block'}).show();
			}else{
				$("#pg_sday_wrap").hide();
			}

		},

		save : function( obj ) {

			var formData = $("#form_prdGroupingReg").serializeArray();

			$.ajax({
				url: "/ad/processing/prd_grouping",
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
//--> 
</script> 