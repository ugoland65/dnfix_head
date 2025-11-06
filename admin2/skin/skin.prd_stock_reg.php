<?
if( $_ps_idx ){
	$stock_data = sql_fetch_array(sql_query_error("SELECT * FROM prd_stock WHERE ps_idx = '".$_ps_idx."' "));
}

if( $_idx ){
	$data = sql_fetch_array(sql_query_error("SELECT * FROM prd_stock_unit WHERE psu_idx = '".$_idx."' "));
	$_stock_day = $data['psu_day'];
}else{
	$_stock_day = date("Y-m-d");
}

?>
<style type="text/css">
.ad-profile-img2{ width:100px; height:100px; box-sizing:border-box;  overflow:hidden; border-radius:50%; }
.ad-profile-img2 img{ width:100%; }
</style>

	<form id="form1">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" id="a_mode" value="stock_info_modify" >
	<input type="hidden" name="idx" value="<?=$_idx?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" id="a_mode" value="stock_info_reg" >
<? } ?>
	<input type="hidden" name="ps_idx" value="<?=$_ps_idx?>" >

	<table class="table-style border01 width-full">

		<tr>
			<th class="text-center" style="width:150px;">재고수량</th>
			<td>
				현재 재고 : <b><?=$stock_data['ps_stock']?></b> &nbsp; | &nbsp;
				보류 재고 : <b><?=$stock_data['ps_stock_hold']?></b>
			</td>
		</tr>
		<tr>
			<th class="text-center">날짜</th>
			<td>
				<? if( $_idx ){ ?>
					<b><?=$data['psu_day']?></b>
				<? }else{ ?>
					<div class="calendar-input">
						<input type='text' name='stock_day' id='stock_day'  value="<?=$_stock_day?>" placeholder="끝일" >
					</div>
				<? } ?>
			</td>
		</tr>
		<tr>
			<th class="text-center">종류</th>
			<td>

				<? if( $_idx ){ ?>
					<b><? if( $data['psu_mode'] == "plus" ){ echo "입고"; }else{ echo "출고"; } ?></b>
				<? }else{ ?>
					<select name='stock_mode' class='selectpicker' >
						<option value='plus' <? if( $data['psu_mode'] == "plus" ) echo "selected"; ?>>입고</option>
						<option value='minus' <? if( $data['psu_mode'] == "minus" ) echo "selected"; ?>>출고</option>
						<option value='to_hold' <? if( $data['psu_mode'] == "to_hold" ) echo "selected"; ?>>보류 전환</option>
						<option value='to_stock' <? if( $data['psu_mode'] == "to_stock" ) echo "selected"; ?>>재고 전환</option>
						<option value='plus_hold' <? if( $data['psu_mode'] == "plus_hold" ) echo "selected"; ?>>보류 입고</option>
						<option value='minus_hold' <? if( $data['psu_mode'] == "minus_hold" ) echo "selected"; ?>>보류 출고</option>
					</select>
				<? } ?>

				<select name="stock_kind" >
					<option value="조정" <? if( $data['psu_kind'] == "조정" ) echo "selected"; ?>>( ± ) 조정</option>
					<option value="판매" <? if( $data['psu_kind'] == "판매" ) echo "selected"; ?>>( - ) 판매 (출고전용)</option>
					<option value="서비스" <? if( $data['psu_kind'] == "서비스" ) echo "selected"; ?>>( - ) 서비스 (출고전용)</option>
					<option value="반품" <? if( $data['psu_kind'] == "반품" ) echo "selected"; ?>>( + ) 반품,주문취소 (입고전용)</option>
					<option value="신규입고" <? if( $data['psu_kind'] == "신규입고" ) echo "selected"; ?>>( + ) 신규입고</option>
					<option value="샘플" <? if( $data['psu_kind'] == "샘플" ) echo "selected"; ?>>샘플</option>
				</select>
				<div class="admin-guide-text">
					<ul>- 보류 전환 : 현재 재고를 보류 재고로 보냄</ul>
					<ul>- 재고 전환 : 보류 재고를 현재 재고로 보냄</ul>
					<ul>- 보류 입고,출고 : 보류 재고를 추가하거나 빼줌</ul>
					<ul>- 신규입고는 주문후 입고된 상황에만 선택해주세요.</ul>
					<ul>- 재고파악,및 단순 수량조정일경우 [조정]을 선택해 주세요.</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="text-center">수량</th>
			<td>
				<? if( $_idx ){ ?>
					<b><?=$data['psu_qry']?></b>
				<? }else{ ?>
					<input type="text" name="stock_qty" style="width:80px;" placeholder="수량" value="1" />
				<? } ?>
			</td>
		</tr>
		<tr>
			<th class="text-center">메모</th>
			<td>
				<input type='text' name='stock_memo'  value="<?=$data['psu_memo']?>" autocomplete="off" >
			</td>
		</tr>

	</table>
	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="stockReg.save(this);" >전송</button>
	</div>

