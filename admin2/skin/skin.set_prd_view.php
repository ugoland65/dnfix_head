<?
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from prd_set WHERE pset_idx = '".$_idx."' "));

	$_pset_goods = json_decode($data['pset_goods'], true);

	$page_title_text = "세트상품 수정";
	$submit_btn_text = "세트상품 수정";

}else{

	$page_title_text = "세트상품 등록";
	$submit_btn_text = "세트상품 등록";

}

?>

<form name='form1' id="form1" method='post' enctype="multipart/form-data" autocomplete="off" >

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="prd2setModify">
	<input type="hidden" name="moidx" id="moidx" value="<?=$_idx?>">
<? }else{ ?>
	<input type="hidden" name="a_mode" value="prd2setNew">
<? } ?>

<table class="table-style border01 width-full">
	<tr>
		<th style="width:150px;">세트명</th>
		<td><input type='text' name='pset_name' id='pset_name' class="width-full" value="<?=$data['pset_name']?>" ></td>
	</tr>
	<tr>
		<th>이미지</th>
		<td>
			<input type="file" id="bd_logo" name="bd_logo" >
			<? if( $brand_data[BD_LOGO] ){ ?>
			<div>
			<img src="../../data/brand_logo/<?=$brand_data[BD_LOGO]?>" alt="">
			</div>
			<? } ?>
		</td>
	</tr>

	<? if( $_idx ){ ?>
	<tr>
		<th>세트코드</th>
		<td>
			<b>NSET-<?=$_idx?></b>
		</td>
	</tr>
	<tr>
		<th>현재재고</th>
		<td><b><?=$data['pset_stock']?></b></td>
	</tr>
	<tr>
		<th>재고관리</th>
		<td>

			<div>
				<label><input type="radio" name="psu_mode" value="ibgo_plus"> 입고증가</label>
				<label><input type="radio" name="psu_mode" value="ibgo_minus"> 입고감소</label>
				&nbsp;|&nbsp;
				<label><input type="radio" name="psu_mode" value="plus" checked> 증가</label>
				<label><input type="radio" name="psu_mode" value="minus"> 감소</label>
			</div>

			<div class="m-t-10">
				수량 : <input type="text" name="psu_stock" id="psu_stock" style="width:50px;">
				사유 : <input type="text" name="psu_memo" id="psu_memo" style="width:150px;">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prd3SetReg.setStock(this);" >재고변경</button>
			</div>

			<div style="color:#ff0000;" class="m-t-5">
				※주의 :: 입고증가, 입고감소는 각 상품의 현재고량에서 재고를 가져오고 뺍니다.
			</div>
		</td>
	</tr>
	<? } ?>

	<tr>
		<th>상품</th>
		<td>
			<div>
				상품 IDX : <input type='text' name='addGoods_idx' id='addGoods_idx' size='40' style="width:70px">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prd3SetReg.addGoods(this);" >상품추가</button>
			</div>
			<div id="" class="m-t-10">
				<table id="addGoods_table">
					<tr>
						<th>IDX</th>
						<th>재고코드</th>
						<th>이미지</th>
						<th>브랜드/상품명</th>
						<th>현 재고량</th>
						<th>삭제</th>
					</tr>
<?
for ($i=0; $i<count($_pset_goods); $i++){

$_addGoods_idx = $_pset_goods[$i]['idx'];

$colum = "A.CD_IDX, A.CD_NAME , A.CD_IMG";
$colum .= ", B.ps_idx, B.ps_stock";
$colum .= ", C.BD_NAME";

$query = "select ".$colum."
from "._DB_COMPARISON." A 
left join prd_stock B ON ( B.ps_prd_idx = A.CD_IDX )
left join "._DB_BRAND." C ON ( C.BD_IDX = A.CD_BRAND_IDX )
where CD_IDX = '".$_addGoods_idx."' ";


$_data = wepix_fetch_array(wepix_query_error($query));

?>
					<tr id='tr_<?=$_pset_goods[$i]['idx']?>'>
						<td>
							<input type='hidden' name='idx[]' value='<?=$_pset_goods[$i]['idx']?>' >
							<input type='hidden' name='stock_idx[]' value='<?=$_pset_goods[$i]['stock_idx']?>' >
							<?=$_pset_goods[$i]['idx']?>
						</td>
						<td><?=$_pset_goods[$i]['stock_idx']?></td>
						<td><img src='../../data/comparion/<?=$_data['CD_IMG']?>' style='width:100px;'></td>
						<td><?=$_data['BD_NAME']?><br><b><?=$_data['CD_NAME']?></b><br><button type='button' class='btnstyle1 btnstyle1-xs' onclick="comparisonQuick('<?=$_pset_goods[$i]['idx']?>','info');" >상품정보</button></td>
						<td><?=$_data['ps_stock']?></td>
						<td><button type='button' class='btnstyle1 btnstyle1-danger btnstyle1-xs' onclick='' >삭제</button></td>
					</tr>
<? } ?>
				</table>
			</div>
		</td>
	</tr>
</table>
		
	</form>

<script type="text/javascript"> 
<!-- 
var prd3SetReg = function() {

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
				url: "processing.prd2_set.php",
				data : formData, 
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "등록", "설정이 저장되었습니다.");
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

		addGoods : function(obj) {

			var addGoods_idx = $("#addGoods_idx").val();

			if( addGoods_idx == "" ){
					showAlert("Error", "상품 IDX가 없습니다.", "alert2" );
					return false;
			}

			$(obj).attr('disabled', true);
			$.ajax({
				url: "ajax_data.php",
				data: { "a_mode":"prd3SetRegAddPrd", "addGoods_idx":addGoods_idx },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){

						var aHtml = ""
						+ "<tr id='tr_"+ addGoods_idx +"'>"
						+ "<td>"
						+ "<input type='hidden' name='idx[]' value='"+ addGoods_idx +"' >"
						+ "<input type='hidden' name='stock_idx[]' value='"+ res.stock_idx +"' >"
						+ addGoods_idx +"</td>"
						+ "<td>"+ res.stock_idx +"</td>"
						+ "<td><img src='../../data/comparion/"+ res.img +"' style='width:100px;'></td>"
						+ "<td>"+ res.brand +"<br><b>"+ res.name +"</b><br><button type='button' class='btnstyle1 btnstyle1-xs' onclick=\"comparisonQuick('"+ addGoods_idx +"','info');\" >상품정보</button></td>"
						+ "<td>"+ res.stock +"</td>"
						+ "<td><button type='button' class='btnstyle1 btnstyle1-danger btnstyle1-xs' onclick='' >삭제</button></td>"
						+ "</tr>";

						$("#addGoods_table").append(aHtml);
						$("#addGoods_idx").val("");

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

		// 세트상품 재고변경
		setStock : function(obj) {

			var idx = $("#moidx").val();
			var psu_mode = $("input[name='psu_mode']:checked").val();
			var psu_stock = $("#psu_stock").val();
			var psu_memo = $("#psu_memo").val();

			$(obj).attr('disabled', true);
			$.ajax({
				url: "/ad/processing/prd",
				data: { "a_mode":"prd3SetStock", "idx":idx, "psu_mode":psu_mode, "psu_stock":psu_stock, "psu_memo":psu_memo },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						alert("재고변경완료");
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