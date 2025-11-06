<?
if( $_idx ){

	$data = wepix_fetch_array(wepix_query_error("select * from bank_statement WHERE idx = '".$_idx."' "));

	if( $data['bs_mode'] == "plus" ){
		$_ledge_cate_mode_text = "수입";
	}elseif( $data['bs_mode'] == "minus" ){
		$_ledge_cate_mode_text = "지출";
	}

	$_bank = json_decode($data['bank'], true);

}else{

}

	$_state_text['1'] = "신청중";
	$_state_text['2'] = "확인중";
	$_state_text['3'] = "승인";
	$_state_text['4'] = "반려";

?>

<style type="text/css">

</style>

	<form id="form1">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="bankStatement_modify" >
	<input type="hidden" name="idx" value="<?=$_idx?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" value="partners_reg" >
<? } ?>

	<table class="table-style border01 width-full">

		<tr>
			<th style="width:100px">확인</th>
			<td >
				<label><input type="radio" name="state" value="N" <? if( !$data['state'] || $data['state'] == "N" ) echo "checked"; ?> > 미확인</label>
				<label><input type="radio" name="state" value="Y" <? if( $data['state'] == "Y" ) echo "checked"; ?> > 확인</label>
			</td>
		</tr>

		<tr>
			<th>날짜</th>
			<td >
				<?=date("Y-m-d H:i:s", strtotime($data['date']))?>
			</td>
		</tr>
		<tr>
			<th>이름</th>
			<td >
				<?=$data['name']?>
			</td>
		</tr>
		<tr>
			<th>입금</th>
			<td >
				<?=number_format($data['in_money'])?>
			</td>
		</tr>
		<tr>
			<th>출금</th>
			<td >
				<?=number_format($data['out_money'])?>
			</td>
		</tr>
		<tr>
			<th>처리시 잔금</th>
			<td >
				<?=number_format($data['balance_money'])?>
			</td>
		</tr>
		<tr>
			<th>처리은행</th>
			<td >
				<?=$_bank['bank']?> | <?=$_bank['num']?>
			</td>
		</tr>
		<tr>
			<th>항목</th>
			<td >
				<select name="ledge_cate_kind" id="ledge_cate_kind" onchange="bankStatementInfo.ledgeCate(this.value)" >
					<option value="">종류</option>
					<option value="수입" <? if( $_ledge_cate_mode_text == "수입" ) echo "selected"; ?> >수입</option>
					<option value="지출" <? if( $_ledge_cate_mode_text == "지출" ) echo "selected"; ?>>지출</option>
				</select>
				<select name="ledge_cate" id="ledge_cate" >
					<option value="">항목선택</option>
					<option value="">------------</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>메모</th>
			<td >
				<input type='text' name='memo'  value="<?=$data['memo']?>" autocomplete="off" style="width:100%">
			</td>
		</tr>

	</table>
	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="bankStatementInfo.save(this);" >전송</button>
	</div>

<script type="text/javascript"> 
<!-- 
var bankStatementInfo = function() {

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
						ledge.list();
						showAlert("Good", "수정완료 되었습니다.", "alert2", "good" );
						return false;
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

		ledgeCate : function( kind, ledge_cate_idx ) {

			$.ajax({
				url: "/ad/processing/accounting",
				data: { "a_mode":"ledgeCateLoad", "kind":kind },
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						
						var _option_text = "";
						var _option_selected = "";

						for (var i = 0; i < res.ledge_cate.length; i++) {
							_option_selected = "";
							if( ledge_cate_idx == res.ledge_cate[i].idx ){
								_option_selected = "selected";
							}
							_option_text += "<option value='"+ res.ledge_cate[i].idx +"' "+ _option_selected +" >" + res.ledge_cate[i].name + "</option>";
						}
						$("#ledge_cate").append(_option_text);

					}else{
						showAlert("Error", res.msg, "dialog" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {

				}
			});

		}

	};

}();

$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

	<? if( $data['ledge_cate_idx'] > 0 ){ ?>
		bankStatementInfo.ledgeCate("<?=$_ledge_cate_mode_text?>", "<?=$data['ledge_cate_idx']?>");
	<? }else{ ?>
		bankStatementInfo.ledgeCate("<?=$_ledge_cate_mode_text?>");
	<? } ?>

});
//--> 
</script> 