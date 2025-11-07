<?
	// 변수 초기화
	$_prd_idx = $_prd_idx ?? "";
	$prd_data = [];
	$prd_contents = [];
	$prd_score = [];
	$_ps_grade_data = [];
	
	if( $_prd_idx ){

		$_colum = "A.*";
		$_colum .= ",B.ps_idx, B.ps_rack_code";
		$_colum .= ", C.BD_NAME";

		$_query = "select ".$_colum." from "._DB_COMPARISON." A
			left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX  ) 
			left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND_IDX  ) 
			where CD_IDX = '".$_prd_idx."' ";

		$prd_data = sql_fetch_array(sql_query_error($_query));
		if (!is_array($prd_data)) {
			$prd_data = [];
		}

		$_query = "select * from prd_contents WHERE cd_idx = '".$_prd_idx."' ";
		$prd_contents = sql_fetch_array(sql_query_error($_query));
		if (!is_array($prd_contents)) {
			$prd_contents = [];
		}

		if( !($prd_contents['pc_idx'] ?? '') ){

			$query = "insert into  prd_contents set
				cd_idx = '".$_prd_idx."',
				c19 = 'Y',
				c19_package = 'N' ";
			sql_query_error($query);

		}

		$_query = "select * from prd_score WHERE ps_pd_idx = '".$_prd_idx."' AND ps_mode = 'total' ";
		$prd_score = sql_fetch_array(sql_query_error($_query));
		if (!is_array($prd_score)) {
			$prd_score = [];
		}

		$_ps_grade_data = json_decode($prd_score['ps_grade_data'] ?? '{}', true);
		if (!is_array($_ps_grade_data)) {
			$_ps_grade_data = [];
		}
//{"ad_modify":{"before":"0","after":"9","reg":{"date":"2023-05-26 11:47:20","idx":"14","id":"admin","name":"권윤호","ip":"210.221.8.92","domain":"dgmall.wepix-hosting.co.kr"}}}
	}
?>
<style type="text/css">
.table-style{}
.table-style th{ text-align:center; }
.table-style td.none-bg{ background-color:#dddddd; border:none !important; padding: 0 !important; }
.table-style td.title{}
.table-style td h1{ display:inline-block; font-size:16px; font-weight:600; padding:5px; }
.img-upload-wrap{ }
.img-upload-wrap ul{ width:33%; text-align:center; display:inline-block; border:1px solid #ddd; padding:10px; vertical-align:top; }
</style>

<form name="onadb_prd_form" id="onadb_prd_form" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="a_mode" value="onadb_prd_modify">
<input type="hidden" name="idx" value="<?=$_prd_idx ?? ''?>">
<input type="hidden" name="ps_idx" value="<?=$prd_score['ps_idx'] ?? ''?>">

<table class="table-style ">
	<colgroup>
		<col width="150px"/>
		<col  />
	</colgroup>
	<tbody>
		<tr>
			<td colspan="2" class="none-bg" style="height:10px;"></td>
		</tr>
		<tr>
			<td colspan="2" class="none-bg title">
				<h1>사이트 (오나디비)</h1>
			</td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th>pc_idx</th>
			<td><?=$prd_contents['pc_idx'] ?? ''?></td>
		</tr>
		<tr>
			<th>사이트 옵션</th>
			<td>

				<table class="table-style border01">
				<colgroup>
					<col width="150px"/>
					<col  />
				</colgroup>
					<tr>
						<th>오나디비 노출</th>
						<td>
							<label><input type="radio" name="cd_site_show" value="Y" <? if(($prd_data['cd_site_show'] ?? '') == "Y" || !($prd_data['cd_site_show'] ?? '') ) echo "checked"; ?>> 노출</label>
							<label><input type="radio" name="cd_site_show" value="N" <? if(($prd_data['cd_site_show'] ?? '') == "N" ) echo "checked"; ?>> 비노출</label>
						</td>
					</tr>
				</table>

			</td>
		</tr>
		<tr>
			<th>분류</th>
			<td>
				<table class="table-style border01">
				<colgroup>
					<col width="150px"/>
					<col  />
				</colgroup>
					<tr>
						<th>티어분류</th>
						<td>
							<select name="cd_tier">
								<? for ($i=1; $i<6; $i++){ ?>
								<option value="<?=$i?>" <? if( !($prd_data['cd_tier'] ?? '') || ($prd_data['cd_tier'] ?? 0) == $i ) echo "selected"; ?>><?=$i?> 티어</option>
								<? } ?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>19금 상품</th>
			<td>
				<label><input type="radio" name="c19" value="Y" <? if( !($prd_contents['c19'] ?? '') || ($prd_contents['c19'] ?? '') == "Y" ) echo "checked"; ?>> 19금 상품</label>
				<label><input type="radio" name="c19" value="N" <? if( ($prd_contents['c19'] ?? '') == "N" ) echo "checked"; ?>> 전체 가능 상품</label>
			</td>
		</tr>
		<tr>
			<th>19금 패키지</th>
			<td>
				<label><input type="radio" name="c19_package" value="Y" <? if( !($prd_contents['c19_package'] ?? '') || ($prd_contents['c19_package'] ?? '') == "Y" ) echo "checked"; ?>> 19금 이미지</label>
				<label><input type="radio" name="c19_package" value="N" <? if( ($prd_contents['c19_package'] ?? '') == "N" ) echo "checked"; ?>> 노멀 이미지</label>
				<div class="admin-guide-text">
					- 패키지에 19금 이미지가 포함되어 있는 상품인 경우<br>
					- 오나디비에서는 모자이크 자동처리 ( 단 19금 대체 썸네일일 있을경우 대체 썸네일로 노출됨 )
				</div>
			</td>
		</tr>

		<tr>
			<th>개인평점</th>
			<td>
				<input type="text" name="ps_grade" value="<?=$prd_score['ps_grade'] ?? ''?>" style="width:80px;">

				<? if( isset($_ps_grade_data['ad_modify']) && is_array($_ps_grade_data['ad_modify']) ){ ?>
					관리자 수정 : <?=$_ps_grade_data['ad_modify']['before'] ?? ''?> -> <?=$_ps_grade_data['ad_modify']['after'] ?? ''?> ( <?=$_ps_grade_data['ad_modify']['reg']['date'] ?? ''?> | <?=$_ps_grade_data['ad_modify']['reg']['id'] ?? ''?> )
				<? } ?>

				<div class="admin-guide-text">
					- 사이트에 바로 노출됨
				</div>
			</td>
		</tr>
	</tbody>

</table>
</form>

<style type="text/css">
	.button-wrap-back{ height:60px; }
	.button-wrap{ width:calc(100% - 205px); height:60px; line-height:60px; text-align:center; background:rgba(0,0,0,.4); border-top:1px solid #000; position:fixed; bottom:0; right:0;  }
</style>
<div class="button-wrap-back">
</div>
<div class="button-wrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="onaDBprdForm.save()" >상품수정</button>
</div>

<script type="text/javascript"> 
<!-- 
var onaDBprdForm = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		save : function() {

			var form = $("#onadb_prd_form")[0];
			var imgData = new FormData(form);

			$.ajax({
				url: "/ad/processing/prd",
				data: imgData,
				type: "POST",
				dataType: "json",
				contentType : false,
				processData : false,
				success: function(res){
					if ( res.success == true ){
						
						alert("수정완료");
						prdInfo.mode('', 'onadb_config');

					}else{
						showAlert("Error", res.msg, "dialog" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {

				}
			});	

		}
	};

}();

$(function(){

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

});
//--> 
</script> 