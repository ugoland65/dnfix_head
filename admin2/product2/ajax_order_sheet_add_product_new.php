<?
include "../lib/inc_common.php";

	$_oop_idx = securityVal($oop_idx);
	$_oog_code = securityVal($oog_code);
	$_oog_arynum = securityVal($oog_arynum);
	$_oog_idx = securityVal($oog_idx);	
	$_oo_idx = securityVal($oo_idx);	


	$oog_data = wepix_fetch_array(wepix_query_error("select oog_brand from ona_order_group where oog_idx = '".$_oog_idx."' "));

	$_brand_json = '['.$oog_data[oog_brand].']';
	$_brand_json_data = json_decode($_brand_json,true);

	for ($i=0; $i<count($_brand_json_data); $i++){
		if( $i == $_oog_arynum ){
			$_show_brand_name = $_brand_json_data[$i]['name'];
		}
	}

	$oop_data = wepix_fetch_array(wepix_query_error("select * from ona_order_prd where oop_idx = '".$_oop_idx."' "));

	$_oop_json_check_data = substr($oop_data[oop_data], 0,1);
	
	if( $_oop_json_check_data == "[" ){
		$_oop_json = $oop_data[oop_data];
	}else{
		$_oop_json = '['.$oop_data[oop_data].']';
	}

	$_oop_jsondata = json_decode($_oop_json,true);

	$brand_query = "select BD_IDX,BD_NAME from "._DB_BRAND." order by BD_NAME DESC";
	$brand_result = wepix_query_error($brand_query); 
	while($brand_list = wepix_fetch_array($brand_result)){
		$_ary_brand_key[] = $brand_list[BD_IDX];
		$_ary_brand_name[] = $brand_list[BD_NAME];
	}

?>

