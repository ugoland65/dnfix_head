<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\RequestHandler;
use App\Controllers\Admin\ProductController;

$productController = new ProductController(); 

$viewData = $productController->prdProviderIndex();

$requestHandler = new RequestHandler();
$requestData = $requestHandler->getAll();
?>
<style>
	.prd-name{
		width:200px;
		white-space:normal !important;
		word-wrap:break-word;
		word-break:break-all;
	}
</style>
<div id="contents_head">
	<h1>공급사 상품관리</h1>
	
	<? /*
	<div class="head-btn-wrap m-l-10">
		<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdDB.makeGrouping('');" >선택상품 그룹핑</button>
	</div>

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='/ad/prd/prd_reg'" > 
			<i class="fas fa-plus-circle"></i>
			신규상품 등록
		</button>
	</div>
	*/ ?>

	<? /*
	<div class="head-right-fixed-wrap">

		<div>
			<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
		</div>
		<div class="m-t-7">
			<ul class="m-t-5">
				<input type='text' name='s_text' id='s_text' size='20' value="<?=$_s_text ?? '' ?>" placeholder="검색어" >
			</ul>
			<ul class="m-t-5">
				<select name="s_brand" id="s_brand" >
					<option value="">전체 브랜드</option>
					<?
					foreach( $viewData['brandForSelect'] as $brand ){
					?>
					<option value="<?=$brand['BD_IDX']?>" <? if( $brand['BD_IDX'] == ($_s_brand ?? '') ) echo "selected";?> ><?=$brand['BD_NAME']?></option>
					<? } ?>
				</select>
			</ul>
			<ul class="m-t-5">
				<select name="s_kind_code" id="s_kind_code" >
					<option value="">전체 종류</option>
					<?
					for($t=0; $t<count($koedge_prd_kind_array); $t++){
					?>
					<option value="<?=$koedge_prd_kind_array[$t]['code']?>" <? if( $s_kind_code == $koedge_prd_kind_array[$t]['code'] ) echo "selected";?>><?=$koedge_prd_kind_array[$t]['name']?></option>
					<? } ?>
				</select>
				<select name="s_tier" id="s_tier">
					<option value="">티어</option>
					<? for ($i=1; $i<6; $i++){ ?>
					<option value="<?=$i?>"><?=$i?> 티어</option>
					<? } ?>
				</select>
			</ul>
			<ul class="m-t-5">
				

				<select name="s_national" id="s_national" >
					<option value="">수입국</option>
					<?
					for ($i=0; $i<count($_arr_national); $i++){
					?>
					<option value="<?=$_arr_national[$i]['code']?>" ><?=$_arr_national[$i]['name']?></option>
					<? } ?>
				</select>
			</ul>
			<ul class="m-t-5">
				SORT : 
				<select name="sort_kind" id="sort_kind" >
					<option value="stock" <? if( $_sort_kind == "stock" ) echo "selected";?>>재고 많은순</option>
					<option value="stock_asc" <? if( $_sort_kind == "stock_asc" ) echo "selected";?>>재고 적은순</option>
					<option value="idx" <? if( $_sort_kind == "idx" ) echo "selected";?> >상품 등록순</option>
					<option value="rack_code" <? if( $_sort_kind == "rack_code" ) echo "selected";?> >랙코드순</option>
					<option value="soldout" <? if( $_sort_kind == "soldout" ) echo "selected";?> >품절일 최근순</option>
				</select>
			</ul>
			<ul class="m-t-15">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="prdDB.list();" > 
					<i class="fas fa-search"></i> 검색
				</button>
			</ul>
		</div>

	</div>
	*/ ?>

