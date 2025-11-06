<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\RequestHandler;
use App\Utils\HttpClient; 
use App\Services\SimpleTokenMatcher;
use App\Services\ProductPartnerService;
use App\Models\BrandModel;

/*
use App\Services\CrawlerService;

$crawlerService = new CrawlerService();

echo "<h3>🔍 바이담 크롤링 디버그 (2단계 접근법)</h3>";
echo "<pre>";

// 1단계: 로그인만 수행
echo "=== 1단계: 로그인 수행 ===\n";
$loginResult = $crawlerService->loginOnly(true);
echo "로그인 결과: " . ($loginResult ? "✅ 성공" : "❌ 실패") . "\n\n";

if ($loginResult) {
    // 2단계: 로그인된 세션으로 상품 페이지 접근
    echo "=== 2단계: 상품 크롤링 ===\n";
    $productInfo = $crawlerService->crawlProduct('1000001528', true);
    
    echo "</pre>";
    echo "<h3>크롤링 결과:</h3>";
    echo "<pre>";
    print_r($productInfo);
    echo "</pre>";
} else {
    echo "❌ 로그인 실패로 크롤링 중단\n";
    echo "</pre>";
}
*/


$requestHandler = new RequestHandler();
$requestData = $requestHandler->getAll();

$s_match_status = $requestData['s_match_status'] ?? 'unmatched';
$site = $requestData['s_site'] ?? null;
$page = $requestData['page'] ?? 1;
$s_brand = $requestData['s_brand'] ?? null;

$supplierData = [
    'mobe' => [
        'name' => '모브',
        'idx' => 3,
    ],
    'byedam' => [
        'name' => '바이담',
        'idx' => 10,
    ],
];

if( $site ){

    $url = "https://dnetc01.mycafe24.com/api/SupplierProduct?site=".$site."&match_status=".$s_match_status;

    // 보낼 API Key
    $headers = [
        "Content-Type: application/json",
        "X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f"
    ];

    // GET 요청
    $response = HttpClient::getData($url, $headers);
    $data = json_decode($response, true);

    $productPartnerService = new ProductPartnerService();

    $payLoad = [
        's_partner' => $supplierData[$site]['idx'],
        'match_status' =>$s_match_status,
        's_brand' => $s_brand,
    ];

    $getProductPartnerList = $productPartnerService->getProductPartnerList($payLoad);
    $db1Rows = $getProductPartnerList->toArray();

    $brandIdxList = array_unique(array_column($db1Rows, 'brand_idx'));
    $brandIdxList = array_values($brandIdxList);

    $brands = BrandModel::whereIn('BD_IDX', $brandIdxList)
        ->select('BD_NAME', 'BD_IDX')
        ->get()
        ->toArray();

    /*
    echo "<pre>";
    print_r($brands);
    echo "</pre>";
    */
    
    $db2Rows = $data['data']['supplierProducts'];

    $simpleTokenMatcher = new SimpleTokenMatcher();
    $matchAllResult = $simpleTokenMatcher->matchAll($db1Rows, $db2Rows, $site);

}else{

    $matchAllResult = [];

}

?>
<div id="contents_head">
	<h1>공급사 외부 매칭</h1>
</div>

<?php
/*
echo "<pre>";
print_r($matchAllResult);
echo "</pre>";
*/
?>

<style>
	.prd-name{
		width:200px;
		white-space:normal !important;
		word-wrap:break-word;
		word-break:break-all;
	}

    .prd-memo{
        color:#ff0000;
    }
