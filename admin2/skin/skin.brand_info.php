<?
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from "._DB_BRAND." WHERE BD_IDX = '".$_idx."' "));

}else{

}

?>
	<form name='brand_form' id='brand_form' method='post' enctype="multipart/form-data" autocomplete="off">

<? if( $_idx ){ ?>
	<input type="hidden" name="a_mode" value="brand_modify" >
	<input type="hidden" name="idx" value="<?=$_idx?>" >
	<input type="hidden" name="bd_logo" value="<?=$data['BD_LOGO']?>" >
<? }else{ ?>
	<input type="hidden" name="a_mode" value="brand_reg" >
<? } ?>

	<table class="table-style border01 width-full">
		<tr>
			<th style="width:120px">이름(국문)</th>
			<td><input type='text' name='bd_name' id='' size='40' value="<?=$data['BD_NAME']?>" ></td>
		</tr>
		<tr>
			<th>이름(영문)</th>
			<td><input type='text' name='bd_name_en' id='' size='40' value="<?=$data['BD_NAME_EN']?>" ></td>
		</tr>
		<tr>
			<th>활성</th>
			<td>
				<label><input type="radio" name='bd_active' value="N" <? if( $data['BD_ACTIVE'] == "N" ) echo "checked"; ?>> 비활성</label>
				<label><input type="radio" name='bd_active' value="Y" <? if( $data['BD_ACTIVE'] == "Y" OR $data['BD_ACTIVE'] == "" ) echo "checked"; ?>> 활성</label>
			</td>
		</tr>
		<tr>
			<th>검색 리스트 노출</th>
			<td>
				<label><input type="radio" name='bd_list_active' value="N" <? if( $data['BD_LIST_ACTIVE'] == "N" ) echo "checked"; ?>> 비활성</label>
				<label><input type="radio" name='bd_list_active' value="Y" <? if( $data['BD_LIST_ACTIVE'] == "Y" OR $data['BD_LIST_ACTIVE'] == "" ) echo "checked"; ?>> 활성</label>
			</td>
		</tr>
		<tr>
			<th>쑈당몰 노출</th>
			<td>
				<label><input type="radio" name='bd_showdang_active' value="N" <? if( !$data['bd_showdang_active'] || $data['bd_showdang_active'] == "N" ) echo "checked"; ?>> 비활성</label>
				<label><input type="radio" name='bd_showdang_active' value="Y" <? if( $data['bd_showdang_active'] == "Y" ) echo "checked"; ?>> 활성</label>
			</td>
		</tr>

		<tr>
			<th>오나디비 노출</th>
			<td>
				<label><input type="radio" name='bd_onadb_active' value="N" <? if( !$data['bd_onadb_active'] || $data['bd_onadb_active'] == "N" ) echo "checked"; ?>> 비노출</label>
				<label><input type="radio" name='bd_onadb_active' value="Y" <? if( $data['bd_onadb_active'] == "Y" ) echo "checked"; ?>> 노출</label>
			</td>
		</tr>

		<tr>
			<th>로고</th>
			<td>
				<input type="file" id="logo_file" name="logo_file" >
				<? if( $data['BD_LOGO'] ){ ?>
				<div>
				<img src="../../data/brand_logo/<?=$data['BD_LOGO']?>" alt="">
				</div>
				<? } ?>
			</td>
		</tr>
		<tr>
			<th>홈페이지</th>
			<td><input type='text' name='bd_domain' value="<?=$data['BD_DOMAIN']?>"></td>
		</tr>

		<!-- 
		<tr>
			<th>간략소개</th>
			<td><input type='text' name='bd_introduce' value="<?=$data['BD_INTRODUCE']?>"></td>
		</tr>
		<tr>
			<th>COODE</th>
			<td><input type='text' name='bd_code' value="<?=$data['BD_CODE']?>"></td>
		</tr>
		<tr>
			<th>구분코드</th>
			<td><input type='text' name='bd_kind_code' value="<?=$_bd_kind_code?>"></td>
		</tr>
		-->

	</table>

	</form>

	<div class="m-t-10 text-center">
		<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="brandInfo.save(this);" >전송</button>
	</div>

<script type="text/javascript"> 
<!-- 
var brandInfo = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		save : function(obj) {

			//$(obj).attr('disabled', true);

			//var formData = $("#brand_form").serializeArray();
			var form = $('#brand_form')[0];
			var imgData = new FormData(form);

			$.ajax({
				url: "/ad/processing/brand",
				data: imgData,
				type: "POST",
				dataType: "json",
				contentType : false,
				processData : false,
				success: function(res){
					if (res.success == true ){
						//toast2("success", "충/환전 설정", "설정이 저장되었습니다.");
						alert("등록되었습니다.");
						//location.reload();
						brandLlist.infoClose();
						brandLlist.list();
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

	};

}();

$(function(){



});
//--> 
</script> 