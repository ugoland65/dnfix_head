<?
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from prd_rack WHERE idx = '".$_idx."' "));

	$_data = json_decode($data['info'], true);

}else{

}

?>
<style type="text/css">
.os-form-wrap{ display:table; width:100%; }
.os-form-wrap > ul{ display:table-cell; vertical-align:top; }
.os-form-wrap > ul.left{ width:400px; } 
.os-form-wrap > ul.right{ padding-left:15px; } 

#group_prd_list_table{ width:100%; display:table; }
#group_prd_list_table ul { width:100%; background:#fff; display:table-row; }
#group_prd_list_table ul li { display:table-cell; padding:5px; vertical-align:middle; border-bottom:1px solid #ddd; }
#group_prd_list_table ul.add-prd{ background:#ffffd9; }

#prdSearch_result_list{ width:100%; height:200px; overflow-y:scroll;  box-sizing:border-box; border:1px solid #999; }
#prdSearch_result_list::-webkit-scrollbar{ width:7px; background:#ccc; border-left:solid 1px rgba(255,255,255,.1)}
#prdSearch_result_list::-webkit-scrollbar-thumb{ background:linear-gradient(#0860d5,#2077ea);border:solid 1px #444; border-radius:3px; }
</style>
<div class="os-form-wrap">
	<ul class="left">

		<form id="form3">
		<table class="table-style border01 width-full">
			<tr>
				<th style="width:100px;">추가 상품검색</th>
				<td>
					<input type="text" name="prdSearch"  id="prdSearch" value="" autocomplete="off" >
					<div class="admin-guide-text">
						- 상품 IDX, 또는 상품명, 코드명
					</div>
				</td>
			</tr>
		</table>
		<div class="m-t-5 m-b-10 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdRackReg.prdSearch(this);" >상품검색</button>
		</div>
		<div id="prdSearch_result">
		</div>
		</form>

		<form id="form1">
			<? if( $_idx ){ ?>
				<input type="hidden" name="a_mode" value="prdRack_modify" >
				<input type="hidden" name="idx" value="<?=$_idx?>" >
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="prdRack_reg" >
			<? } ?>

			<table class="table-style border01 width-full">

				<tr>
					<th>랙 이름</th>
					<td colspan="3">
						<input type='text' name='name'  value="<?=$data['name']?>" autocomplete="off" >
					</td>
				</tr>
				<tr>
					<th>랙 코드</th>
					<td colspan="3">
						<input type='text' name='code'  value="<?=$data['code']?>" autocomplete="off" >
						<div class="admin-guide-text">
							- 상품에 지정될 코드
						</div>
					</td>
				</tr>
				<tr>
					<th>메모</th>
					<td colspan="3">
						<textarea name="memo"><?=$data['memo']?></textarea>
					</td>
				</tr>

			</table>
		</form>
		<div class="m-t-10 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdRackReg.save(this);" >전송</button>
		</div>

	</ul>
	<ul class="right" <? if( !$_idx ) echo "display-none"; ?>">

		<div class="group-list-wrap m-t-6">

			<div id="group_prd_list_table">
			
			</div>
		</div>

	</ul>
</div>

<script type="text/javascript"> 
<!-- 
var prdRackReg = function() {

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
				url: "/ad/processing/rack",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						<? if( $_idx ){ ?>
						alert("수정되었습니다.");
						<? }else{?>
						alert("등록되었습니다.");
						location.reload();
						<? } ?>
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

		//상품검색
		prdSearch : function( obj, oop_idx ) {

			var keyword = $("#prdSearch").val();

			if( !$("#prdSearch").val() ){
				showAlert("Error", "검색어를 입력해주세요.", "alert2" );
				return false;
			}

			$.ajax({
				url: "/ad/processing/order_sheet",
				data: { 
					"a_mode" : "orderSheetForm_group_prd_search",
					"keyword" : keyword
				},
				type: "POST",
				dataType: "json",
				success: function(res){
					
					if ( res.success == true ){
						
						var shtml = '<div class="m-t-10">검색결과 : (<b>'+ res.count +'</b>)건</div>'
							+ '<div class="m-t-5" id="prdSearch_result_list">'
							+ '<table class="table-style border01 width-full">';
						
						for (var i = 0; i < res.prd_data.length; i++) {

							let key = res.prd_data[i].idx;

							if ( res.prd_data[i].ps_rack_code ){
								var _rack_code = "[ "+ res.prd_data[i].ps_rack_code + " ]";
							}else{
								var _rack_code = "[ 랙 미지정 ]";
							}

							shtml += '<tr>'
								+ '<td class="text-center" style="width:30px"><input type="checkbox" name="" class="prd-search-result-checkbox" value="'+ res.prd_data[i].idx +'" '
								+ ' data-psidx = "'+ res.prd_data[i].ps_idx +'" '
								+ ' data-img = "'+ res.prd_data[i].img +'" '
								+ ' data-prdname = "'+ res.prd_data[i].name +'" '
								+ ' ></td>'
								+ '<td class="text-center" style="width:50px"><img src="/data/comparion/'+ res.prd_data[i].img +'" style="width:40px; "></td>'
								+ '<td>'
								+ res.prd_data[i].idx + ' '+_rack_code  +'<br>'
								+ res.prd_data[i].name
								+ ' <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView(\''+ res.prd_data[i].idx +'\',\'info\');" >보기</button>'
								+ '</td>'
								+ '</tr>';

						}

						shtml += '</table>'
							+ '</div>'
							+ '<div class="m-t-5 m-b-10 text-center">'
							+ '<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdRackReg.prdSearchAdd(this);" >선택상품 추가</button>'
							+ '</div>';

						$("#prdSearch_result").html(shtml);

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

		},

		//검색된 상품 추가하기
		prdSearchAdd : function( obj ) {

			if( $(".prd-search-result-checkbox:checked").length == 0 ){
				showAlert("Error", "선택된 상품이 없습니다.", "alert2" );
				return false;
			}

		},


	};

}();

$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

});
//--> 
</script> 