</style>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap">

            <div class="table-top">
				<ul class="total">
					Total : <b><?=count($matchAllResult)?></b>
				</ul>
                <ul>
					<select name="s_site" id="s_site" >
						<option value=""  >공급사 사이트</option>
                        <option value="mobe" <?=$site == 'mobe' ? 'selected' : ''?>>mobe (모브)</option>
                        <option value="byedam" <?=$site == 'byedam' ? 'selected' : ''?>>byedam (바이담)</option>
					</select>
				</ul>
                <ul>
					<select name="s_match_status" id="s_match_status" >
						<option value="all_match" <?=$s_match_status == 'all_match' ? 'selected' : ''?>>전체매칭</option>
                        <option value="matched" <?=$s_match_status == 'matched' ? 'selected' : ''?>>매칭완료</option>
                        <option value="unmatched" <?=$s_match_status == 'unmatched' ? 'selected' : ''?>>매칭안됨</option>
					</select>
				</ul>
                <ul>
                    <select name="s_brand" id="s_brand">
                        <option value="" <?= $s_brand == '' ? 'selected' : '' ?>>전체브랜드</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= $brand['BD_IDX'] ?>" <?= ($brand['BD_IDX'] == ($s_brand ?? '')) ? 'selected' : '' ?>>
                                <?= $brand['BD_NAME'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
                        <tr>
                            <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                            <th class="list-idx">고유번호</th>
                            <th class="">고도몰<br>상품번호</th>
                            <th class="">브랜드</th>
                            <th class="">검색매칭</th>
                            <th class="list-">쑈당몰 등록상품명</th>
                            <th class="list-idx">쑈당몰<br>이미지</th>
                            <th class="">매칭 스코어</th>
                            <th class="">공급사<br>이미지</th>
                            <th class="">추천 매칭 상품명</th>
                            <th class="">옵션</th>
                            <th>공급사<br>매칭번호</th>
                            <th>사이트</th>
                            <th>공급사<br>상품번호</th>
                            <th>공급사</th>
                            <th>매칭</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ( $matchAllResult as $row ){

                                if( $row['match_data']['is_option']=="Y") {
                                    $option_data = [];
                                    if( !empty($row['match_data']['option_data']) ){
                                        $option_data = json_decode($row['match_data']['option_data'], true);
                                    }
                                }

                        ?>
                        <tr id="match_id_<?=$row['db1_idx']?>"
                            data-price="<?=$row['match_data']['price']?>"
                            data-delivery-fee="<?=$row['match_data']['delivery_fee']?>"
                            data-supplier-site="<?=$row['match_data']['site']?>"
                            data-supplier-2nd-name="<?=$row['match_data']['supplier']?>"
                            data-supplier-prd-pk="<?=$row['match_data']['prd_pk']?>"
                            data-prd-name="<?=$row['match_data']['name']?>"
                            data-supplier-img-src="<?=$row['match_data']['image_url']?>"
                            data-is-vat="<?=$row['match_data']['is_vat']?>"
                        >
                            <td><input type="checkbox" name="" value="<?php echo $row['db1_idx']; ?>"></td>
                            <td class="text-center"><?=$row['db1_idx']?></td>
                            <td class="text-center">
                                <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
                                    onclick="goGodoMall(<?=$row['prd_data']['godo_goodsNo']?>);" >#<?=$row['prd_data']['godo_goodsNo']?></button>
                            </td>
                            <td class="text-center">
                                <a href="javascript:koegAd.brandModify(<?=$row['prd_data']['brand_idx']?>);"><?=$row['db1_brand_name']?></a>
                            </td>
                            <td>
                                <button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-sm match-btn-one" 
                                    data-db1-idx="<?=$row['db1_idx']?>" 
                                    data-db2-idx="<?=$row['match_data']['idx']?>"
                                >검색매칭</button>
                            </td>
                            <td class="text-right prd-name">
                                <a href="javascript:prdProviderQuick(<?=$row['db1_idx']?>);"><?=$row['db1_name']?></a>

                                <? if( !empty($row['prd_data']['memo']) ){ ?>
                                    <br><span class="prd-memo"><?=$row['prd_data']['memo']?></span>
                                <? } ?> 

                                <?php 
                                /*
                                if($row['db1_name_transformed'] && $row['db1_name_transformed'] !== $row['db1_name']): ?>
                                    <br><small style="color: #0066cc;">치환됨: <?=$row['db1_name_transformed']?></small>
                                <?php endif; 
                                */
                                ?>

                            </td>
                            <td><img src="<?=$row['db1_img_src']?>" style="height:70px; border:1px solid #eee !important;"></td>
                            <td class="text-center"><?=round($row['score'], 2)?></td>
                            <td><img src="<?=$row['match_data']['image_url']?>" style="height:70px; border:1px solid #eee !important;"></td>
                            <td class="text-left prd-name">
                                <a href="javascript:goSupplierProductEdit('<?=$row['match_data']['idx']?>');"><?=$row['match_data']['name']?></a>
                            </td>
                            <td>
                                <?php
                                    if( $row['match_data']['is_option']=="Y") {
                                        foreach($option_data as $option){
                                            echo $option['name']."<br>";
                                            foreach($option['items'] as $item){
                                                echo "-".$item['value']."<br>";
                                            }
                                        }
                                    }else{
                                ?>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
                                    onclick="goSupplierProductEdit('<?=$row['match_data']['idx']?>');" >#<?=$row['match_data']['idx']?></button>
                            </td>
                            <td><?=$row['match_data']['site']?></td>
                            <td class="text-center">
                                <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
                                    onclick="goSupplierProduct('<?=$row['match_data']['site']?>', '<?=$row['match_data']['prd_pk']?>');" >#<?=$row['match_data']['prd_pk']?></button>
                            </td>
                            <td><?=$row['match_data']['supplier']?></td>
                            <td>
                                
                                <?php if( $row['prd_data']['godo_is_option'] == "Y" ): ?>
                            옵션있는 상품
                                <?php else: ?>

                                    <?php
                                        if( !empty($row['match_data']['idx'])){
                                            if( !empty($row['match_data']['is_option']=="N") ){
                                    ?>
                                    <button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-sm match-btn" 
                                        data-db1-idx="<?=$row['db1_idx']?>" 
                                        data-db2-idx="<?=$row['match_data']['idx']?>"
                                    >바로매칭</button>
                                    <?
                                        }else{
                                    ?>

                                    <select name="option_match" id="option_match_<?=$row['match_data']['idx']?>">
                                        <?php
                                            foreach($option_data as $option){
                                                foreach($option['items'] as $item){
                                                    echo "<option value='".$item['value']."'>".$item['value']."</option>";
                                                }
                                            }
                                        ?>
                                    </select>

                                    <button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-sm option-match-btn" 
                                        data-db1-idx="<?=$row['db1_idx']?>" 
                                        data-db2-idx="<?=$row['match_data']['idx']?>"
                                    >옵션매칭</button>
                                    <?php } } ?>

                                <?php endif; ?>

                            </td>
                        </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    <table>

                </div>
            </div>

        </div>
    </div>
</div>

<script>

const supplierProductMatch = (function(){

    const API_ENDPOINT = {
        match_provider_product: '/router/matchProviderProduct/',
    };

    function matchProviderProduct(mode='direct', db1_idx, db2_idx, option_match=null){

        const price = $(`#match_id_${db1_idx}`).data('price');
        const delivery_fee = $(`#match_id_${db1_idx}`).data('delivery-fee');
        const supplier_site = $(`#match_id_${db1_idx}`).data('supplier-site');
        const supplier_2nd_name = $(`#match_id_${db1_idx}`).data('supplier-2nd-name');
        const supplier_prd_pk = $(`#match_id_${db1_idx}`).data('supplier-prd-pk');
        const prd_name = $(`#match_id_${db1_idx}`).data('prd-name');
        const supplier_img_src = $(`#match_id_${db1_idx}`).data('supplier-img-src');
        const is_vat = $(`#match_id_${db1_idx}`).data('is-vat');

        ajaxRequest(API_ENDPOINT.match_provider_product, {
            mode,
            db1_idx,
            db2_idx,
            price,
            delivery_fee,
            supplier_site,
            supplier_2nd_name,
            supplier_prd_pk,
            prd_name,
            supplier_img_src,
            is_vat,
            option_match
        })
        .then(res => {
            //console.log('매칭 응답:', res);
            if (res.status === 'success') {
                //alert('매칭이 완료되었습니다: ' + res.message);
                // 매칭 완료된 행 스타일 변경
                $(`#match_id_${db1_idx}`).addClass('matched').css('background-color', '#f0f8ff');

                if( mode == 'direct' ){
                    $(`#match_id_${db2_idx} .match-btn`).prop('disabled', true).text('완료');
                }else{
                    $(`#match_id_${db2_idx} .option-match-btn`).prop('disabled', true).text('완료');
                }

            } else {
                alert('매칭 실패: ' + (res.message || '알 수 없는 오류'));
            }
        })
        .catch(error => {
            console.error('AJAX 요청 실패:', error);
            alert('서버 통신에 실패했습니다.');
        });

    }

    return {
        matchProviderProduct
    }

})();

$(function(){

    $('.match-btn').on('click', function(){
        const db1_idx = $(this).data('db1-idx');
        const db2_idx = $(this).data('db2-idx');
        supplierProductMatch.matchProviderProduct('direct', db1_idx, db2_idx);
    });

    $('.match-btn-one').on('click', function(){
        const db1_idx = $(this).data('db1-idx');
        const db2_idx = $(this).data('db2-idx');
        prdProviderQuick(db1_idx, 'match');
    });

    $('.option-match-btn').on('click', function(){
        const db1_idx = $(this).data('db1-idx');
        const db2_idx = $(this).data('db2-idx');
        const option_match = $(`#option_match_${db2_idx}`).val();
        supplierProductMatch.matchProviderProduct('option', db1_idx, db2_idx, option_match);
    });

    $("#searchBtn").on('click',function(){

        // 검색 파라미터 수집
        var params = {};

        // URL에서 viewMode 파라미터 가져오기
        var urlParams = new URLSearchParams(window.location.search);

        // 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
        var fields = {
            's_site': $("#s_site").val(),
            's_match_status': $("#s_match_status").val(),
            's_brand': $("#s_brand").val(),
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
        location.href = '/ad/provider/supplier_product_match' + (queryString ? '?' + queryString : '');

    });

});
</script>