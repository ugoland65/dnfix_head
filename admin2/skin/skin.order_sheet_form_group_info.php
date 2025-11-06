<?
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from ona_order_prd WHERE oop_idx = '".$_idx."' "));
	
	$_oop_idx = $data['oop_idx'];

	$_oop_json_check_data = substr($data['oop_data'], 0,1);
	if( $_oop_json_check_data == "[" ){
		$_oop_json = $data['oop_data'];
	}else{
		$_oop_json = '['.$data['oop_data'].']';
	}

	$_prd_jsondata = json_decode($_oop_json, true);
}
?>
<div class="prd-search-add-wrap">
	<ul class="left">
	
		<div>
			<input type="text" name="prdSearch"  id="prdSearch" value="" autocomplete="off" placeholder="검색어">
		</div>	
		<div class="m-t-5 m-b-10 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="osFormGroupReg.prdSearch(this);" >상품검색</button>
		</div>
		<div id="prdSearch_result">
		</div>

	</ul>
	<ul class="right">

		<div>※ 추가된 상품은 저장을 눌러야 최종 적용됩니다.</div>
		<div class="prd-search-add-prd-list-wrap">

			<form id="form2">
			<input type="hidden" name="a_mode" value="orderSheetForm_group_prd_inout" >
			<input type="hidden" name="idx" value="<?=$_idx?>" >

			<div id="prd_search_add_prd_list_table" class="prd-search-add-prd-list-table">
<?
for ($z=0; $z<count($_prd_jsondata); $z++){

	$_prd_idx = $_prd_jsondata[$z]['idx'];
	$_ps_idx = $_prd_jsondata[$z]['stockidx'];
	$_pname = $_prd_jsondata[$z]['pname'];

	$_om = $_prd_jsondata[$z]['om']; //주문메모
	$_state = $_prd_jsondata[$z]['state']; //판매상태

	if(($z%2) == 0){
		$_tr_color = "#ffffff";
	}else{
		$_tr_color = "#eee";
	}

	$_colum = "A.CD_IMG, A.CD_CODE2, A.CD_CODE3, A.CD_NAME";
	$_colum .= ",B.ps_idx";

	$_query = "select ".$_colum." from "._DB_COMPARISON." A
		left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX  ) 
		where CD_IDX = '".$_prd_idx."' ";

	$prd_data = sql_fetch_array(sql_query_error($_query));

	if( $prd_data['CD_IMG'] ){
		$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
	}
	$_code2 = $prd_data['CD_CODE2'];
	$_code3 = $prd_data['CD_CODE3'];

	if( !$_pname ) $_pname = $prd_data['CD_NAME'];

?>
<ul class="" data-prdidx="<?=$_prd_idx?>">
	<input type="hidden" name="prd_idx[]" value="<?=$_prd_idx?>" >
	<input type="hidden" name="ps_idx[]" value="<?=$prd_data['ps_idx']?>" >
	<li class="text-center" style="width:40px"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></li>
	<li class="text-center" style="width:45px"><?=$_prd_idx?></li>
	<li class="text-center" style="width:55px"><img src="<?=$img_path?>" style="height:45px; border:1px solid #ddd;"></li>
	<li class="text-center" style="width:80px">
		<b><?=$_code2?></b>
		<? if( $_code3 ){ ?><br><?=$_code3?><? } ?>
	</li>
	<li>
		<div>
			<?=$_pname?>
			<button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?=$_prd_idx?>','info');"">보기</button>
		</div>
		<div>
			<input type="text" name="ordermemo[]" value="<?=$_om?>" class="m-t-2" placeholder="주문 메모" style="font-size:12px;" >
		</div>
	</li>
	<li class="text-center" style="width:75px">
		<select name="state[]">
			<option value="on" <? if( $_state == "on" ) echo "selected"; ?> >판매</option>
			<option value="out" <? if( $_state == "out" ) echo "selected"; ?>>단종</option>
			<option value="off" <? if( $_state == "off" ) echo "selected"; ?>>감춤</option>
		</select>
	</li>
	<li class="" style="width:50px"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="osFormGroupReg.prdListDel(this)" ><i class="fas fa-trash-alt"></i></button></li>
</ul>
<? } ?>

			</div>
			</form>

		</div>

		<div class="m-t-5 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="osFormGroupReg.prdSave(this, '<?=$_idx?>');" >그룹 상품 노출순서 저장</button>
		</div>

	</ul>
</div>

