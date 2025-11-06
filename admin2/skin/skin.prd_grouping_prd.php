<?
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from prd_grouping WHERE idx = '".$_idx."' "));

	$_prd_jsondata = json_decode($data['data'], true);


}
?>
<div class="prd-search-add-wrap">
	<ul class="left">
	
		<div>
			<input type="text" name="prdSearch"  id="prdSearch" value="" autocomplete="off" placeholder="검색어" >
		</div>	
		<div class="m-t-5 m-b-10 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdGroupingAdd.prdSearch(this);" >상품검색</button>
		</div>
		<div id="prdSearch_result">
		</div>

		<div>
			<ul>수동 인스턴트 상품등록</ul>
			<ul><input type="text" name="prd_instant_name"  id="prd_instant_name" value="" autocomplete="off" placeholder="상품명" ></ul>
		</div>
		<div class="m-t-5 m-b-10 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdGroupingAdd.prdInstantAdd(this)" >수동 인스턴트 상품 추가</button>
		</div>

	</ul>
	<ul class="right">
		
		<div>※ 추가된 상품은 저장을 눌러야 최종 적용됩니다. <span id="add_prd_count"></span></div>
		<div class="prd-search-add-prd-list-wrap">

			<form id="form2">
			<input type="hidden" name="a_mode" value="prdGrouping_prd_inout" >
			<input type="hidden" name="idx" value="<?=$_idx?>" >

			<div id="prd_search_add_prd_list_table" class="prd-search-add-prd-list-table">
<?
for ($z=0; $z<count($_prd_jsondata); $z++){

	$_prd_idx = $_prd_jsondata[$z]['idx'];
	$_ps_idx = $_prd_jsondata[$z]['stockidx'];
	$_mode_data = $_prd_jsondata[$z]['mode_data'];
	$_pname = $_prd_jsondata[$z]['pname'];


	if(($z%2) == 0){
		$_tr_color = "#ffffff";
	}else{
		$_tr_color = "#eee";
	}

	if( $_prd_idx == "Instant" ){
	}else{

		$_colum = "A.CD_IMG, A.CD_CODE2, A.CD_CODE3, A.CD_NAME, A.cd_code_fn, A.cd_sale_price, A.cd_cost_price, A.CD_MEMO";
		$_colum .= ",B.ps_idx, B.ps_in_sale_s, B.ps_in_sale_e, B.ps_in_sale_data";
		$_colum .= ", C.BD_NAME";

		$_query = "select ".$_colum." from "._DB_COMPARISON." A
			left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX  ) 
			left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND_IDX  ) 
			where CD_IDX = '".$_prd_idx."' ";

		$prd_data = sql_fetch_array(sql_query_error($_query));

		if( $prd_data['CD_IMG'] ){
			$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
		}
		$_code2 = $prd_data['CD_CODE2'];
		$_code3 = $prd_data['CD_CODE3'];

		$_cd_code_data = json_decode($prd_data['cd_code_fn'], true);

		$_jancode = $_cd_code_data['jan'];

		if( !$_pname ) $_pname = $prd_data['CD_NAME'];
		
		$_brand_name = $prd_data['BD_NAME'];

		if( $prd_data['cd_sale_price'] < 29999 ){
			$_margin_per =  round( ($prd_data['cd_sale_price'] - $prd_data['cd_cost_price'] ) / $prd_data['cd_sale_price'] * 100, 2);
		}else{
			$_margin_per =  round( ($prd_data['cd_sale_price'] - ($prd_data['cd_cost_price'] + 2500) ) / $prd_data['cd_sale_price'] * 100, 2);
		}

	}

?>