<style type="text/css">
.add-order-sheet-goofs-wrap{ width:100%; height:100%; display:table; box-sizing:border-box; }
.add-order-sheet-goofs-wrap .left{ vertical-align:top; width:300px; display:table-cell; border-right:1px solid #ddd;  padding:20px 10px 10px; box-sizing:border-box;}
.add-order-sheet-goofs-wrap .right{ vertical-align:top; display:table-cell; padding:20px; box-sizing:border-box;  }
.oop-prd-list{ width:100%; height:600px; overflow-y:scroll; border:2px solid #333; }
</style>

<div class="add-order-sheet-goofs-wrap">
	<div class="left">

		( <?=$_oog_code?> | <?=$_oop_idx?> | <?=$_oog_arynum?> | <?=$_oog_idx?> )<br>

		<div class="m-t-3"><b style="font-size:14px">그룹관리</b></div>		
		<div class="m-t-5">
			그룹명 : <input type='text' name='group_name' id='group_name' value="<?=$_show_brand_name?>" style='width:150px;'>
		</div>
		<div class="m-t-5">
			그룹 IDX : <input type='text' name='group_idx' id='group_idx' value="<?=$_oop_idx?>" style='width:150px;'>
		</div>
<!-- 
		<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm m-t-5" onclick="groupModify()" > 
			그룹정보 변경
		</button>
 -->

		<div class="m-t-30"><b style="font-size:14px">상품 IDX로 등록</b> | <?=$_oog_code?> | <?=$_oop_idx?></div>
		<div class="m-t-5">
			상품 IDX : <input type='text' name='add_idx' id='add_idx' value="" style='width:150px;'>
		</div>
		<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm m-t-5" onclick="ohAddIdxPd()" > 
			상품 IDX 추가
		</button>


		<form id="oh_add_before_req_pd_form" method="post">
		<input type="hidden" name="a_mode" value="ohAddBeforeReqPd">
		<input type="hidden" name="oog_code" value="<?=$_oog_code?>">

		<div class="m-t-30"><b style="font-size:14px">상품 등록후 추가</b> | <?=$_oog_code?> | <?=$_oop_idx?></div>
		<div class="m-t-5">
			<select name="cd_kind_code" id="cd_kind_code">
			<? for($t=0; $t<count($koedge_prd_kind_array); $t++){ ?>
				<option value="<?=$koedge_prd_kind_array[$t]['code']?>"><?=$koedge_prd_kind_array[$t]['name']?></option>
			<? } ?>
			</select>
		</div>

		<div class="m-t-5">
			<select name='cl_brand' style="width:120px;">
				<option value=''>Select Brand</option>
				<?
				for ($i=0; $i<count($_ary_brand_name); $i++){
				?>
				<option value='<?=$_ary_brand_key[$i]?>'<? if( $_ary_brand_key[$i] == $comparison_data[CD_BRAND_IDX] ) echo "selected"; ?>><?=$_ary_brand_name[$i]?></option>
				<? } ?>
			</select>

			<select name='cl_brand2' style="width:120px;">
				<option value=''>Select Brand</option>
				<?
				for ($i=0; $i<count($_ary_brand_name); $i++){
				?>
				<option value='<?=$_ary_brand_key[$i]?>'<? if( $_ary_brand_key[$i] == $comparison_data[CD_BRAND2_IDX] ) echo "selected"; ?>><?=$_ary_brand_name[$i]?></option>
				<? } ?>
			</select>
		</div>

		<div class="m-t-5"><input type='text' name='cd_name'  id='cd_name' size='40' value="" placeholder="상품명"></div>
		<div class="m-t-5"><input type='text' name='cd_name_og'  size='40' value="" placeholder="상품명 영문" ></div>
		<div class="m-t-5">전체중량 : <input type='text' name='cd_weight2' id='cd_weight2' style='width:100px;'  value="<?=$comparison_data[CD_WEIGHT2]?>"> ( g ) </div>
		<div class="m-t-5">
			JAN 코드 : <input type='text' name='cd_code' id='cd_code' value="" style='width:150px;'>
		</div>
		<div class="m-t-5">
			코드 : <input type='text' name='cd_code_npg' id='cd_code_npg' value="" style='width:100px;' placeholder="코드">
		</div>
		<div class="m-t-5">
			<select name="cd_price_mode">
				<option value="TH">TH</option>
				<option value="TIS">TIS</option>
				<option value="NPG">NPG</option>
				<option value="A">브랜드 A</option>
				<option value="B">브랜드 B</option>
				<option value="NLS">NLS</option>
				<option value="ETC1">기타1</option>
			</select>
			<input type='text' name='cd_supply_price' id='cd_supply_price' style='width:70px;' value="<?=number_format($comparison_data[CD_SUPPLY_PRICE_2])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);">엔
		</div>
		</form>
		<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm m-t-5" onclick="ohAddBeforeReqPd()" > 
			상품 등록후 추가
		</button>

	</div>
	<div class="right">

<div class="oop-prd-list">
<form id="form_table_checked" method="post">
<input type="hidden" name="a_mode" value="orderSheetProductModifyNew">
<input type="hidden" name="oop_idx" value="<?=$_oop_idx?>">
<table class="table-list" id="oop_prd_list">
<?
// for z
for ($z=0; $z<count($_oop_jsondata); $z++){

	$_idx = $_oop_jsondata[$z]['idx'];
	$_ps_idx = $_oop_jsondata[$z]['stockidx'];
	$_code = $_oop_jsondata[$z]['code'];
	$_jan = $_oop_jsondata[$z]['jan'];
	$_kind = $_oop_jsondata[$z]['kind'];
	$_pname = $_oop_jsondata[$z]['pname'];
	$_om = $_oop_jsondata[$z]['om'];
	$_price = $_oop_jsondata[$z]['price']*1;
	$_show_price = number_format($_price,1);
	$_weight = $_oop_jsondata[$z]['weight'];
	$_state = $_oop_jsondata[$z]['state'];

	if(($z%2) == 0){
		$_tr_color = "#ffffff";
	}else{
		$_tr_color = "#eee";
	}
?>
<tr id="tr_<?=$_idx?>" bgcolor="<?=$_tr_color?>">
	<td width="25px"><input type="radio" name="chk" id="radio_<?=$_idx?>" value="<?=$_idx?>" onclick="chkSelect(this,'<?=$_idx?>')" /></td>
	<td style="width:50px" class="text-left p-5">
		<input type='text' name='idx[]' id="code2_<?=$_idx?>" value="<?=$_idx?>"><br>
		<input type='text' name='ps_idx[]' id="code2_<?=$_idx?>" value="<?=$_ps_idx?>" class="m-t-5">
	</td>
	<td style="width:50px"><?=$_state?>
		<select name="state[]" id="state_<?=$_idx?>">
			<option value="on" <? if( $_state == "on" ) echo "selected"; ?> >판매</option>
			<option value="out" <? if( $_state == "out" ) echo "selected"; ?>>단종</option>
			<option value="off" <? if( $_state == "off" ) echo "selected"; ?>>감춤</option>
		</select>
	</td>
	<td style="width:75px"><input type='text' name='code[]' id="code2_<?=$_idx?>" value="<?=$_code?>"></td>
	<td style="width:110px"><input type='text' name='jan[]' id="code2_<?=$_idx?>" value="<?=$_jan?>"></td>
	<td style="width:60px"><input type='text' name='kind[]' id="code2_<?=$_idx?>" value="<?=$_kind?>"></td>
	<td class="text-left p-5">
		<input type='text' name='pname[]' id="code2_<?=$_idx?>" value="<?=$_pname?>" class="m-t-5"><br>
		<input type='text' name='ordermemo[]' id="code2_<?=$_idx?>" value="<?=$_om?>" class="m-t-2 m-b-5">
	</td>
	<td style="width:60px"><input type='text' name='price[]' id="code2_<?=$_idx?>" value="<?=$_show_price?>"></td>
	<td style="width:60px"><input type='text' name='weight[]' id="code2_<?=$_idx?>" value="<?=$_weight?>"></td>
	<td style="width:60px">
	<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="oopPrdDel('<?=$_idx?>')"> 삭제</button>
	</td>
</tr>
<? } ?>
</table>
</form>
</div>

<div class="m-t-10">
	<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveTop()" style="width:100px" > 
		<i class="fas fa-chevron-circle-up"></i> 맨위로
	</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveUp()" style="width:100px" > 
		<i class="fas fa-chevron-circle-up"></i> 위로
	</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveDown()" style="width:100px" > 
		<i class="fas fa-chevron-circle-down"></i> 아래
	</button>
	<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm " onclick="moveBottom()" style="width:100px" > 
		<i class="fas fa-chevron-circle-down"></i> 맨아래
	</button>

	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm m-l-20" onclick="saveOop()" style="width:100px" > 
		저장
	</button>
</div>

	</div>
</div>

<script type="text/javascript"> 
<!-- 

var trid = "";

function ohAddIdxPd(){

	var add_idx = $('#add_idx').val();

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data: { 
			"a_mode":"ohAddIdxPd",
			"oog_code":"<?=$_oog_code?>",
			"add_idx":add_idx
		},
		success: function(getdata) {

			makedata = getdata.split('|');
			ckcode = makedata[1];

			ps_idx = makedata[3];
			cd_code_npg = makedata[4];
			cd_code = makedata[5];
			kind = makedata[6];
			cd_name = makedata[7];
			cd_supply_price = makedata[8];
			cd_weight2 = makedata[9];

			if(ckcode=="Processing_Complete"){
				
				var add_html = ''
				+'<tr id="tr_'+ add_idx +'">'
				+ '<td width="25px"><input type="radio" name="chk" id="radio_'+ add_idx +'" value="'+ add_idx +'" onclick="chkSelect(this,\''+ add_idx +'\')" /></td>'
				+ '<td style="width:50px"><input type="text" name="idx[]" value="'+ add_idx +'"><br><input type="text" name="ps_idx[]" value="'+ ps_idx +'"></td>'
				+ '<td style="width:50px">'
				+ '<select name="state[]" id="state_'+ add_idx +'">'
				+ '<option value="on" selected >판매</option>'
				+ '<option value="out" >단종</option>'
				+ '<option value="off" >감춤</option>'
				+ '</select>'
				+ '</td></td>'
				+ '<td style="width:75px"><input type="text" name="code[]" value="'+ cd_code_npg +'"></td>'
				+ '<td style="width:110px"><input type="text" name="jan[]" value="'+ cd_code +'"></td>'
				+ '<td style="width:60px"><input type="text" name="kind[]" value="'+ kind +'"></td>'
				+ '<td class="text-left">'
				+ '<input type="text" name="pname[]" value="'+ cd_name +'" class="m-t-5"><br>'
				+ '<input type="text" name="ordermemo[]" class="m-t-2 m-b-5">'
				+ '</td>'
				+ '<td style="width:60px"><input type="text" name="price[]" value="'+ cd_supply_price +'"></td>'
				+ '<td style="width:60px"><input type="text" name="weight[]" value="'+ cd_weight2 +'"></td>'
				+ '</tr>';

				$('#oop_prd_list').prepend(add_html);

			}else if(ckcode=="Value_null"){

			}
		}
	});

}


