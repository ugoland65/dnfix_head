<?
include "../lib/inc_common.php";

	$_oog_idx = securityVal($oog_idx);	

	$oog_data = wepix_fetch_array(wepix_query_error("select oog_brand from ona_order_group where oog_idx = '".$_oog_idx."' "));

	$_brand_json = '['.$oog_data[oog_brand].']';
	$_brand_json_data = json_decode($_brand_json,true);

?>

<style type="text/css">
.add-order-sheet-goofs-wrap{ width:100%; height:100%; display:table; box-sizing:border-box; }
.add-order-sheet-goofs-wrap .left{ vertical-align:top; width:300px; display:table-cell; border-right:1px solid #ddd;  padding:20px 10px 10px; box-sizing:border-box;}
.add-order-sheet-goofs-wrap .right{ vertical-align:top; display:table-cell; padding:20px; box-sizing:border-box;  }
.oop-prd-list{ width:100%; height:600px; overflow-y:scroll; border:2px solid #333; }
</style>

<div class="add-order-sheet-goofs-wrap">
	<div class="left">

		<div class="m-t-5"><b style="font-size:14px">그룹추가</b></div>
		<div class="m-t-5"><input type='text' name='brand_idx'  id='brand_idx' size='40' value="" placeholder="브랜드 IDX"></div>
		<div class="m-t-5"><input type='text' name='group_name'  id='group_name' size='40' value="" placeholder="그룹명"></div>
		<div class="m-t-5"><input type='text' name='oop_idx'  id='oop_idx' size='40' value="" placeholder="상품그룹 IDX"></div>
		<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm m-t-5" onclick="ohAddGroup()" > 
			그룹 추가
		</button>

	</div>
	<div class="right">

<div class="oop-prd-list">

<form id="form_table_checked" method="post">
<input type="hidden" name="a_mode" value="orderSheetGroupModify">
<input type="hidden" name="oog_idx" value="<?=$_oog_idx?>">

<table class="table-list" id="oop_prd_list">

<? for ($i=0; $i<count($_brand_json_data); $i++){ ?>
<tr id="tr_<?=$i?>">
	<td width="25px"><input type="radio" name="chk" id="radio_<?=$i?>" value="<?=$i?>" onclick="chkSelect(this,'<?=$i?>')" /></td>
	<td>브랜드 IDX : <input type='text' name='brand[]' id="" value="<?=$_brand_json_data[$i]['brand']?>" style="width:60px"></td>
	<td>그룹명 : <input type='text' name='name[]' id="" value="<?=$_brand_json_data[$i]['name']?>" style="width:200px"></td>
	<td>상품그룹 IDX : <input type='text' name='oop_idx[]' id="" value="<?=$_brand_json_data[$i]['oop_idx']?>" style="width:60px"></td>
	<td>
		<select name="active[]">
			<option value="Y" <? if( $_brand_json_data[$i]['active'] == "Y" ) echo "selected";?> >활성</option>
			<option value="N" <? if( $_brand_json_data[$i]['active'] == "N" ) echo "selected";?> >비활성</option>
		</select>
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
	<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveBottom()" style="width:100px" > 
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
var inst_num = 100;

function ohAddGroup(){

	inst_num ++;

	var brand_idx = $("#brand_idx").val();
	var group_name = $("#group_name").val();
	var oop_idx = $("#oop_idx").val();

	if( oop_idx == "" ){
		if (!confirm("상품그룹 IDX가 없을경우 상품그룹 IDX를 새로 생성함")) {
			return false;
		} else {
			$.ajax({
				type: "post",
				url : "processing.order_sheet.php",
				async:false,
				data: {  
					"a_mode":"ohPdGroupMake",
					"group_name":group_name
				},
				success: function(res) {
					if(res.success === true) {
						oop_idx = res.return_key;
					}
				}
			});
		}
	}

	if( oop_idx ){ 

		var add_html = ''
		+ '<tr id="tr_'+ inst_num +'">'
		+ '<td width="25px"><input type="radio" name="chk" id="radio_'+ inst_num +'" value="'+ inst_num +'" onclick="chkSelect(this,\''+ inst_num +'\')" /></td>'
		+ '<td>브랜드 IDX : <input type="text" name="brand[]" value="'+ brand_idx +'" style="width:60px"></td>'
		+ '<td>그룹명 : <input type="text" name="name[]" id=">" value="'+ group_name +'" style="width:200px"></td>'
		+ '<td>상품그룹 IDX : <input type="text" name="oop_idx[]" value="'+ oop_idx +'" style="width:60px"></td>'
		+ '<td>'
		+ '<select name="active[]">'
		+ '<option value="Y">활성</option>'
		+ '<option value="N">비활성</option>'
		+ '</select>'
		+ '</td>'
		+ '</tr>';

		$('#oop_prd_list').prepend(add_html);

	}
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
				//alert(res.msg);
				window.location.reload();
			}
		}
	});

}
//--> 
</script> 