<ul class="" data-prdidx="<?=$_prd_idx?>">
	<input type="hidden" name="prd_idx[]" value="<?=$_prd_idx?>" >
	<input type="hidden" name="ps_idx[]" value="<?=$prd_data['ps_idx']?>" >
	<input type="hidden" name="mode_data[]" value='<?=json_encode($_mode_data, JSON_UNESCAPED_UNICODE)?>' >
	<input type="hidden" name="memo[]" value='<?=$_prd_jsondata[$z]['memo']?>' >

	<li class="text-center" style="width:40px"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></li>
	<li class="text-center" style="width:45px"><?=$_prd_idx?></li>
	
	<? if( $_prd_idx == "Instant" ){ ?>
		
		<li class="text-center" style="width:55px; height:45px; ">
			
		</li>
		<li class="text-center" style="width:80px">
		</li>
		<li>
			<input type="text" name="prd_name[]" value="<?=$_pname?>" >
		</li>
	
	<? }else{ ?>

		<li class="text-center" style="width:55px"><img src="<?=$img_path?>" style="height:45px; border:1px solid #ddd;"></li>
		<li class="text-center" style="width:80px; font-size:12px;">
			<?=$_brand_name?>
			<!-- 
				<b><?=$_code2?></b>
				<? if( $_code3 ){ ?><br><?=$_code3?><? } ?>
			-->
		</li>
		<li>
			<input type="hidden" name="prd_name[]" value="<?=$_pname?>" >
			<div>
				<ul><span class="prd-code"><?=$_jancode?></span></ul>
				<ul>
					<?=$_pname?>
					<button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?=$_prd_idx?>','info');"">보기</button>
				</ul>
				<? if( $prd_data['cd_sale_price'] > 0 && $prd_data['cd_cost_price'] > 0 ){ ?>
				<ul class="m-t-5" style="font-size:12px;"><?=number_format($prd_data['cd_sale_price'])?> ( <?=number_format($prd_data['cd_cost_price'])?> ) <b><?=$_margin_per?></b>%</ul>
				<? } ?>
				<? if( $prd_data['CD_MEMO'] ){ ?><ul class="m-t-3"><span class="prd-memo"><i class="fas fa-feather-alt"></i> <?=$prd_data['CD_MEMO']?></span></ul><? } ?>
				<ul ><?=in_sale_icon($prd_data['ps_in_sale_s'], $prd_data['ps_in_sale_e'], $prd_data['ps_in_sale_data'])?></ul>
			</div>
		</li>

	<? } ?>

	<li class="" style="width:50px"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="prdGroupingAdd.prdListDel(this)" ><i class="fas fa-trash-alt"></i></button></li>
</ul>
<? } ?>

			</div>
			</form>

		</div>

		<div class="m-t-5 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdGroupingAdd.prdSave(this, '<?=$_idx?>');" >상품/노출순서 저장</button>
		</div>

	</ul>
</div>

<?
/*
	var oopJsondata = <?=$data['oop_data']?>;
*/
?>

