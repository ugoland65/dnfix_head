<?php
/*
=================================================================================
뷰 파일: admin2/skin/skin.prd_reg_form.php
호출경로 : /ad/ajax/prd_reg_form
설명: 상품 등록 폼 화면
작성자: Lion65
수정일: 2025-03-15
=================================================================================

GET
@getParam {int} $_prd_idx - 상품 시퀀스

CONTROLLER
/application/Controllers/Admin/ProductController.php

*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\ProductController; 

$productController = new ProductController(); 

$viewData = $productController->prdRegFormIndex();

$_prd_idx = $viewData['prd_idx'];
$prd_data = $viewData['productData'];

/*
	//$_prd_idx = $_get1;
	if( $_prd_idx ){

		$_colum = "A.*";
		$_colum .= ",B.ps_idx, B.ps_rack_code, B.ps_stock_object, B.ps_alarm_count";
		$_colum .= ", C.BD_NAME";

		$_query = "select ".$_colum." from "._DB_COMPARISON." A
			left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX  ) 
			left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND_IDX  ) 
			where CD_IDX = '".$_prd_idx."' ";

		$prd_data = sql_fetch_array(sql_query_error($_query));

		$_cd_size_data = json_decode($prd_data['CD_SIZE'], true);
		$_cd_size_w = $_cd_size_data['W'];
		$_cd_size_h = $_cd_size_data['H'];
		$_cd_size_d = $_cd_size_data['D'];

		$prd_data['cd_size_fn'] = json_decode($prd_data['cd_size_fn'], true);

		$_cd_weight_data = json_decode($prd_data['cd_weight_fn'], true);
		$_cd_weight_1 = $_cd_weight_data['1'];
		$_cd_weight_2 = $_cd_weight_data['2'];
		$_cd_weight_3 = $_cd_weight_data['3'];

		$prd_data['cd_add_img'] = json_decode($prd_data['cd_add_img'], true);

	}

	$brand_query = "select BD_IDX,BD_NAME from "._DB_BRAND." order by BD_NAME asc ";
	$brand_result = sql_query_error($brand_query); 
	while($brand_list = sql_fetch_array($brand_result)){
		$_ary_brand_key[] = $brand_list['BD_IDX'];
		$_ary_brand_name[] = $brand_list['BD_NAME'];
	}
*/
?>

<?php
/*
<div style="overflow:scroll; width:800px; height:500px;">
<?
echo "<pre>";
print_r($viewData);
echo "</pre>";
?>
</div>
*/
?>

<form name='prd_form' id='prd_form' method='post' enctype="multipart/form-data" autocomplete="off">

<? if( $_prd_idx ){ ?>
	<input type="hidden" name="a_mode" value="prd_modify">
	<input type="hidden" name="idx" value="<?=$_prd_idx?>">
	<input type="hidden" name="img_name" value="<?=$prd_data['CD_IMG']?>">
	<input type="hidden" name="img_name2" value="<?=$prd_data['CD_IMG2']?>">
	<input type="hidden" name="img_add1" value="<?=isset($prd_data['cd_add_img']) && isset($prd_data['cd_add_img']['add1']) && isset($prd_data['cd_add_img']['add1']['filename']) ? $prd_data['cd_add_img']['add1']['filename'] : ''?>">
	<input type="hidden" name="img_add2" value="<?=isset($prd_data['cd_add_img']) && isset($prd_data['cd_add_img']['add2']) && isset($prd_data['cd_add_img']['add2']['filename']) ? $prd_data['cd_add_img']['add2']['filename'] : ''?>">

<? }else{ ?>
	<input type="hidden" name="a_mode" value="prd_reg">
<? } ?>