function ohAddBeforeReqPd(){

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data: $('#oh_add_before_req_pd_form').serialize(),
		success: function(getdata) {
			makedata = getdata.split('|');
			ckcode = makedata[1];
			key = makedata[3];
			if(ckcode=="Processing_Complete"){
				
				var kind = $("#cd_kind_code option:selected").text();
				var cd_code_npg = $("#cd_code_npg").val();
				var cd_code = $("#cd_code").val();
				var cd_name = $("#cd_name").val();
				var cd_weight2 = $("#cd_weight2").val();
				var cd_supply_price = $("#cd_supply_price").val();

				//alert("aaa");
				var add_html = ''
				+'<tr id="tr_'+ key +'">'
				+ '<td width="25px"><input type="radio" name="chk" id="radio_'+ key +'" value="'+ key +'" onclick="chkSelect(this,\''+ key +'\')" /></td>'
				+ '<td style="width:50px"><input type="text" name="idx[]" value="'+ key +'"></td>'
				+ '<td style="width:50px"></td>'
				+ '<td style="width:75px"><input type="text" name="code[]" value="'+ cd_code_npg +'"></td>'
				+ '<td style="width:110px"><input type="text" name="jan[]" value="'+ cd_code +'"></td>'
				+ '<td style="width:60px"><input type="text" name="kind[]" value="'+ kind +'"></td>'
				+ '<td class="text-left"><input type="text" name="pname[]" value="'+ cd_name +'"></td>'
				+ '<td style="width:60px"><input type="text" name="price[]" value="'+ cd_supply_price +'"></td>'
				+ '<td style="width:60px"><input type="text" name="weight[]" value="'+ cd_weight2 +'"></td>'
				+ '</tr>';

				$('#oop_prd_list').prepend(add_html);

			}else if(ckcode=="Value_null"){

			}
		}
	});

}