<script type="text/javascript"> 
<!-- 
var stockReg = function() {

	var _pn = "<?=$_pn?>";

	var C = function() {
	};

	return {
		
		init : function() {

		},

		save : function(obj) {

			$(obj).attr('disabled', true);

			var formData = $("#form1").serializeArray();

			$.ajax({
				url: "/ad/processing/prd",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						
						if( $("#a_mode").val() == "stock_info_reg" ){
							$("#now_stock").html(res.stock);
							$("#now_stock_hold").html(res.stock_hold);
							prdInfo.mode('1', 'stock');
							prdInfo.stockModifyClose();
						}else{
							prdInfo.mode(_pn, 'stock');
							prdInfo.stockModifyClose();
						}

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

		// 파일업로드
		fileUpload : function( mode ) {
		
			var fileCheck = document.getElementById("upload_file_"+ mode).value;
			if( !fileCheck ){
				showAlert("Error", "파일을 첨부해 주세요", "alert2" );
				return false;
			}

			$("#file_upload_mode").val(mode);

			var form = $('#file_upload_form')[0];
			var imgData = new FormData(form);

			imgData.append("fileObj", $("#upload_file_" + mode)[0].files[0]);

			$.ajax({
				url: "/ad/processing/staff",
				data: imgData,
				type: "POST",
				dataType: "json",
				contentType : false,
				processData : false,
				success: function(res){
					if ( res.success == true ){

						//toast2("success", "파일등록", "파일이 성공적으로 등록되었습니다.");
						alert("등록되었습니다.");
						location.reload();
						/*
						var html = '<div class="file-line m-t-5">'
							+ '<i class="far fa-save fa-flip-horizontal"></i>'
							+ ' <a href="/data/uploads/'+ res.filename +'" target="_blank">'+ res.filename +'</a>'
							+ ' :: '+ res.reg_id +' ( '+ res.reg_date +' )'
							+ ' <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="orderSheetReg.fileDel(this, \''+ mode +'\', \''+ res.idx +'\' ,\''+ res.filename +'\')" >'
							+ '<i class="fas fa-trash-alt"></i>'
							+ '</button>'
							+ '</div>';
						*/

						$("#file_line_wrap_" + mode).append(html);	
						$("#upload_file_"+ mode).val("");

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

	var clareCalendar2 = {
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		weekHeader: 'Wk',
		dateFormat: 'yy-mm-dd', //형식(20120303)
		autoSize: false, //오토리사이즈(body등 상위태그의 설정에 따른다)
		changeMonth: true, //월변경가능
		changeYear: true, //년변경가능
		showMonthAfterYear: true, //년 뒤에 월 표시
		buttonImageOnly: false, //이미지표시
		yearRange: '1983:2030' //1990년부터 2020년까지
	};

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar2);
	}

});
//--> 
</script> 