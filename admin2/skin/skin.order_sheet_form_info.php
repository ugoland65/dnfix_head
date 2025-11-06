<?
if( $_idx ){

	$data = wepix_fetch_array(wepix_query_error("select * from ona_order_group WHERE oog_idx = '".$_idx."' "));
	$_oog_idx = $data['oog_idx'];
	$_oog_group = json_decode($data['oog_brand'], true);

}else{

}
?>
<style type="text/css">
.os-form-wrap{ display:table; width:100%; }
.os-form-wrap > ul{ display:table-cell; vertical-align:top; }
.os-form-wrap > ul.left{ width:450px; } 
.os-form-wrap > ul.right{ padding-left:15px; } 

.group-list-wrap{ width:100%; height:400px; overflow-y:scroll;  box-sizing:border-box; border:1px solid #999; }
.group-list-wrap::-webkit-scrollbar{ width:5px; background:#ccc; border-left:solid 1px rgba(255,255,255,.1)}
.group-list-wrap::-webkit-scrollbar-thumb{ background:linear-gradient(#0860d5,#2077ea);border:solid 1px #444; border-radius:3px; }

#group_list_table ul{ padding:7px 0 0 10px; font-size:12px; }

.position-move-btn{ width:22px; height:26px; line-height:26px; text-align:center; border:1px solid #eee; border-radius:5px; display:inline-block; cursor:pointer; vertical-align:middle;  }
.position-move-btn:hover{ background-color:#ff0000; color:#fff; }
</style>

<div class="os-form-wrap">
	<ul class="left">
		폼 정보
		
		<form id="form1">

		<? if( $_idx ){ ?>
		<input type="hidden" name="a_mode" value="orderSheetForm_modify" >
		<input type="hidden" name="idx" value="<?=$_oog_idx?>" >
		<? }else{ ?>
		<input type="hidden" name="a_mode" value="orderSheetForm_reg" >
		<? } ?>

		<table class="table-style border01 width-full">
			<tr>
				<th style="width:100px;">주문서폼 이름</th>
				<td>
					<input type='text' name='oog_name'  value="<?=$data['oog_name']?>" autocomplete="off" >
				</td>
			</tr>
			<tr>
				<th>수입형태</th>
				<td>
					<select name="oog_import">
						<option value="국내" <? if( $data['oog_import'] == "국내" ) echo "selected"; ?>>국내</option>
						<option value="수입" <? if( $data['oog_import'] == "수입" ) echo "selected"; ?>>수입</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>가격코드</th>
				<td>
					<input type='text' name='oog_code' id='oog_code' value="<?=$data['oog_code']?>" style="width:200px;">
					<div class="admin-guide-text">
						- 중복불가<br>
						- 영문, 영문+숫자로된 가격 고유코드 5자리정도
					</div>
				</td>
			</tr>
			<tr>
				<th>국가</th>
				<td>
					<label><input type="radio" name="oog_group" value="ko" <? if( !$data['oog_group'] || $data['oog_group'] == "ko" ) echo "checked"; ?> > 한국</label>
					<label><input type="radio" name="oog_group" value="jp" <? if( $data['oog_group'] == "jp" ) echo "checked"; ?> > 일본</label>
					<label><input type="radio" name="oog_group" value="cn" <? if( $data['oog_group'] == "cn" ) echo "checked"; ?> > 중국</label>
					<label><input type="radio" name="oog_group" value="dol" <? if( $data['oog_group'] == "dol" ) echo "checked"; ?> > 그외 달러 국가</label>
					<label><input type="radio" name="oog_group" value="etc" <? if( $data['oog_group'] == "etc" ) echo "checked"; ?> > 기타</label>
				</td>
			</tr>
			<tr>
				<th>메모</th>
				<td>
					<textarea name="memo" style="height:70px"><?=$data['memo']?></textarea>
				</td>
			</tr>
		</table>
		</form>

		<div class="m-t-10 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="orderSheetFormReg.save(this);" >전송</button>
		</div>

	</ul>
	<ul class="right <? if( !$_idx ) echo "display-none"; ?>">
		
		<div class="group-title">
			그룹 리스트 ( <b><?=count($_oog_group)?></b> )
			<input type='text' name='name[]' id="" value="<?=$_oog_group[$i]['name']?>" class="width-200 m-l-20">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="orderSheetFormReg.groupNew();" >신규 그룹생성</button>
		</div>

		<div class="group-list-wrap m-t-6">
			
			<form id="form2">
			<input type="hidden" name="a_mode" value="orderSheetForm_group" >
			<input type="hidden" name="idx" value="<?=$_oog_idx?>" >
			<input type="hidden" name="oop_code" value="<?=$data['oog_code']?>" >

			<div id="group_list_table">
			<? 
			for ($i=0; $i<count($_oog_group); $i++){

				$_oop_idx = $_oog_group[$i]['oop_idx'];
				$oop_data = sql_fetch_array(sql_query_error("select * from ona_order_prd where oop_idx = '".$_oop_idx."' "));

				$_oop_data = json_decode($oop_data['oop_data'], true);

			?>
				<ul>
					<p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p>
					<input type="text" name="name[]" value="<?=$_oog_group[$i]['name']?>" class="width-200">
					<select name="active[]">
						<option value="Y" <? if( $_oog_group[$i]['active'] == "Y" ) echo "selected";?> >활성</option>
						<option value="N" <? if( $_oog_group[$i]['active'] == "N" ) echo "selected";?> >비활성</option>
					</select>
					상품수 : <b><?=count($_oop_data)?></b> | 
					oop_idx : <b><?=$_oog_group[$i]['oop_idx']?></b>
					<button type="button" id="" class="btnstyle1 btnstyle1-sm m-r-20" onclick="orderSheetForm.groupView('<?=$_oog_group[$i]['oop_idx']?>')" >폼그룹 상품관리</button>
					<input type="hidden" name="oop_idx[]" value="<?=$_oog_group[$i]['oop_idx']?>" >
				</ul>
			<? } ?>
			</div>
			</form>

		</div>

		<div class="m-t-5 text-center">
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="orderSheetFormReg.groupSave(this, '<?=$_idx?>');" >그룹 노출순서 저장</button>
		</div>

	</ul>
</div>

<script type="text/javascript"> 
<!--
var orderSheetFormReg = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		groupNew : function(obj) {
		
			var html = '<ul>'
				+ '<p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p>'
				+ ' <input type="text" name="name[]" class="width-200">'
				+ ' <select name="active[]">'
				+ '<option value="Y" selected >활성</option>'
				+ '<option value="N" >비활성</option>'
				+ '</select>'
				+ '<input type="hidden" name="oop_idx[]" >'
				+ '</ul>';

			$("#group_list_table").prepend(html);

		},

		groupSave : function( obj, idx ) {

			$(obj).attr('disabled', true);

			var formData = $("#form2").serializeArray();

			$.ajax({
				url: "/ad/processing/order_sheet",
				data: formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "그룹 리스트", "설정이 저장되었습니다.");
						orderSheetForm.viewReset(idx);
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

	$( "#group_list_table" ).sortable({
		axis: "y",
		cursor: "move"
	});

});
//--> 
</script> 