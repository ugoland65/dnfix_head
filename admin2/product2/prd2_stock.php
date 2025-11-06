<?
$pageGroup = "product2";
$pageName = "prd2_stock";

include "../lib/inc_common.php";

	$_pn = securityVal($pn);
	$_s_active = securityVal($s_active);
	$_s_text = securityVal($s_text);
	$_sort_kind = securityVal($sort_kind);

include "../layout/header.php";
?>

<script type="text/javascript"> 
<!-- 
function prd2_stock_plus(){
	showPopup(900,700,'ajax');

	$.ajax({
		type: "post",
		url : "ajax_prd2_stock_prd_plus.php",
		data : { quickmode : "on" },
		success: function(shtml) {
			$('#popup_layer_body').html(shtml);
		}
	});
}

function prd2_stock_plus_list(mode){
	
	$.ajax({
		type: "post",
		data: {
			"mode":mode
		},
		url : "ajax_prd2_stock_prd_plus.php",
		success: function(shtml) {
			$('#popup_layer_body').html(shtml);
		}
	});
}

function prd2_stock_list(mode){
	
	$.ajax({
		type: "post",
		data: {
			"mode":mode
		},
		url : "ajax_prd2_stock_prd_list.php",
		success: function(shtml) {
			$('#stock_prd_list').html(shtml);
		}
	});

}

//--> 
</script> 

<div id="contents_head">
	<h1>재고관리</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div id="" class="list-box-layout3 display-table">
			<ul class="display-table-cell width-50p v-align-top">
				
				<div class="list-button-wrap m-b-5">
					<ul>
						<select name="" onchange="prd2_stock_list(this.value)">
							<option value="all" selected>전체 상품</option>
<?
for($t=0; $t<count($koedge_prd_kind_array); $t++){
?>
							<option value="<?=$koedge_prd_kind_array[$t]['code']?>">(<?=$koedge_prd_kind_array[$t]['code']?>) <?=$koedge_prd_kind_array[$t]['name']?></option>
<?
}
?>
						</select>
					</ul>
					<ul><button type="button" id="bklist_open" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="prd2_stock_plus()">+ 상품추가</button></ul>
				</div>

				<div class="list-box-layout3-wrap" id="stock_prd_list">

				</div>

			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell v-align-top">

				<div class="list-button-wrap m-b-5"><!--_stock_day-->
					<b>일일재고등록</b> &nbsp;&nbsp;&nbsp;&nbsp;날짜 : <input type="text" id="stock_day" name="stock_day" value="<?=date("Y-m-d")?>" style="width:80px; cursor:pointer;" readonly />
				</div>
				<div class="list-box-layout3-wrap">
					<form id="form2">
					<input type="hidden" name="a_mode" value="day_stock">
					<input type="hidden" name="stock_day" id="stock_day">
					<table class="table-list" id="stock_prd_cart">
					</table>
					</form>
				</div>

			</ul>
		</div>

		<div class="list-bottom-btn-wrap">
			<ul class="list-top-total">
				<span class="count"></span>
			</ul>
			<ul class="list-top-btn">
				<!-- <button type="button" id="bklist_open" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="stockHand.dayStock(this)">재고 입출고 등록하기</button> -->

				<button type="button" id="bklist_open" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="alert(' 이제 사용 안함 ');">재고 입출고 등록하기 (사용금지)</button>

			</ul>
		</div>

	</div>
</div>
<script type="text/javascript"> 
<!-- 
prd2_stock_list('all');

function prd2_stock_cart_set(momkey, str){
	
	//var str = "P|48,49,297|오나홀@P|273,274,271|윤활젤@P|339,338,337|토이백@S|245@S|349@S|222@S|251";
	//var str = "P|234,235|오나홀@S|251";

	var arrString = str.split("@");

	var totalSetCount = arrString.length;

	var setName = $("#ps_prd_name_"+momkey).text();

	var html;
    for(var i=0; i<arrString.length; i++) { 

			html += "<tr>";

			if( i == 0 ){
			html += "<td rowspan='"+ totalSetCount +"'>세트상품<br>("+ setName +")</td>";
			}

		var t = arrString[i].split('|'); 
		if( t[0] == "P" ){

			var u = t[1].split(','); 

			html += "<td>";
			html += "<select name='stock_key[]'>";

			for(var z=0; z<u.length; z++) { 
				var prdName = $("#ps_prd_name_"+u[z]).text();
				html += "<option value='"+ u[z] +"' >"+ prdName +"</option> ";
			}

			html += "</select>";
			html += "</td>";
			html += "<td>";
			html += "<select name='stock_mode[]'>";
			html += "<option value='minus' selected>출고</option> ";
			html += "</select>";
			html += "</td>";

		}else if (t[0] == "S" ){
			
			var key = t[1];
			var prdName = $("#ps_prd_name_"+key).text();

			html += "<td>"+ prdName +"</td>";
			html += "<td>";
			html += "<input type='hidden' name='stock_key[]' value='"+ key +"'>";
			html += "<select name='stock_mode[]' >";
			html += "<option value='minus' selected>출고</option> ";
			html += "</select>";
			html += "</td>";

		}

			html += '<td><input type="text" name="stock_qry[]" style="width:80px;" placeholder="수량" value="1"/></td>';
			html += '<td>';
			html += '<select name="stock_kind[]">';
			html += '<option value="판매" selected>- 판매</option> ';
			html += '</select>';
			html += '</td>';
			html += '<td><input type="text" name="stock_memo[]" style="width:150px;" value="세트판매" /></td>';
			html += '<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="cartLineDel(this)">삭제</button></td>';

			html += "</tr>";
    }//for END

    $("#stock_prd_cart").prepend(html);
}


