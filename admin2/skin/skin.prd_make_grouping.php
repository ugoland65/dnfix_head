<?
if( $_idx ){

/*
	$data = sql_fetch_array(sql_query_error("select * from prd_grouping WHERE idx = '".$_idx."' "));
	$_prd_jsondata = json_decode($data['data'], true);
*/
}

/*
	echo "<pre>";
	print_r($_chkArray);
	echo "</pre>";
*/

?>

<style type="text/css">
ul.new-add *{ background-color:#fff5bf; }
</style>

<div class="prd-search-add-wrap">
	<ul class="left">
		
		<div>(1) 새롭게 그룹핑 생성하기</div>

		<form id="form1">
		<input type="hidden" name="a_mode" value="prdGrouping_reg" >
		<input type="hidden" name="reg_mode" value="make_prouping" >
		<input type="hidden" name="pg_mode" id="form1_pg_mode" value="<?=$_grouping_mode?>" >

		<div class="m-t-5">
			<ul>
				<select name="pg_mode" >
					<option value="sale" >데이할인</option>
					<option value="period" >기간할인</option>
					<option value="event" >기획전</option>
					<option value="qty" >수량 체크</option>
				</select>
			</ul>
			<ul class="m-t-4"><input type="text" name="pg_subject"  id="form1_pg_subject" value="" autocomplete="off" placeholder="그룹핑 이름" class="width-full"></ul>
			<ul class="m-t-4">진행일 : <div class="calendar-input" style="display:inline-block;"><input type="text" name="pg_day"  id="pg_day" value="" style="width:90px;"  autocomplete="off" ></div></ul>
		</div>	
		</form>

		<div class="m-t-5 m-b-10 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdMakeGrouping.newprdGrouping(this);" >신규 그룹핑 생성</button>
		</div>
		
		<div class="m-t-10" style="border-bottom:1px dashed #aaa;"></div>

		
		<?
			$_mode_text['sale'] = "할인";
			$_mode_text['qty'] = "수량체크";
			$_mode_text['event'] = "기획전";
		?>
		<div class="m-t-20">(2) [등록된 그룹핑]에 추가 - 진행중 ( <?=$_mode_text[$_grouping_mode]?> )</div>
		<div class="m-t-5">
			<table class="table-style border01 width-full" id="ing_prd_grouping_table">
				<?
					//$_where = "WHERE pg_mode = '".$_grouping_mode."' AND pg_state = '진행' ";
					$_where = "WHERE pg_state = '진행' ";
					$_result = sql_query_error("select * from  prd_grouping ".$_where." ORDER BY idx DESC");
					while($_list = sql_fetch_array($_result)){
				?>
				<tr>
					<td class="list-checkbox"><input type="radio" name="ing_prd_grouping_idx" id="choiceGroup_<?=$_list['idx']?>" value="<?=$_list['idx']?>" onclick="prdMakeGrouping.choiceGroup('<?=$_list['idx']?>')"></td>	
					<td class="text-center" style="width:60px"><?=$_mode_text[$_list['pg_mode']]?></td>
					<td><label for="choiceGroup_<?=$_list['idx']?>"><?=$_list['pg_subject']?></label></td>
				</tr>
				<? } ?>
			</table>
		</div>


	</ul>
	<ul class="right">
		
		<div>※ 추가된 상품은 저장을 눌러야 최종 적용됩니다.</div>
		<div class="prd-search-add-prd-list-wrap">

			<form id="form2">
			<input type="hidden" name="a_mode" value="prdGrouping_prd_inout" >
			<input type="hidden" name="idx" value="<?=$_idx?>" >

			<div id="prd_search_add_prd_list_table" class="prd-search-add-prd-list-table">

			</div>
			</form>

		</div>

		<div class="m-t-5 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdMakeGrouping.prdSave(this);" >상품추가 / 노출순서 저장</button>
		</div>

	</ul>
</div>
<?php echo json_encode($_chkArray)?>
<?
/*
	var oopJsondata = <?=$data['oop_data']?>;
*/
?>

<script type="text/javascript"> 
<!--
var prdMakeGrouping = function() {

	var new_add_shtml = '';
	var new_add_array = <?php echo json_encode($_chkArray)?>; 

	var setRow = function(row, mode) {

		var setRowHtml = '';
		
		var ul_class_name = "";
		if( mode == "new_add" ){
			ul_class_name = "new-add";
		}

		for (var i = 0; i < row.length; i++) {

			if( row[i].idx == "Instant" ){
				var this_for_ps_idx = "";
			}else{
				var this_for_ps_idx = row[i].ps_idx;
			}

			setRowHtml += '<ul class="'+ ul_class_name +'" data-prdidx="'+ row[i].idx +'">'
				+ '<input type="hidden" name="prd_idx[]" value="'+ row[i].idx +'" >'
				+ '<input type="hidden" name="ps_idx[]" value="'+ this_for_ps_idx +'" >'
				+ '<input type="hidden" name="mode_data[]" value="'+ row[i].mode_data +'" >'
				+ '<li class="text-center" style="width:40px"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></li>';
				
			if( mode == "new_add" ){
				setRowHtml += '<li class="text-center" style="width:45px"><span style="font-size:11px;">[선택]</span><br>'+ row[i].idx + ' / '+ this_for_ps_idx +'</li>';
			}else{
				setRowHtml += '<li class="text-center" style="width:45px">'+ row[i].idx + ' / '+ this_for_ps_idx +'</li>';
			}

			if( row[i].idx == "Instant" ){ 
			
				setRowHtml += '<li class="text-center" style="width:55px; height:45px; ">'
					+ '</li>'
					+ '<li class="text-center" style="width:80px">'
					+ '</li>'
					+ '<li>'
					+ '<input type="text" name="prd_name[]" value="'+ row[i].pname +'" >'
					+ '</li>';

			}else{

				setRowHtml += '<li class="text-center" style="width:55px"><img src="'+ row[i].img_path +'" style="height:45px; border:1px solid #ddd;"></li>';
				
				/*
				setRowHtml += '<li class="text-center" style="width:80px">'
					+ '<b>'+ row[i].code2 +'</b>';

				if( row[i].code3 ){
					setRowHtml += '<br>'+ row[i].code3;
				}

				setRowHtml += '</li>';
				*/
				setRowHtml += '<li class="text-center" style="width:80px; font-size:12px;">'+ row[i].brandname +'</li>';

				setRowHtml += '<li>'
					+ '<input type="hidden" name="prd_name[]" value="'+ row[i].pname +'" >'
					+ '<div>'
					+ '<ul><span class="prd-code">'+ row[i].jancode +'</span></ul>'
					+ '<ul>'
					+ row[i].pname
					+ ' <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView(\''+ row[i].idx + '\',\'info\');"">보기</button>'
					+ '</ul>';

				if( row[i].sale_price > 0 && row[i].cost_price > 0 ){
					setRowHtml += '<ul class="m-t-5" style="font-size:12px;">'+ GC.comma(row[i].sale_price) +' ( '+ GC.comma(row[i].cost_price) +' ) <b>'+ row[i].margin_per +'</b>%</ul>'
				}

				setRowHtml += '</div>'
					+ '</li>';

			}

			setRowHtml += '<li class="" style="width:50px"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="prdMakeGrouping.prdListDel(this)" ><i class="fas fa-trash-alt"></i></button></li>'
				+ '</ul>';

		} //for END

		return setRowHtml;

	};

	return {
		
		init : function() {

			//선택한 상품 정보 뽑아서 가공해 놓기
			$.ajax({
				url: "/ad/processing/prd_grouping",
				data: { "a_mode":"prdGrouping_prd_load", "load_mode":"new_add", "new_add_array":new_add_array },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						new_add_shtml = setRow( res.prd_data, "new_add" );
						$("#prd_search_add_prd_list_table").html(new_add_shtml);
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

		choiceGroup : function( idx ) {

			var shtml = new_add_shtml;
			$("#prd_search_add_prd_list_table").html("");

			$.ajax({
				url: "/ad/processing/prd_grouping",
				data: { "a_mode":"prdGrouping_prd_load", "load_mode":"saved_prd", "idx":idx, "new_add_array":new_add_array  },
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						
						/*
						for (var i = 0; i < res.prd_data.length; i++) {

							shtml += '<ul class="" data-prdidx="'+ res.prd_data[i].idx +'">'
								+ '<input type="hidden" name="prd_idx[]" value="'+ res.prd_data[i].idx +'" >'
								+ '<input type="hidden" name="ps_idx[]" value="'+ res.prd_data[i].ps_idx +'" >'
								+ '<input type="hidden" name="mode_data[]" value="'+ res.prd_data[i].mode_data +'" >'

								+ '<li class="text-center" style="width:40px"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></li>'
								+ '<li class="text-center" style="width:45px">'+ res.prd_data[i].idx + '</li>';
		
							if( res.prd_data[i].idx == "Instant" ){ 
							
								shtml += '<li class="text-center" style="width:55px; height:45px; ">'
									+ '</li>'
									+ '<li class="text-center" style="width:80px">'
									+ '</li>'
									+ '<li>'
									+ '<input type="text" name="prd_name[]" value="'+ res.prd_data[i].pname +'" >'
									+ '</li>';

							}else{

								shtml += '<li class="text-center" style="width:55px"><img src="'+ res.prd_data[i].img_path +'" style="height:45px; border:1px solid #ddd;"></li>'
									+ '<li class="text-center" style="width:80px">'
									+ '<b>'+ res.prd_data[i].code2 +'</b>';

								if( res.prd_data[i].code3 ){
									shtml += '<br>'+ res.prd_data[i].code3;
								}

								shtml += '</li>'
									+ '<li>'
									+ '<input type="hidden" name="prd_name[]" value="'+ res.prd_data[i].pname +'" >'
									+ '<div>'
									+ '<ul><span class="prd-code">'+ res.prd_data[i].jancode +'</span></ul>'
									+ '<ul>'
									+ res.prd_data[i].pname
									+ '<button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView(\''+ res.prd_data[i].idx + '\',\'info\');"">보기</button>'
									+ '</ul>'
									+ '</div>'
									+ '</li>';

							}

							shtml += '<li class="" style="width:50px"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="prdGroupingAdd.prdListDel(this)" ><i class="fas fa-trash-alt"></i></button></li>'
								+ '</ul>';

						} //for END
						*/

						shtml += setRow( res.prd_data );
						$("#prd_search_add_prd_list_table").html(shtml);

						if( res.jungbok > 0 ){
							showAlert("Notice", "선택한 상품중 기존 중복상품이 ("+ res.jungbok +")개 있어 제외했습니다.", "alert2" );
							return false;
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
					//$(obj).attr('disabled', false);
				}
			});

		},

		prdSave : function( obj ) {

			var ing_prd_grouping_idx = $('input:radio[name=ing_prd_grouping_idx]:checked').val();
			
			if( !ing_prd_grouping_idx ){
				showAlert("Error", "저장할 [등록된 그룹핑]을 선택해 주세요.", "dialog" );
				return false; 
			}

			var formData = $("#form2").serializeArray();
			formData.push({ name: "idx", value: ing_prd_grouping_idx });

			$.ajax({
				url: "/ad/processing/prd_grouping",
				data: formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						showAlert("Good!", "성공적으로 저장되었습니다.", "alert2", "good" );
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
					//$(obj).attr('disabled', false);
				}
			});

		},

		//상품 라인에서 삭제
		prdListDel : function( obj ) {
			$(obj).closest('ul').remove();
		},

		newprdGrouping : function( obj ) {

			var this_pg_subject = $("#form1_pg_subject").val();

			if( this_pg_subject == "" ){
				showAlert("Error", "그룹핑 이름을 넣어주세요.", "dialog" );
				return false; 
			}

			var formData = $("#form1").serializeArray();

			$.ajax({
				url: "/ad/processing/prd_grouping",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						
						var thisHtml = ''
							+'<tr>'
							+'<td class="list-checkbox"><input type="radio" name="ing_prd_grouping_idx" id="choiceGroup_'+ res.key +'" onclick="prdMakeGrouping.choiceGroup(\''+ res.key +'\')"></td>'
							+'<td class="text-center" style="width:50px">할인</td>'
							+'<td><label for="choiceGroup_'+ res.key +'">'+ this_pg_subject +'</label></td>'
							+'</tr>';

						$("#ing_prd_grouping_table").prepend(thisHtml);

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

prdMakeGrouping.init();

$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

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