function chkSelect(obj, id){
	$(".oop-prd-list tr").each(function(){
		$(this).css("background-color", "#ffffff");
	});
	trid = id;
	$(obj).closest('tr').css("background-color", "#ff9797");
}


function moveUp(){
	var idStr = "#tr_"+trid;
	var $tr = $(idStr);
    $tr.prev().before($tr); 
}

function moveDown(){
	var idStr = "#tr_"+trid;
	var $tr = $(idStr);
	$tr.next().after($tr);
}

function moveTop(){
	var idStr = "#tr_"+trid;
	var $tr = $(idStr);
	$tr.closest('table').find('tr:first').before($tr);
}

function moveBottom(){
	var idStr = "#tr_"+trid;
	var $tr = $(idStr);
	$tr.closest('table').find('tr:last').after($tr);
}


function saveOop(){

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data: $('#form_table_checked').serialize(),
		success: function(res) {

			if(res.success === true) {
				//window.location.reload();
				location.href='order_sheet_test3.php?oo_idx=<?=$_oo_idx?>&cate_num=<?=$_oop_idx?>';
			}
/*
			makedata = getdata.split('|');
			ckcode = makedata[1];
			msg = makedata[3];
			if(ckcode=="Processing_Complete"){
				//$('.oop-prd-list').html(msg);
				window.location.reload();
			}else if(ckcode=="Value_null"){

			}
*/
		}
	});

}


function oopPrdDel(key){
	$("#tr_"+key).remove();
}

function groupModify(){

	var group_name = $('#group_name').val();
	var group_idx = $('#group_idx').val();

	$.ajax({
		type: "post",
		url : "processing.order_sheet.php",
		data: { 
			"a_mode":"ohGroupModify",
			"oog_idx":"<?=$_oog_idx?>",
			"oog_arynum":"<?=$_oog_arynum?>",
			"group_name":group_name,
			"group_idx":group_idx
		},
		success: function(res) {
			if(res.success === true) {
				//alert(res.msg);
				window.location.reload();
			}
		}
	});

}
//--> 
</script> 