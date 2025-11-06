<?

	$_smonth = date("Y-m");

?>
<script src="/plugins/jquery-ui/jquery.ui.monthpicker.js"></script>
<div id="contents_head">
	<h1>입출금 장부통계</h1>

	<!-- 
	<div class="head-btn-wrap m-l-10">
		<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="ledge.bankStatementExcelUpload();" >입출금 엑셀등록</button>
	</div>
	-->

	<!-- 
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="moneyPlan.reg(this)" > 
			<i class="fas fa-plus-circle"></i>
			신규 등록
		</button>
	</div>
	-->

	<div class="head-left-fixed-wrap">

		<div>
			<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
		</div>

		<div class="calendar-wrap-month">
			<ul>
				<label for="monthpicker"><i class="far fa-calendar-alt m-r-7"></i><input type="text" name="" value="<?=$_smonth?>" id="monthpicker" style="width:100px;" readonly></label>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="ledgeChart.show()" > 기간 검색</button>
			</ul>
		</div>

		<div class="m-t-20 text-right">
			2022 12월 31일 마감<br>
			국민 <b>3,557,537</b>원 <br>
			농협 <b>18,165,149</b>원 <br>
			하나 <b>530,883</b>원 <br>
			합계 <b><?=number_format(3557537 + 18165149 + 530883)?><br>
		</div>

	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap" class="have-head-left-fixed">

		<div id="list_wrap"></div>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script type="text/javascript"> 
<!--
var ledgeChart = function() {

	var smonth = "<?=$_smonth?>";

	var C = function() {
	};

	return {

		init : function() {

		},

		show: function(  ) {

			var ym = $("#monthpicker").val();

			$.ajax({
				url: "/ad/ajax/ledge_chart_list",
				data: { "ym":ym },
				type: "POST",
				dataType: "html",
				success: function(getdata){
					$('#list_wrap').html(getdata);
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {
					loading('off','white');
				}
			});

		},

	};

}();


$(function(){

	ledgeChart.show();

	var monthPickerOptions = {
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dateFormat: 'yy-mm'
	};

	if( $(".calendar-wrap-month").length ){
		$("#monthpicker").monthpicker(monthPickerOptions);
	}

});
//--> 
</script>