function prd2_stock_cart(key,mode){

	var brandName = $("#ps_brand_name_"+key).text();
	var prdName = $("#ps_prd_name_"+key).text();

	if( mode == "plus" ){
		var selectedP = "selected";
		var selectedM = "";
		var selectedP2 = "selected";
		var selectedM2 = "";
	}else{
		var selectedP = "";
		var selectedM = "selected";
		var selectedP2 = "";
		var selectedM2 = "selected";
	}

	var html;
		html += "<tr>";
 		html += "<td>"+ brandName +"</td>";
 		html += "<td>"+ prdName +"</td>";
 		html += "<td>";
 		html += "<input type='hidden' name='stock_key[]' value='"+ key +"'>";
 		html += "<select name='stock_mode[]' class='selectpicker'  size='2'>";
 		html += "<option value='plus' "+ selectedP +">입고</option> ";
 		html += "<option value='minus' "+ selectedM +">출고</option>";
 		html += "</select>";
 		html += "</td>";
 		html += '<td><input type="text" name="stock_qry[]" style="width:80px;" placeholder="수량" value="1"/></td>';
 		html += '<td>';
 		html += '<select name="stock_kind[]" size="5">';
 		html += '<option value="판매" '+ selectedM2 +'>- 판매</option> ';
 		html += '<option value="서비스">- 서비스</option>';
 		html += '<option value="신규입고" '+ selectedP2 +'>+ 신규입고</option>';
 		html += '<option value="반품">+ 반품</option>';
 		html += '<option value="조정">조정</option> ';
 		html += '</select>';
 		html += '</td>';
 		html += '<td><input type="text" name="stock_memo[]" style="width:150px;"  placeholder="메모" /></td>';
 		html += '<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="cartLineDel(this)">삭제</button></td>';
 		html += "</tr>";

 		$("#stock_prd_cart").prepend(html);
}

function cartLineDel(obj){
	$(obj).parent().parent().remove();
}

function dayStock(){
	$("#stock_day").val($("#_stock_day").val());
	$("#day_stock_form").submit();
}

function stockHistory(idx){
	window.open("popup_prd2_stock_history.php?idx="+idx, "prd2_stock_"+idx, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
function stockAlarm(idx){
	window.open("popup_prd2_stock_alarm.php?idx="+idx, "prd2_stock_alarm_"+idx, "width=700,height=400,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

var stockHand = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		dayStock : function(obj) {

			var formData = $("#form2").serializeArray();
			var stock_day = $("#stock_day").val();

			formData.push({name: "stock_day", value: stock_day});

			$(obj).attr('disabled', true);

			$.ajax({
				url: "processing.prd2_stock.php",
				data : formData, 
				type: "POST",
				dataType: "json",
				success: function(res){
					$("#stock_prd_cart tr").remove();
					showAlert("OK", "재고등록 성공", "alert2", "good" );
					return false;
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
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

	var content22 = '이 페이지(재고관리)는 곧 폐기될 예정입니다.<br>상품별 재고 입/출고를 사용해야 할경우'
		+ '<br>상품 정보창을 열어서 등록해주세요.';


	$.confirm({
		boxWidth : "500px",
		useBootstrap : false,
		icon: 'fas fa-exclamation-triangle',
		title: '공지',
		content: content22,
		type: 'red',
		typeAnimated: true,
		closeIcon: true,
		buttons: {
			cencle: {
				text: '확인/닫기',
				action: function(){
				}
			}
		}
	});

});

//--> 
</script> 


<?
include "../layout/footer.php";
exit;
?>