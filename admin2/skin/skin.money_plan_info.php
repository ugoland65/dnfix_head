<?
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from money_plan WHERE idx = '".$_idx."' "));



}else{


}


?>

	<form id="form1">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="moneyPlan_modify" >
	<input type="hidden" name="idx" value="<?=$_idx?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" value="moneyPlan_reg" >
<? } ?>

	<table class="table-style border01 width-full">

		<tr>
			<th style="width:120px">이름</th>
			<td>
				<input type='text' name='name'  value="<?=$data['name']?>" autocomplete="off" >
			</td>
		</tr>
		<tr>
			<th>종류</th>
			<td>
				<select name="category">
					<? for ($i=0; $i<count($_moneyplan_cate); $i++){ ?>
					<option value="<?=$_moneyplan_cate[$i]["name"]?>" <? if( $data['category'] ==$_moneyplan_cate[$i]["name"] ) echo "selected"; ?>><?=$_moneyplan_cate[$i]["name"]?></option>
					<? } ?>
				</select>
			</td>
		</tr>

		<? if( $_idx ){ ?>
		<tr>
			<th>balance</th>
			<td>
				<b><?=number_format($data['balance'])?></b>
			</td>
		</tr>
		<? } ?>
		<tr>
			<th>메모</th>
			<td>
				<textarea name="memo" style="height:50px;" autocomplete="off"><?=$data['memo']?></textarea>
			</td>
		</tr>

	</table>
	</form>

	<div class="m-t-10 text-center">
		<? if( $_idx ){ ?>
			<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="moneyPlanReg.save(this);" >수정</button>
		<? } else{ ?>
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="moneyPlanReg.save(this);" >전송</button>
		<? } ?>
	</div>


<? if( $_idx ){ ?>
	
	<form id="form_money_plan_history">
	<input type="hidden" name="a_mode" value="moneyPlan_history" >
	<input type="hidden" name="idx" value="<?=$_idx?>" >
	
	<table class="table-style border01 width-full m-t-15">
		<tr>
			<th style="width:120px">종류</th>
			<td>
				<label><input type="radio" name="mode" value="plus" checked > 증가</label>
				<label><input type="radio" name="mode" value="minus" > 차감</label>
				<span class="display-inline-block m-l-10">
					금액 : <input type="text" name="price" class="price price_point" value="" onkeyUP="GC.commaInput( this.value, this );" style="width:100px;" autocomplete="off"> 원
				</span>
				<span class="display-inline-block m-l-10">
					날짜 : <div class="calendar-input" style="display:inline-block;"><input type="text" name="date" style="width:80px;" autocomplete="off"></div>
				</span>
			</td>
		</tr>
		<tr>
			<th>메모</th>
			<td>
				<input type="text" name="memo" value="" placeholder="메모" >
			</td>
		</tr>
	</table>
	</form>

	<div class="m-t-5 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="moneyPlanReg.historyReg(this, '<?=$_idx?>');" >금액변동 입력</button>
	</div>

	<table class="table-style border01 width-full m-t-15">
	<tr>
		<th style="width:50px">idx</th>
		<th style="width:90px">일시</th>
		<th style="width:90px">차감</th>
		<th style="width:90px">증가</th>
		<th style="width:130px">잔액</th>
		<th>메모</th>
	</tr>
<?
	$_query = "select * from money_plan_history WHERE tidx= '".$_idx."'  ORDER BY idx DESC";
	$_result = sql_query_error($_query);
	while($list = sql_fetch_array($_result)){
		
		$_plus_price = "";
		$_minus_price = "";
		if( $list['mode'] == "plus"){
			$_plus_price = number_format($list['price']);
		}elseif( $list['mode'] == "minus"){
			$_minus_price = number_format($list['price']);
		}

?>
	<tr>
		<td><?=$list['idx']?></td>
		<td><?=date ("Y-m-d", strtotime($list['date']))?></td>
		<td class="text-right"><?=$_minus_price?></td>
		<td class="text-right"><?=$_plus_price?></td>
		<td class="text-right"><?= number_format($list['total'])?></td>
		<td><?=$list['memo']?></td>
	</tr>
<? } //while END ?>
	</table>

<? } ?>

<script type="text/javascript"> 
<!-- 
var moneyPlanReg = function() {

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
				url: "/ad/processing/accounting",
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

		},

		historyReg : function( obj, idx ) {

			$(obj).attr('disabled', true);

			var formData = $("#form_money_plan_history").serializeArray();

			$.ajax({
				url: "/ad/processing/accounting",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "금액변동", "저장되었습니다.");
						moneyPlan.viewReset(idx);
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