<script type="text/javascript"> 
<!--
var prdGroupingAdd = function() {

	var prdSearchResultVal;

	return {
		
		init : function() {

		},

		prdSave : function( obj, oop_idx ) {

			var formData = $("#form2").serializeArray();

			$.ajax({
				url: "/ad/processing/prd_grouping",
				data: formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						alert("저장 완료!");
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
					//$(obj).attr('disabled', false);
				}
			});

		},

		//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		// 상품검색
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

						var shtml = '<div class="m-t-10"><b>'+ keyword +'</b> 검색결과 : (<b>'+ res.count +'</b>)건<br><span style="color:#ff0000; font-size:12px;">※ 이미지 더블클릭 추가가능</span></div>'
							+ '<div class="m-t-5" id="prdSearch_result_list">'
							+ '<table class="table-style border01 width-full">';
						
						for (var i = 0; i < res.prd_data.length; i++) {

							let key = res.prd_data[i].idx;

							shtml += '<tr>'
								+ '<td class="text-center" style="width:30px"><input type="checkbox" id="prd_search_'+ res.prd_data[i].idx +'" class="prd-search-result-checkbox" value="'+ res.prd_data[i].idx +'" '
								+ ' data-psidx = "'+ res.prd_data[i].ps_idx +'" '
								+ ' data-img = "'+ res.prd_data[i].img +'" '
								+ ' data-prdname = "'+ res.prd_data[i].name +'" '
								+ ' ></td>'
								+ '<td class="text-center" style="width:50px"><img src="/data/comparion/'+ res.prd_data[i].img +'" style="width:40px; cursor:pointer;" ondblclick="prdGroupingAdd.prdSearchAdd(\'dblclick\', \''+ res.prd_data[i].idx +'\');"></td>'
								+ '<td><div>'
								+ '<div>'
								
								+ '<ul><span class="prd-code"><label for="prd_search_'+ res.prd_data[i].idx +'">'+ res.prd_data[i].idx + ' | '+ res.prd_data[i].jancode +'</span></ul>'
								+ '<ul><span class="prd-name">'+ res.prd_data[i].name +'</label></span>'
								+ ' <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView(\''+ res.prd_data[i].idx +'\',\'info\');" >보기</button></ul>';
							
							if ( res.prd_data[i].in_sale_icon != "" ){
								shtml += '<ul id="in_sale_icon_ul_'+ res.prd_data[i].ps_idx +'">'+ res.prd_data[i].in_sale_icon +'</ul>';
							}else{
								shtml += '<ul id="in_sale_icon_ul_'+ res.prd_data[i].ps_idx +'"></ul>';
							}

							shtml += '</div>'
								+ '</td>'
								+ '</tr>';

						}

						shtml += '</table>'
							+ '</div>'
							+ '<div class="m-t-5 m-b-10 text-center">'
							+ '<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm prd-search-add-btn" onclick="prdGroupingAdd.prdSearchAdd();" >선택상품 추가</button>'
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

		//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		prdInstantAdd : function( obj ) {

			var prd_instant_name = $("#prd_instant_name").val();
			
			if( !$("#prd_instant_name").val() ){
				showAlert("Error", "인스턴트 상품명을 입력해주세요.", "alert2" );
				return false;
			}

			var shtml = '';
				shtml += '<ul class="add-prd" data-prdidx="Instant" >'
					+ '<input type="hidden" name="prd_idx[]" value="Instant" >'
					+ '<input type="hidden" name="ps_idx[]" value="Instant" >'
					+ '<input type="hidden" name="mode_data[]" value="" >'
					+ '<input type="hidden" name="memo[]" value="" >'
					+ '<li class="text-center" style="width:40px"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></li>'
					+ '<li class="text-center" style="width:45px">Instant</li>'
					+ '<li class="text-center" style="width:55px"></li>'
					+ '<li class="text-center" style="width:80px">'
					+ '</li>'
					+ '<li>'
					+ '<input type="text" name="prd_name[]" value="'+ prd_instant_name +'"  >'
					+ '</div>'
					+ '</li>'
					+ '<li class="" style="width:50px"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="prdGroupingAdd.prdListDel(this)" ><i class="fas fa-trash-alt"></i></button></li>'
					+ '</ul>';

			$("#prd_search_add_prd_list_table").prepend(shtml);

		},

		//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		//검색된 상품 추가하기
		prdSearchAdd : function( mode, idx ) {
			
			if( mode == "dblclick" ){
				$("#prd_search_"+ idx).prop("checked", true);
			}

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
				var _in_sale_icon_ul_html = $("#in_sale_icon_ul_"+_prd_ps_idx).html();

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
						+ '<input type="hidden" name="prd_name[]" value="'+ _prd_name +'" >'
						+ '<input type="hidden" name="mode_data[]" value="" >'
						+ '<input type="hidden" name="memo[]" value="" >'
						+ '<li class="text-center" style="width:40px"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></li>'
						+ '<li class="text-center" style="width:45px">'+ _prd_idx +'</li>'
						+ '<li class="text-center" style="width:55px"><img src="/data/comparion/'+ _prd_img +'" style="height:45px; border:1px solid #ddd;" ></li>'
						+ '<li class="text-center" style="width:80px">'
						+ '</li>'
						+ '<li>'

						+ '<div>'
						+ '<ul>'
						+ _prd_name
						+ ' <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView(\''+ _prd_idx +'\',\'info\');" >보기</button>'
						+ '</ul>'
						+ '<ul>'+ _in_sale_icon_ul_html +'</ul>'
						+ '</div>'

						+ '</li>'
						+ '<li class="" style="width:50px"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="prdGroupingAdd.prdListDel(this)" ><i class="fas fa-trash-alt"></i></button></li>'
						+ '</ul>';

				} //if( overlappingCk == "on" ){

			});

			$("#prd_search_add_prd_list_table").prepend(shtml);

			if( overlappingCkCount > 0 ){
				showAlert("Good", "총 선택된 상품 ("+ checkedCount +")개중<br>중복("+ overlappingCkCount +")을 제외한 ("+ (checkedCount - overlappingCkCount) +")상품이 추가되었습니다.<br>추가된 상품은 저장을 눌러야 최종 적용됩니다.", "alert2", "good" );
				return false;
			}

			$("#add_prd_count").html("선택된 상품 수 : <b>"+ $("#prd_search_add_prd_list_table > ul").length +"</b>");

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
						//alert("등록되었습니다.");
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
			prdGroupingAdd.prdSearch();
		}
	});


});
//--> 
</script>