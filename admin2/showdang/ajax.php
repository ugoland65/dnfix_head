<?
include "../lib/inc_common.php";


	//넘어온 변수 전체 검열
	while(list($key,$val)= each($_POST)){
		${"_".$key} = securityVal($val);
	}

////////////////////////////////////////////////////////////////////////////// 
// 신규생성
if( $_mode == "brandLinkNewReq" ){
?>
<div class="table-wrap">
	<form id="form1">
	<input type="hidden" name="a_mode" value="brandLinkNewReq">
	<table class="table-style width-full">
		<tr>
			<th class="tds1">브랜드 키워드</th>
			<td class="tds2">
				<input type='text' name='bl_keyword' id='bl_keyword' >
			</td>
		</tr>
		<tr>
			<th class="tds1">링크 URL</th>
			<td class="tds2">
				<input type='text' name='bl_link' id='bl_link' value="/brand/list.html?cate_no="><br>
				/brand/list.html?cate_no=<b style="color:#ff0000">{카페24분류번호}</b>
			</td>
		</tr>
	<table>
	</form>
	<div class="text-center m-t-10"><button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="showdangAajx.brandLinkNewReq(this);" >등록</button></div>
</div>

<? } ?>

<script type="text/javascript"> 
<!-- 
var showdangAajx = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		brandLinkNewReq : function(obj) {

			$(obj).attr('disabled', true);

			var formData = $("#form1").serializeArray();
			$.ajax({
				url: "processing.showdang.php",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
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
//--> 
</script> 