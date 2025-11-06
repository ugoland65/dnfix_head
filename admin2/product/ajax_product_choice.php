<?
include "../lib/inc_common.php";
?>

<STYLE TYPE="text/css">
#pd_choice_wrap{ width:100%; display:table; margin-top:15px; }
.pd-choice-wrap-tr{  display:table-cell; box-sizing:border-box;  }
.pd-choice-wrap-tr.left,
.pd-choice-wrap-tr.right{ width:430px; vertical-align:top; }
.pd-choice-wrap-tr.mid{  text-align:center; vertical-align:middle; }
.pd-choice-wrap-tr.mid button{ margin:5px 0; }

#ajax_pd_show,
#after_pd_show{ height:360px; overflow-y:scroll; margin-top:4px; box-sizing:border-box; border:1px solid #ddd; }

.pdtd-checkbox{ width:25px; text-align:center; }
.pdtd-price{ width:80px; text-align:right; }

#pd_choice_btn{ margin-top:15px; text-align:center; }
</STYLE>

<div id="popup_contents_head">
	<h1>상품 선택</h1>
</div>
<div id="popup_contents_body">

	<div class="section-title-one">
	</div>
	<div>
		<select  name='pdc_depth_1' id='pdc_depth_1' class="cate-select" onchange="ctChoice('1', this.value);">
			<option value="">분류 코드</option>
			<option value="ONAHOLE">ONAHOLE</option>
			<option value="REALDOLL">REALDOLL</option>
			<option value="WOMAN">WOMAN</option>
		</select>

	</div>
	
	<div id="pd_choice_wrap">
		<ul class="pd-choice-wrap-tr left">
			<div class="section-title-one">
				<h2>검색 상품</h2>
			</div>
			<div id="ajax_pd_show">
			</div>
		</ul>
		<ul class="pd-choice-wrap-tr mid">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="pdSelctPlus();" ><i class="fas fa-angle-double-right"></i></button>
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="pdSelctMinus();" ><i class="fas fa-angle-double-left"></i></button>
		</ul>
		<ul class="pd-choice-wrap-tr right">
			<div class="section-title-one">
				<h2>선택 상품</h2>
				<span>(<b id="pd_choice_count">0</b>)개의 상품이 선택</span>
			</div>
			<div id="after_pd_show">
				<div class="table-wrap">
					<table cellspacing="1px" cellpadding="0" border="0" class="table-style product-choice" id="after_pd_show_table">	
					</table>
				</div>
			</div>
		</ul>
	</div>
	<div id="pd_choice_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-lg" onclick="pdSelctFinal('<?=$num?>');" > 
			<i class="far fa-check-circle"></i>
			진열 상품 추가
		</button>
	</div>
</div>

<script type="text/javascript"> 
<!-- 
var finalPdKey = new Array;
var finalPdKeyCheck = new Array;

function showDepthChange(ct_id, depth_id, depth, target_id){ 
		$.ajax({
			type : "POST",
			url : "<?=_A_PATH_PRODUCT_CATE_SELECT_SHOW?>",
			data : { ct_id : ct_id, depth_id : depth_id, depth : depth },
			error : function(){
			},
			success : function(data){
				$("#"+ target_id).html(data) ;
			}
        });
}

function ctChoicePdShow(depth_id){
		$.ajax({
			type : "POST",
			url : "<?=_A_PATH_PRODUCT_CHOICE_LIST?>",
			data : { depth_id : depth_id },
			error : function(){
			},
			success : function(data){
				$("#ajax_pd_show").html(data) ;
			}
        });
}

function ctChoice(depth, depth_id, target_id){
	//alert(depth_id);
	showDepthChange("", depth_id, depth, target_id);
	ctChoicePdShow(depth_id);
}

    // 배열요소의 중복만 제거해서 배열로 반환
    function uniqArr(arr) {
        var chk = [];
        for (var i = 0; i < arr.length; i++) {
            if (chk.length == 0) {
                chk.push(arr[i]);
            } else {
                var flg = true;
                for (var j = 0; j < chk.length; j++) {
                    if (chk[j] == arr[i]) {
                        flg = false;
                        break;
                    }
                }
                if (flg) {
                    chk.push(arr[i]);
                }
            }
        }
        return chk;
    }

function pdSelctShow(){

}

function pdSelctPlus(){
	
	if( $("#ajax_pd_show input[type='checkbox']:checked").length == 0 ){
		alert("선택된 상품이 없습니다.");
		return;
	}

	$("#ajax_pd_show input[type='checkbox']:checked").each(function(){
		var ck = "ok";
		for(i=0; i<finalPdKeyCheck.length; i++){
			if( finalPdKeyCheck[i] == $(this).val() ){
				ck = "no";
				break;
			}
		}
		if( ck == "ok" ){
			finalPdKeyCheck.push($(this).val());
			var plusPdHtml = "";
			plusPdHtml += '<tr id="pd_del_tr_'+ $(this).val() +'">';
			plusPdHtml += '<td class="pdtd-checkbox"><input type="checkbox" name="del_check[]" id="del_check_'+ $(this).val() +'" value="'+ $(this).val() +'" onclick="delSelect(\''+ $(this).val() +'\');" />';
			plusPdHtml += '<td class="pdtd-name">'+$("#pass_pd_name_"+$(this).val()).html()+'</td>';
			plusPdHtml += '<td class="pdtd-price">'+$("#pass_pd_price_"+$(this).val()).html()+'</td>';
			plusPdHtml += '</tr>';
			$("#after_pd_show_table").prepend(plusPdHtml);
		}
	});

	$("#pd_choice_count").html(finalPdKeyCheck.length);
}

function delSelect(id) {
	if($("#del_check_"+id).is(":checked")==true){
		$("#pd_del_tr_"+id+ " td").css({'background':'#f6d7cc' }); 
	}else{
		$("#pd_del_tr_"+id+ " td").css({'background':'#ffffff' }); 
	}
}

function pdSelctMinus(){

	if( $("#after_pd_show input[type='checkbox']:checked").length == 0 ){
		alert("선택된 상품이 없습니다.");
		return;
	}

	$("#after_pd_show input[type='checkbox']:checked").each(function(){
		var delIndex = finalPdKeyCheck.indexOf($(this).val());
		if (delIndex > -1) {
			finalPdKeyCheck.splice(delIndex, 1);
		}
		$("#pd_del_tr_"+$(this).val()).remove();
	});

	$("#pd_choice_count").html(finalPdKeyCheck.length);
}


//--> 
</script>

<?
exit;
?>