<table class="table-style ">
	<colgroup>
		<col width="150px"/>
		<col  />
	</colgroup>
	<tr>
		<td colspan="2" class="none-bg title">
			<h1>상품 기본정보</h1>
		</td>
	</tr>

	<tbody>
		<tr>
			<th>상품 구분</th>
			<td>
				<? 
				/*
				for($t=0; $t<count($koedge_prd_kind_array); $t++){ ?>
				<label><input type="radio" name="cd_kind_code" value="<?=$koedge_prd_kind_array[$t]['code']?>" <? if($prd_data['CD_KIND_CODE'] == $koedge_prd_kind_array[$t]['code'] ) echo "checked"; ?>> <?=$koedge_prd_kind_array[$t]['name']?></label>
				<? } 
				*/
				?>
				<select name="cd_kind_code">
				<? foreach($koedge_prd_kind_array as $kind){ ?>
					<option value="<?=$kind['code']?>" <? if($prd_data['CD_KIND_CODE'] == $kind['code'] ) echo "selected"; ?>><?=$kind['name']?></option>
				<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			 <th>브랜드</th>
			 <td>
				<select name="cd_brand_idx">
					<option value=''>브랜드 선택</option>
					<?
						foreach ($viewData['brandForSelect'] as $brand) {
					?>
					<option value='<?=$brand['BD_IDX']?>'<? if( $brand['BD_IDX'] == $prd_data['CD_BRAND_IDX'] ) echo "selected"; ?>><?=$brand['BD_NAME']?></option>
					<? } ?>
				</select>

				<select name="cd_brand2_idx">
					<option value=''>브랜드2 선택</option>
					<?
						foreach ($viewData['brandForSelect'] as $brand) {
					?>
						<option value='<?=$brand['BD_IDX']?>'<? if( $brand['BD_IDX'] == $prd_data['CD_BRAND2_IDX'] ) echo "selected"; ?>><?=$brand['BD_NAME']?></option>
					<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>상품명</th>
			<td><input type='text' name='cd_name'  size='40' value="<?=$prd_data['CD_NAME']?>" ></td>
		</tr>
		<tr>
			<th>원 상품명</th>
			<td><input type='text' name='cd_name_og'  size='40' value="<?=$prd_data['CD_NAME_OG']?>" ></td>
		</tr>
		<tr>
			<th>영문 상품명</th>
			<td><input type='text' name='cd_name_en'  size='40' value="<?=$prd_data['CD_NAME_EN']?>" ></td>
		</tr>
		<tr>
			<th>이미지</th>
			<td>

				<div class="img-upload-wrap">
					<ul>
						<div class="admin-guide-text">
							기본 이미지 302 x 302(px)
						</div>
						<div class="img-box">
							<?
								if( $prd_data['CD_IMG'] ){
									$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
							?>
							<div class="m-b-15">
								<img src="<?=$img_path?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
							</div>
							<? } ?>

							<input type='file' name='cd_img'><br>
							<input type='text' name='out_img'  value="" placeholder="URL로 저장"><br>
							
							<? if( $prd_data['CD_IMG'] ){ ?>
								<div class="m-t-10"><?=$prd_data['CD_IMG']?></div>
							<? } ?>

						</div>
					</ul>
					<ul>
						<div class="admin-guide-text">
							아이콘 이미지 100 x 100(px)
						</div>
						<div class="img-box">
							<?
								if( $prd_data['CD_IMG2'] ){
									$img_path2 = '/data/comparion/'.$prd_data['CD_IMG2'];
							?>
							<div class="m-b-15">
								<img src="<?=$img_path2?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
							</div>
							<? } ?>

							<input type='file' name='cd_img2'>

							<? if( $prd_data['CD_IMG2'] ){ ?>
								<div class="m-t-10"><?=$prd_data['CD_IMG2']?></div>
							<? } ?>
						</div>
					</ul>
					<ul>
						<div class="admin-guide-text">
							19금 대체 이미지
						</div>
						<div class="img-box">

							<?
								if( isset($prd_data['cd_add_img']) && isset($prd_data['cd_add_img']['add2']) && isset($prd_data['cd_add_img']['add2']['filename']) && $prd_data['cd_add_img']['add2']['filename'] ){
									$img_add2 = '/data/comparion/'.$prd_data['cd_add_img']['add2']['filename'];
							?>
							<div class="m-b-15">
								<img src="<?=$img_add2?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
							</div>
							<? } ?>

							<input type='file' name='cd_add2'>

							<? if( isset($prd_data['cd_add_img']) && isset($prd_data['cd_add_img']['add2']) && isset($prd_data['cd_add_img']['add2']['filename']) && $prd_data['cd_add_img']['add2']['filename'] ){ ?>
								<div class="m-t-10"><?=$prd_data['cd_add_img']['add2']['filename']?></div>
							<? } ?>
						</div>
					</ul>

					<ul>
						<div class="admin-guide-text">
							인보이스 이미지
						</div>
						<div class="img-box">

							<?
								if( isset($prd_data['cd_add_img']) && isset($prd_data['cd_add_img']['add1']) && isset($prd_data['cd_add_img']['add1']['filename']) && $prd_data['cd_add_img']['add1']['filename'] ){
									$img_add1 = '/data/comparion/'.$prd_data['cd_add_img']['add1']['filename'];
							?>
							<div class="m-b-15">
								<img src="<?=$img_add1?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
							</div>
							<? } ?>

							<input type='file' name='cd_add1'>

							<? if( isset($prd_data['cd_add_img']) && isset($prd_data['cd_add_img']['add1']) && isset($prd_data['cd_add_img']['add1']['filename']) && $prd_data['cd_add_img']['add1']['filename'] ){ ?>
								<div class="m-t-10"><?=$prd_data['cd_add_img']['add1']['filename']?></div>
							<? } ?>
						</div>
					</ul>
				</div>

			</td>
		</tr>
		<tr>
			<th>상품 간략설명</th>
			<td>
				<input type='text' name='cd_cont'  value="<?=$prd_data['CD_CONT']?>">
				<div class="admin-guide-text">
					- 오나디비에 노출될 상품 간략설명
				</div>
			</td>
		</tr>

		<tr>
			<td colspan="2" class="none-bg" style="height:6px;"></td>
		</tr>

		<tr>
			<th>리스트 메모</th>
			<td>
				<input type='text' name='cd_memo2'  value="<?=$prd_data['cd_memo2']?>" />
				<div class="admin-guide-text">
					- 상품목록에 노출되는 메모입니다.
				</div>
			</td>
		</tr>
		<tr>
			<th>메모</th>
			<td>
				<? /*<input type='text' name='cd_memo'  value="<?=$prd_data['CD_MEMO']?>"> */ ?>
				<textarea name="cd_memo" rows="5"><?=$prd_data['CD_MEMO']?></textarea>
				<div class="admin-guide-text">
					- 외부에 노출되지 않는 인트라넷 전용 메모
				</div>
			</td>
		</tr>

		<tr>
			<th>상품 검색어</th>
			<td>
				<input type='text' name='cd_search_term' value="<?=$prd_data['CD_SEARCH_TERM']?>" >
				<div class="admin-guide-text">
					- 인트라넷, 오나디비 검색시 가능한 추가 검색어
				</div>
			</td>
		</tr>
	</tbody>

	<tbody>
		<tr>
			<td colspan="2" class="none-bg" style="height:10px;"></td>
		</tr>
		<tr>
			<td colspan="2" class="none-bg title">
				<h1>HBTI</h1>
			</td>
		</tr>
	</tbody>

	<tbody>
		<tr>
			<th>HBTI 대상</th>
			<td>
				<label><input type="checkbox" name="hbti_target" value="N" <? if($prd_data['hbti_target'] == "Y" ) echo "checked"; ?>> 비대상</label>
				<div class="admin-guide-text">
					- 비대상 체크후 저장하면 HBTI 설정값이 초기화 되고 기존 데이터는 삭제됩니다.
				</div>
			</td>
		</tr>
		<tr>
			<th>HBTI</th>
			<td>

				<table class="table-style border01">
				<colgroup>
					<col width="250px"/>
					<col  />
				</colgroup>
					<tr>
						<th>촉감 분석 (S/H)<br>softness (부드러움 정도)</th>
						<td>
							<label><input type="radio" name="hbti_1" value="S" <? if(isset($prd_data['cd_hbti_data']) && isset($prd_data['cd_hbti_data'][0]) && $prd_data['cd_hbti_data'][0] == "S" ) echo "checked"; ?>> S (Soft)</label>
							<label><input type="radio" name="hbti_1" value="H" <? if(isset($prd_data['cd_hbti_data']) && isset($prd_data['cd_hbti_data'][0]) && $prd_data['cd_hbti_data'][0] == "H" ) echo "checked"; ?>> H (Hard)</label>
						</td>
						<td>
							<div style="font-size:11px;">
								softness >= 7 → 부드러움 선호<br>
								softness < 7 → 강한 자극 선호
							</div>
						</td>
					</tr>

					<tr>
						<th>디자인 스타일 (R/F)<br>realistic_design (현실적 디자인 여부)</th>
						<td>
							<label><input type="radio" name="hbti_2" value="R" <? if(isset($prd_data['cd_hbti_data']) && isset($prd_data['cd_hbti_data'][1]) && $prd_data['cd_hbti_data'][1] == "R" ) echo "checked"; ?>> R (Realistic)</label>
							<label><input type="radio" name="hbti_2" value="F" <? if(isset($prd_data['cd_hbti_data']) && isset($prd_data['cd_hbti_data'][1]) && $prd_data['cd_hbti_data'][1] == "F" ) echo "checked"; ?>> F (Fantasy)</label>
						</td>
						<td>
							<div style="font-size:11px;">
								realistic_design == true → 실제감 높은 제품<br>
								realistic_design == false → 판타지 스타일
							</div>
						</td>
					</tr>

					<tr>
						<th>세척 & 관리 난이도 (J/P)<br>easy_to_clean (세척 용이성)</th>
						<td>
							<label><input type="radio" name="hbti_3" value="J" <? if(isset($prd_data['cd_hbti_data']) && isset($prd_data['cd_hbti_data'][2]) && $prd_data['cd_hbti_data'][2] == "J" ) echo "checked"; ?>> J (Judging)</label>
							<label><input type="radio" name="hbti_3" value="P" <? if(isset($prd_data['cd_hbti_data']) && isset($prd_data['cd_hbti_data'][2]) && $prd_data['cd_hbti_data'][2] == "P" ) echo "checked"; ?>> P (Perceiving)</label>
						</td>
						<td>
							<div style="font-size:11px;">
								easy_to_clean == true → 세척이 쉬움<br>
								easy_to_clean == false → 세척이 어려움
							</div>
						</td>
					</tr>

					<tr>
						<th>기능성 여부 (T/E)<br>has_tech_features (기술 포함 여부)</th>
						<td>
							<label><input type="radio" name="hbti_4" value="T" <? if(isset($prd_data['cd_hbti_data']) && isset($prd_data['cd_hbti_data'][3]) && $prd_data['cd_hbti_data'][3] == "T" ) echo "checked"; ?>> T (Technical)</label>
							<label><input type="radio" name="hbti_4" value="E" <? if(isset($prd_data['cd_hbti_data']) && isset($prd_data['cd_hbti_data'][3]) && $prd_data['cd_hbti_data'][3] == "E" ) echo "checked"; ?>> E (Emotional)</label>
						</td>
						<td>
							<div style="font-size:11px;">
								has_tech_features == true → 온열, 진동, 자동 기능 포함<br>
								has_tech_features == false → 기능보다 감성적 요소가 중요
							</div>
						</td>
					</tr>

				</table>

			</td>
		</tr>
	</tbody>

	<tbody>
		<tr>
			<td colspan="2" class="none-bg" style="height:10px;"></td>
		</tr>
		<tr>
			<td colspan="2" class="none-bg title">
				<h1>고도몰</h1>
			</td>
		</tr>
	</tbody>
	
	<tbody>
		<tr>
			<th>고도몰 상품번호</th>
			<td>
				<input type='text' name='cd_godo_code' style='width:200px;' value="<?=$prd_data['cd_godo_code']?>">
				<div class="admin-guide-text">
					- 상품코드 아니고 상품번호 입니다.!!!!
				</div>
			</td>
		</tr>
	</tbody>
	
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
							<label><input type="radio" name="cd_site_show" value="Y" <? if($prd_data['cd_site_show'] == "Y" || !$prd_data['cd_site_show'] ) echo "checked"; ?>> 노출</label>
							<label><input type="radio" name="cd_site_show" value="N" <? if($prd_data['cd_site_show'] == "N" ) echo "checked"; ?>> 비노출</label>
						</td>
					</tr>
				</table>

			</td>
		</tr>
		<tr>
			<th>분류</th>
			<td>티어 정보는 오나DB 설정으로 옮겨감
			</td>
		</tr>
	</tbody>

	<tbody>
		<tr>
			<td colspan="2" class="none-bg" style="height:10px;"></td>
		</tr>
		<tr>
			<td colspan="2" class="none-bg title">
				<h1>상품 상세정보</h1>
			</td>
		</tr>
	</tbody>

	<tbody>
		<tr>
			<th>출시일</th>
			<td>
				<div class="calendar-input">
					<input type='text' name='cd_release_date'  value="<?=$prd_data['CD_RELEASE_DATE']?>" >
				</div>
			</td>
		</tr>

		<tr>
			<th>패키지 사이즈</th>
			<td>

				세로(H) : <input type='text' name='cd_size_h' value="<?=$prd_data['CD_SIZE']['H'] ?? ''?>" style="width:60px">
				가로(W) : <input type='text' name='cd_size_w' value="<?=$prd_data['CD_SIZE']['W'] ?? ''?>" style="width:60px">
				깊이(D) : <input type='text' name='cd_size_d' value="<?=$prd_data['CD_SIZE']['D'] ?? ''?>" style="width:60px">
				<div class="admin-guide-text">
					- 단위 mm (숫자만 등록할것)
				</div>

			</td>
		</tr>

		<tr>
			<th>내부길이</th>
			<td>
				<input type='text' name='cd_size2' style='width:100px;'  value="<?=$prd_data['CD_SIZE2']?>"> ( Cm )
				<div class="admin-guide-text">
					※ 젤일때는 용량( ml )
				</div>
			</td>
		</tr>

		<tr>
			<th>중량</th>
			<td>
				상품중량 : <input type='text' name='cd_weight_1' style='width:80px;'  value="<?=$prd_data['cd_weight_fn']['1'] ?? ''?>">
				전체중량 : <input type='text' name='cd_weight_2' style='width:80px;'  value="<?=$prd_data['cd_weight_fn']['2'] ?? ''?>">
				실측중량 : <input type='text' name='cd_weight_3' style='width:80px;'  value="<?=$prd_data['cd_weight_fn']['3'] ?? ''?>"> 
				<div class="admin-guide-text">
					- 단위 g (숫자만 등록할것)
				</div>
			</td>
		</tr>
		<tr>
			<th>상품 코드</th>
			<td>
				바코드 : <input type='text' name='cd_code' style='width:200px;' value="<?=$prd_data['CD_CODE']?>">
				상품 품번 : <input type='text' name='cd_code2' style='width:100px;' value="<?=$prd_data['CD_CODE2']?>">
			</td>
		</tr>
	</tbody>


	<tbody>
		<tr>
			<td colspan="2" class="none-bg" style="height:10px;"></td>
		</tr>
		<tr>
			<td colspan="2" class="none-bg title">
				<h1>재고/주문 정보</h1>
			</td>
		</tr>
	</tbody>
	<tbody>

		<tr>
			<th>주문서 메모</th>
			<td>
				<input type='text' name='cd_memo3'  value="<?=$prd_data['cd_memo3']?>" />
				<div class="admin-guide-text">
					- 주문서 폼에 노출되는 메모입니다.
				</div>
			</td>
		</tr>

		<? if( $prd_data['ps_idx'] ){ ?>
		<tr>
			<th>재고</th>
			<td>
				
				<input type="hidden" name="ps_idx" value="<?=$prd_data['ps_idx']?>">
				<table class="">
					<tr>
						<th class="text-center" style="width:100px">재고코드</th>
						<td>
							<b><?=$prd_data['ps_idx']?></b>
						</td>
						<th class="text-center" style="width:100px">랙 코드</th>
						<td>
							<input type='text' name='ps_rack_code' style='width:100px;' value="<?=$prd_data['ps_rack_code']?>">
						</td>
					</tr>
				</table>

			</td>
		</tr>
		<tr>
			<th>재고관리</th>
			<td>
				<table class="">
					<tr>
						<th class="text-center" style="width:100px">재고관리</th>
						<td>
							<label><input type="radio" name="ps_stock_object" value="Y" <? if( $prd_data['ps_stock_object'] == "Y" ) echo "checked"; ?> > 재고관리</label>&nbsp;&nbsp;
							<label><input type="radio" name="ps_stock_object" value="N" <? if( $prd_data['ps_stock_object'] == "N" ) echo "checked"; ?> > 재고관리 안함</label>
						</td>
						<th class="text-center" style="width:100px">재고알림</th>
						<td>
							<input type='text' name='ps_alarm_count' style='width:50px;' value="<?=$prd_data['ps_alarm_count']?>"> 개
						</td>
					</tr>
				</table>
				<div class="admin-guide-text">
					- 재고알림 예)3 재고가 3개 이하시 알람발생
				</div>
			</td>
		</tr>
		<? } ?>
		
		<tr>
			<th>수입국가</th>
			<td>
				<? for ($i=0; $i<count($_arr_national); $i++){ ?>
				<label><input type="radio" name="cd_national" value="<?=$_arr_national[$i]['code']?>" <? if( $prd_data['cd_national'] == $_arr_national[$i]['code'] ) echo "checked"; ?> > <?=$_arr_national[$i]['name']?>(<?=$_arr_national[$i]['code']?>)</label>&nbsp;&nbsp;
				<? } ?>
				
				<? /* 
				<input type="radio" name="cd_national" value="jp" <? if( $prd_data['cd_national'] == "jp" ) echo "checked"; ?>> 일본
				<input type="radio" name="cd_national" value="cn" <? if( $prd_data['cd_national'] == "cn" ) echo "checked"; ?>> 중국
				<input type="radio" name="cd_national" value="kr" <? if( $prd_data['cd_national'] == "kr" ) echo "checked"; ?>>  한국
				*/ ?>
			</td>
		</tr>

		<tr>
			<th>포장 사이즈</th>
			<td>
				가로(W) : <input type='text' name='invoice_size_w' value="<?=$prd_data['cd_size_fn']['invoice']['W'] ?? ''?>" style="width:60px">
				세로(H) : <input type='text' name='invoice_size_h' value="<?=$prd_data['cd_size_fn']['invoice']['H'] ?? ''?>" style="width:60px">
				깊이(D) : <input type='text' name='invoice_size_d' value="<?=$prd_data['cd_size_fn']['invoice']['D'] ?? ''?>" style="width:60px">
				&nbsp;&nbsp;
				CBM : <input type='text' name='invoice_size_cbm' value="<?=$prd_data['cd_size_fn']['invoice']['cbm'] ?? ''?>" style="width:60px">
				<input type="checkbox" name="invoice_size_cbm_mode" value="hand" <?if (($prd_data['cd_size_fn']['invoice']['cbm_mode'] ?? '') == "hand" ) echo "checked"; ?>> CBM 수동입력
				<div class="admin-guide-text">
					- 단위 mm (숫자만 등록할것)
				</div>
			</td>
		</tr>

		<tr>
			<th>인보이스 이름1 (일어)</th>
			<td><input type='text' name='cd_inv_name1' value="<?=$prd_data['CD_INV_NAME1']?>"></td>
		</tr>
		<tr>
			<th>인보이스 이름2 (영어)</th>
			<td><input type='text' name='cd_inv_name2' value="<?=$prd_data['CD_INV_NAME2']?>"></td>
		</tr>
		<tr>
			<th>인보이스 소재</th>
			<td><input type='text' name='cd_inv_material' value="<?=$prd_data['CD_INV_MATERIAL']?>" style='width:250px;'></td>
		</tr>
		<tr>
			<th>원산지</th>
			<td><input type='text' name='cd_coo' value="<?=$prd_data['CD_COO']?>" style='width:250px;'></td>
		</tr>
		<tr>
			<th>플라스틱 함유량</th>
			<td><input type='text' name='import_plastic' value="<?=$prd_data['cd_size_fn']['import']['plastic'] ?? ''?>" style='width:250px;'></td>
		</tr>
		<tr>
			<th>HS CODE</th>
			<td>
				<input type='text' name='import_hscode' value="<?=$prd_data['cd_size_fn']['import']['hscode'] ?? ''?>" style='width:250px;'><br>
				<input type='text' name='import_hscode1' value="<?=$prd_data['cd_size_fn']['import']['hscode1'] ?? ''?>" class="m-t-5" style='width:250px;'><br>
				<input type='text' name='import_hscode2' value="<?=$prd_data['cd_size_fn']['import']['hscode2'] ?? ''?>" class="m-t-5" style='width:250px;'><br>
			</td>
		</tr>

	</tbody>

	




	<? if( !$_prd_idx ){ ?>
	<tr>
		<td colspan="2" class="none-bg">
			<div class="m-t-10 text-center">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdRegForm.save()" >상품등록</button>
			</div>
		</td>
	</tr>
	<? } ?>

</table>

</form>

<? if( $_prd_idx ){ ?>
<div class="button-wrap-back">
</div>
<div class="button-wrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdRegForm.save()" >상품수정</button>
</div>
<? } ?>

<script type="text/javascript"> 
<!-- 
var prdRegForm = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		save : function() {

			var form = $('#prd_form')[0];
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
						
						if( res.a_mode == "prd_reg" ){
							onlyAD.prdView(res.idx, 'info');
							location.href='/ad/prd/prd_db';
						}else{
							location.reload();
						}

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