</div>
<div id="contents_body">
	<div id="contents_body_wrap" >
		<div id="list_new_wrap">
	
			<div class="table-top">
				<ul class="total">
					Total : <span><b><?=number_format($viewData['pagination']['total'])?></b></span> &nbsp; | &nbsp;
					<span><b><?=$viewData['pagination']['current_page']?></b></span> / <?=$viewData['pagination']['last_page']?> page
				</ul>
				<ul>
					<select name="s_partner" id="s_partner" >
						<option value="">전체 공급사</option>
						<?
						foreach( $viewData['partnerForSelect'] as $partner ){
						?>
						<option value="<?=$partner['idx']?>" <? if( $partner['idx'] == ($_s_partner ?? '') ) echo "selected";?> ><?=$partner['name']?></option>
						<? } ?>
					</select>
				</ul>
				<ul>
					<select name="s_godo_match" id="s_godo_match" >
						<option value="">고도몰 매칭</option>
						<option value="matched" <? if( $requestData['s_godo_match'] == 'matched' ) echo "selected";?>>매칭완료</option>
						<option value="unmatched" <? if( $requestData['s_godo_match'] == 'unmatched' ) echo "selected";?>>매칭안됨</option>
					</select>
				</ul>
				<ul>
					<select name="s_supplier_match" id="s_supplier_match" >
						<option value="">공급사 매칭</option>
						<option value="matched" <? if( $requestData['s_supplier_match'] == 'matched' ) echo "selected";?>>매칭완료</option>
						<option value="unmatched" <? if( $requestData['s_supplier_match'] == 'unmatched' ) echo "selected";?>>매칭안됨</option>
					</select>
				</ul>
				<ul>
					<input type="text" name="s_keyword" id="s_keyword" placeholder="상품명 검색" value="<?=$requestData['s_keyword'] ?? ''?>">
				</ul>
				<ul>
					<button type="button" id="searchBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm"  > 
						<i class="fas fa-search"></i> 검색
					</button>
				</ul>
			</div>

			<div class="table-wrap5 m-t-5">
				<div class="scroll-wrap">

					<table class="table-st1">
						<thead>
						<tr class="list">
							<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
							<th class="list-idx">고유번호</th>
							<th class="">상태</th>
							<th class="" style="width:80px;">이미지</th>
							<th class="">분류</th>
							<th class="" >이름</th>
							
							<th class="">브랜드</th>
							<th class="">공급사</th>
							<th class="">코드</th>
							<th class="">고도몰<br>상품코드</th>
							<th class="">고도몰<br>판매가</th>
							<th class="">상품원가<br>/주문가</th>
							<th class="">마진</th>
							<th class="">마진율</th>
							
							<th class="">공급사<br>이미지</th>
							<th class="" >공급사<br>상품명</th>
							<th class="">공급매칭</th>
							<th class="">공급<br>상품코드</th>
							<th class="">공급<br>사이트</th>
							<th class="">공급 2차</th>
							<th class="">매칭취소</th>
						</tr>
						</thead>
						<?php foreach ($viewData['productPartnerList'] as $item) { ?>
							<tr>
								<td><input type="checkbox" name="check_idx[]" value="<?=$item['idx']?>"></td>
								<td class="text-center"><?=$item['idx']?></td>
								<td class="text-center"><?=$item['status']?></td>
								<td >
									<img src="<?=$item['img_src']?>" style="height:70px; border:1px solid #eee !important;">
								</td>
								<td class="text-center"><?=$koedge_prd_kind_name[$item['kind']] ?? "미지정"?></td>
								<td class="prd-name"><a href="javascript:prdProviderQuick(<?=$item['idx']?>);"><?=$item['name']?></a></td>
								
								<td class="text-center"><?=$item['brand_name']?></td>
								<td class="text-center"><a href="/ad/prd/prd_provider/?s_partner=<?=$item['partner_idx']?>"><?=$item['partner_name']?></a></td>
								<td class="text-center"><?=$item['code']?></td>
								<td class="text-center">
									<button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
										onclick="goGodoMall(<?=$item['godo_goodsNo']?>);" >#<?=$item['godo_goodsNo']?></button>
								</td>
								<td class="text-right"><?=number_format($item['sale_price'])?></td>
								<td class="text-right">
									<?=number_format($item['cost_price'])?>
									<br><b><?=number_format($item['order_price'])?></b>
								</td>
								<td class="text-right">
									<?php 
									$salePrice = (float)($item['sale_price'] ?? 0);
									$costPrice = (float)($item['cost_price'] ?? 0);
									$orderPrice = (float)($item['order_price'] ?? 0);
									$margin = $salePrice - $costPrice;
									$margin2 = $salePrice - $orderPrice;
									
									if ($salePrice > 0 && $costPrice > 0) {
										echo number_format($margin);
									} else {
										echo '-';
									}
									if ($salePrice > 0 && $orderPrice > 0) {
										echo "<br><b>".number_format($margin2)."</b>";
									} else {
										echo '-';
									}
									?>
								</td>
								<td class="text-right">
									<?php 
									if ($salePrice > 0 && $costPrice > 0) {
										$marginRate = ($margin / $salePrice) * 100;
										echo number_format($marginRate, 1) . '%';
									} else {
										echo '-';
									}
									?>
									<?php 
									if ($salePrice > 0 && $orderPrice > 0) {
										$marginRate2 = ($margin2 / $salePrice) * 100;
										echo "<br><b>".number_format($marginRate2, 1) . '%</b>';
									} else {
										echo '-';
									}
									?>
								</td>
								
								<td class="text-center">
									<?php 
									if( !empty($item['supplier_img_src'])  ){
									?>
									<img src="<?=$item['supplier_img_src']?>" style="height:70px; border:1px solid #eee !important;">
									<?php }else{ ?>
										-
									<?php } ?>
								</td>

								<td class="prd-name text-left">
									<a href="javascript:goSupplierProductEdit('<?=$item['supplier_prd_idx']?>');"><?=$item['name_p'] ?? '-'?></a>
									<?php
										if( !empty($item['matching_option']) ){
									?>
									<br>( 옵션 : <?=$item['matching_option'] ?? '-'?>)
									<?php
										}
									?>
								</td>

								<!-- 공급매칭 -->
								<td class="text-center">
									<?php 
									if( !empty($item['supplier_prd_idx'])  ){
									?>
										<?=$item['supplier_prd_idx']?>
									<?php }else{ ?>
										비매칭
									<?php } ?>
								</td>

								<td class="text-center">
									<?php 
									if( !empty($item['supplier_prd_idx'])  ){
									?>
                                	<button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
                                    	onclick="goSupplierProduct('<?=$item['supplier_site']?>', '<?=$item['supplier_prd_pk']?>');" >#<?=$item['supplier_prd_pk']?></button>
									<?php }else{ ?>
										-
									<?php } ?>
								</td>
								<td class="text-center"><?=$item['supplier_site'] ?? '-' ?></td>
								<td class="text-center"><?=$item['supplier_2nd_name'] ?? '-'?></td>
								<td class="text-center">
									<?php 
										if( !empty($item['supplier_prd_idx'])  ){
									?>
									<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs cancel-match-btn" 
										data-db1-idx="<?=$item['idx']?>" 
										data-db2-idx="<?=$item['supplier_prd_idx']?>"
									>매칭취소</button>
									<?php }else{ ?>
										-
									<?php } ?>
								</td>
                            </td>
							</tr>
						<?php } ?>
					</table>

				</div>
			</div>

		</div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap"><?=$viewData['paginationHtml']?></div>