<script type="text/javascript"> 
<!--
var osFormGroupReg = function() {

	var prdSearchResultVal;

	return {
		
		init : function() {

		},

		prdSave : function( obj, oop_idx ) {

			//$(obj).attr('disabled', true);

			var formData = $("#form2").serializeArray();

			$.ajax({
				url: "/ad/processing/order_sheet",
				data: formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						toast2("success", "그룹 상품리스트", "설정이 저장되었습니다.");
						orderSheetForm.groupViewReset( oop_idx );
						orderSheetDetail.PrdListReload();
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
						
						if( res.count == 0 ){
							showAlert("Error", "검색 결과가 없습니다.", "alert2" );
							return false;
						}

						var shtml = '<div class="m-t-10"><b>'+ keyword +'</b> 검색결과 : (<b>'+ res.count +'</b>)건</div>'
							+ '<div class="m-t-5" id="prdSearch_result_list">'
							+ '<table class="table-style border01 width-full">';
						
						for (var i = 0; i < res.prd_data.length; i++) {

							let key = res.prd_data[i].idx;

							shtml += '<tr>'
								+ '<td class="text-center" style="width:30px"><input type="checkbox" name="" class="prd-search-result-checkbox" value="'+ res.prd_data[i].idx +'" '
								+ ' data-psidx = "'+ res.prd_data[i].ps_idx +'" '
								+ ' data-img = "'+ res.prd_data[i].img +'" '
								+ ' data-prdname = "'+ res.prd_data[i].name +'" '
								+ ' ></td>'
								+ '<td class="text-center" style="width:50px"><img src="/data/comparion/'+ res.prd_data[i].img +'" style="width:40px; "></td>'
								+ '<td>'
								+ '<span class="prd-code">'+ res.prd_data[i].idx + ' | '+ res.prd_data[i].jancode +'</span><br>'
								+ '<span class="prd-name">'+ res.prd_data[i].name +'</span>'
								+ ' <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView(\''+ res.prd_data[i].idx +'\',\'info\');" >보기</button>'
								+ '</td>'
								+ '</tr>';

						}

						shtml += '</table>'
							+ '</div>'
							+ '<div class="m-t-5 m-b-10 text-center">'
							+ '<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm prd-search-add-btn" onclick="osFormGroupReg.prdSearchAdd(this);" >선택상품 추가</button>'
							+ '</div>';

						$("#prdSearch_result").html(shtml);
						$("#prdSearch").val("");

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

			var shtml = '';
			var checkedCount = 0;
			var overlappingCkCount = 0;

			$(".prd-search-result-checkbox:checked").each(function(){

				var _prd_idx = $(this).val();
				var _prd_ps_idx = $(this).data("psidx");
				var _prd_img = $(this).data("img");
				var _prd_name = $(this).data("prdname");

				var overlappingCk = "on";
				checkedCount++;

				//중복체크
				$("#prd_search_add_prd_list_table ul").each(function(){
					if( _prd_idx == $(this).data("prdidx") ){
						overlappingCk = "off";
						overlappingCkCount++;
					}
				});

				if( overlappingCk == "on" ){
					
					shtml += '<ul class="add-prd" data-prdidx="'+ _prd_idx +'" >'
						+ '<input type="hidden" name="prd_idx[]" value="'+ _prd_idx +'" >'
						+ '<input type="hidden" name="ps_idx[]" value="'+ _prd_ps_idx +'" >'
						+ '<li class="text-center" style="width:40px"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></li>'
						+ '<li class="text-center" style="width:45px">'+ _prd_idx +'</li>'
						+ '<li class="text-center" style="width:55px"><img src="/data/comparion/'+ _prd_img +'" style="height:45px; border:1px solid #ddd;" ></li>'
						+ '<li class="text-center" style="width:80px">'
						+ '</li>'
						+ '<li>'
						+ '<div>'
						+ _prd_name
						+ ' <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView(\''+ _prd_idx +'\',\'info\');" >보기</button>'
						+ '</div>'
						+ '<div>'
						+ '<input type="text" name="ordermemo[]" value="" class="m-t-2" placeholder="주문 메모" style="font-size:12px;" >'
						+ '</div>'
						+ '</li>'
						+ '<li class="text-center" style="width:75px" >'
						+ '<select name="state[]">'
						+ '<option value="on">판매</option>'
						+ '<option value="out">단종</option>'
						+ '<option value="off">감춤</option>'
						+ '</select>'
						+ '</li>'
						+ '<li class="" style="width:50px"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="osFormGroupReg.prdListDel(this)" ><i class="fas fa-trash-alt"></i></button></li>'
						+ '</ul>';

				} //if( overlappingCk == "on" ){

			});

			$("#prd_search_add_prd_list_table").prepend(shtml);
			
			

			if( overlappingCkCount > 0 ){
				showAlert("Good", "총 선택된 상품 ("+ checkedCount +")개중<br>중복("+ overlappingCkCount +")을 제외한 ("+ (checkedCount - overlappingCkCount) +")상품이 추가되었습니다.<br>추가된 상품은 저장을 눌러야 최종 적용됩니다.", "alert2", "good" );
				return false;
			}

		},

		//상품 라인에서 삭제
		prdListDel : function( obj ) {
			$(obj).closest('ul').remove();
		},

		save : function(obj) {

			$(obj).attr('disabled', true);

			var formData = $("#form1").serializeArray();

			$.ajax({
				url: "/ad/processing/order_sheet",
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

		}

	};

}();

$(function(){

	$( "#prd_search_add_prd_list_table" ).sortable({
		axis: "y",
		cursor: "move"
	});


	$("#prdSearch").bind("keydown", function(e){
		if(e.which=="13"){
			osFormGroupReg.prdSearch();
		}
	});

});
//--> 
</script>