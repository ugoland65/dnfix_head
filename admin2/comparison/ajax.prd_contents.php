<?
	include "../lib/inc_common.php";

	$_idx = securityVal($prd_idx);

	$prd_cont_data = wepix_fetch_array(wepix_query_error("select * from prd_contents where cd_idx = '".$_idx."' "));
	if( !$prd_cont_data[cd_idx] ){
		wepix_query_error("insert into  prd_contents set cd_idx = '".$_idx."' ");
		$prd_cont_data = wepix_fetch_array(wepix_query_error("select * from prd_contents where cd_idx = '".$_idx."' "));
	}

?>

<div class="crm-title">
	<h3>컨텐츠 관리</h3>
	<ul class="crm-req-date">
	</ul>
</div> 

<form id="form1">
<input type="hidden" name="a_mode" value="prdContents" >
<input type="hidden" name="idx" value="<?=$prd_cont_data[pc_idx]?>" >

<div class="crm-detail-info">
	<table class="table-style">
		<tr>
			<th class="tds1">상품 패키지 (19금)</th>
			<td class="tds2">
				<label><input type="radio" name="c19" value="Y" <? if($prd_cont_data[c19] == "Y" ) echo "checked"; ?>>19금 패키지</label>
				<label><input type="radio" name="c19" value="N" <? if($prd_cont_data[c19] == "N" ) echo "checked"; ?>>누구나 봐도 되는 패키지</label>
			</td>
		</tr>

	</table>
</div>
</form>

<div class="text-center m-t-10">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-submit" onclick="prdContents.submit(this);" > 
		<i class="far fa-check-circle"></i> 수정
	</button>
</div>

<script type="text/javascript"> 
<!-- 
var prdContents = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		submit : function(obj) {

			var formData = $("#form1").serializeArray();

			$(obj).attr('disabled', true);
			$.ajax({
				url: "processing.prd.php",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "설정", "설정이 저장되었습니다.");
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