</div>
<script type="text/javascript">
<!--
const prdProvider = (function(){

	const API_ENDPOINT = {
		cancelMatchProviderProduct: '/router/cancelMatchProviderProduct/',
	};

	/**
	 * 공급사 상품 매칭 취소
	 * @param {string} db1_idx - 상품 고유번호
	 * @param {string} db2_idx - 공급사 상품 고유번호
	 */
	function cancelMatchProviderProduct(db1_idx, db2_idx){

		ajaxRequest(API_ENDPOINT.cancelMatchProviderProduct, {
			db1_idx,
			db2_idx,
		})
		.then(res => {
			if( res.status === 'success' ){
				alert(res.message);
				location.reload();
			}else{
				alert(res.message);
			}
		})
		.catch(error => {
			console.error('AJAX 요청 실패:', error);
			alert('서버 통신에 실패했습니다.');
		});
	}

	return {
		cancelMatchProviderProduct
	};

})();


$(function(){

	$("#searchBtn").on('click',function(){

        // 검색 파라미터 수집
        var params = {};

        // URL에서 viewMode 파라미터 가져오기
        var urlParams = new URLSearchParams(window.location.search);

        // 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
        var fields = {
            's_partner': $("#s_partner").val(),
			's_godo_match': $("#s_godo_match").val(),
			's_supplier_match': $("#s_supplier_match").val(),
			's_keyword': $("#s_keyword").val(),
        };

        // 유효한 값만 params에 추가
        for (var key in fields) {
            if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '') {
                params[key] = fields[key];
            }
        }

        // URL 쿼리 문자열 생성
        var queryString = Object.keys(params)
            .map(function(key) {
                return key + '=' + encodeURIComponent(params[key]);
            })
            .join('&');

        // 페이지 이동
        location.href = '/ad/provider/prd_provider' + (queryString ? '?' + queryString : '');

    });

	// 매칭취소 버튼 클릭
	$('.cancel-match-btn').on('click', function(){

		const db1_idx = $(this).data('db1-idx');
		const db2_idx = $(this).data('db2-idx');

		dnConfirm(
			'정말 매칭취소 하시겠습니까?',
			'취소하시면 데이터는 복구되지 않습니다.',
			() => {
				prdProvider.cancelMatchProviderProduct(db1_idx, db2_idx);
			}
		);

	});

});

//--> 
</script>