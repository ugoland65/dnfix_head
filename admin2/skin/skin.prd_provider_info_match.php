<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\RequestHandler;
use App\Utils\HttpClient;
use App\Services\ProductPartnerService;
use App\Services\SimpleTokenMatcher;

$requestHandler = new RequestHandler();
$requestData = $requestHandler->getAll();

$prd_idx = $requestData['prd_idx'];
$search_name = $requestData['search_name'] ?? null;

$productPartnerService = new ProductPartnerService(); 
$prdData = $productPartnerService->getProductPartnerInfo($prd_idx);

//dump($prdData);
$match_name = $search_name ?? $prdData['name'];

/*
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
*/
$supplierData = [
    '3' => [
        'name' => '모브',
        'idx' => 3,
        'code' => 'mobe',
    ],
    '10' => [
        'name' => '바이담',
        'idx' => 10,
        'code' => 'byedam',
    ],
    '6' => [
        'name' => '도라도라',
        'idx' => 6,
        'code' => 'doradora',
    ],
    '8' => [
        'name' => '바니컴퍼니',
        'idx' => 8,
        'code' => 'bunny',
    ],
];

$partnerIdx = $prdData['partner_idx'] ?? null;
$site = $supplierData[$partnerIdx]['code'] ?? ($prdData['site'] ?? 'mobe');

// API URL 구성
$url = "https://dnetc01.mycafe24.com/api/SupplierProduct?site=".$site."&match_status=unmatched";

// 검색어가 있을 경우 검색 모드와 키워드 추가
if (!empty($search_name)) {
    $url .= "&search_mode=on&search_keyword=" . urlencode($search_name);
}

// 보낼 API Key
$headers = [
    "Content-Type: application/json",
    "X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f"
];

// GET 요청
$response = HttpClient::getData($url, $headers);
$data = json_decode($response, true);
$db2Rows = $data['data']['supplierProducts'];

if (!empty($search_name)) {
    $matchResult = [];
    foreach($db2Rows as $row){
        $matchResult[] = [
            'match_data' => $row,
            'score' => 1.0,
        ];
    }

}else{
    $simpleTokenMatcher = new SimpleTokenMatcher();
    $matchResult = $simpleTokenMatcher->matchCandidates($match_name, $db2Rows, 100, $site);
}
?>
<form id="prd_provider_info_match_form">
<table class="table-style ">
    <colgroup>
        <col width="150px"/>
        <col  />
    </colgroup>
    <tbody>
        <tr>
            <th>상품명</th>
            <td>
                <?=$prdData['name']?>
            </td>
        </tr>

        <tr>
            <th>매칭 상품명 검색</th>
            <td>
                <input type="text" name="search_name" value="<?=$match_name?>" style="width:200px;" id="search_name_input">
                <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdProviderInfoMatch.search()">검색</button>
            </td>
        </tr>


    </tbody>
</table>
</form>

<style>
.scroll-wrap {
    height: 700px !important;
    max-height: 700px !important;
}

.prd-name{
    width:200px;
    white-space:normal !important;
    word-wrap:break-word;
    word-break:break-all;
}
</style>
<div class="scroll-wrap m-t-10">
    <table class="table-st1">
        <thead>
        <tr>
            <th>매칭 스코어</th>
            <th>공급사<br>이미지</th>
            <th>추천 매칭 상품명</th>
            <th>옵션</th>
            <th>사이트</th>
            <th>공급사 상품번호</th>
            <th>공급<br>입점사</th>
            <th>공급가</th>
            <?php if( $prdData['sale_price'] > 0 ){?>
                <th>고도몰<br>판매가</th>
                <th>마진율</th>
            <?php } ?>
            <th>매칭</th>
        </tr>
        </thead>
        <tbody>
        <?php
            foreach($matchResult as $row){

                if( $row['match_data']['is_option']=="Y") {
                    $option_data = [];
                    if( !empty($row['match_data']['option_data']) ){
                        $option_data = json_decode($row['match_data']['option_data'], true);
                    }
                }
        ?>
        <tr id="match_id_<?=$row['match_data']['idx']?>"
            data-price="<?=$row['match_data']['price']?>"
            data-delivery-fee="<?=$row['match_data']['delivery_fee']?>"
            data-supplier-site="<?=$row['match_data']['site']?>"
            data-supplier-2nd-name="<?=$row['match_data']['supplier']?>"
            data-supplier-prd-pk="<?=$row['match_data']['prd_pk']?>"
            data-prd-name="<?=htmlspecialchars($row['match_data']['name'] ?? '', ENT_QUOTES, 'UTF-8')?>"
            data-supplier-img-src="<?=$row['match_data']['image_url']?>"
            data-is-vat="<?=$row['match_data']['is_vat']?>"
        >
            <td class="text-center"><?=round($row['score'], 2)?></td>
            <td><img src="<?=$row['match_data']['image_url']?>" style="height:70px; border:1px solid #eee !important; cursor: pointer;" class="supplier-thumbnail" onclick="showImageModal('<?=$row['match_data']['image_url']?>')"></td>
            <td class="prd-name">
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
            <td><?=$row['match_data']['site']?></td>
            <td class="text-center">
                <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
                    onclick="goSupplierProduct('<?=$row['match_data']['site']?>', '<?=$row['match_data']['prd_pk']?>');" >#<?=$row['match_data']['prd_pk']?></button>
                <?php
                /*
                <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
                    onclick="goSupplierProductEdit('<?=$row['match_data']['idx']?>');" >#<?=$row['match_data']['idx']?></button>
                */
                ?>
            </td>
            <td><?=$row['match_data']['supplier']?></td>
            <td class="text-right"><?=number_format($row['match_data']['price'])?></td>

            <?php if( $prdData['sale_price'] > 0 ){?>
                <td class="text-right"><?=number_format($prdData['sale_price'])?></td>
                <td class="text-right">
                    <?php
                        $margin = ($prdData['sale_price'] ?? 0) - ($row['match_data']['price'] ?? 0);
                        $margin_rate = $margin / $prdData['sale_price'] * 100;
                    ?>
                    <?=number_format($margin)?>
                    <br><b><?=number_format($margin_rate, 2)?>%</b>
                </td>
            <?php } ?>
            <td>
                <?php
                    if( $row['match_data']['is_option'] == "Y" ){
                ?>
                    <select name="option_match" id="option_match_<?=$row['match_data']['idx']?>">
                        <?php
                            foreach($option_data as $option){
                                foreach($option['items'] as $item){
                                    $disabled = '';
                                    if( $item['is_matched'] == 'Y' ){
                                        $disabled = 'disabled';
                                        $value = $item['value']." (매칭완료 : #".$item['provider_prd_idx'].")";
                                    }else{
                                        $value = $item['value'];
                                    }

                                    echo "<option value='".$item['value']."' ".$disabled.">".$value."</option>";
                                }
                            }
                        ?>
                    </select>

                    <button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-sm option-match-btn" 
                        data-db1-idx="<?=$prd_idx?>" 
                        data-db2-idx="<?=$row['match_data']['idx']?>"
                    >옵션매칭</button>
                <?php } ?>

                <button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-sm match-btn" 
                    data-db1-idx="<?=$prd_idx?>" 
                    data-db2-idx="<?=$row['match_data']['idx']?>"
                >바로매칭</button>

            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- 이미지 모달 팝업 -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <div class="image-modal-content" onclick="event.stopPropagation()">
        <span class="close-button" onclick="closeImageModal()">&times;</span>
        <img id="modalImage" src="" alt="원본 이미지">
    </div>
</div>

<style>
.image-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    cursor: pointer;
}

.image-modal-content {
    position: relative;
    margin: auto;
    display: block;
    width: 90%;
    max-width: 800px;
    top: 50%;
    transform: translateY(-50%);
    text-align: center;
}

.image-modal-content img {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
}

.close-button {
    position: absolute;
    top: -40px;
    right: 10px;
    color: white;
    font-size: 35px;
    font-weight: bold;
    cursor: pointer;
    z-index: 10001;
}

.close-button:hover {
    color: #ccc;
}

.supplier-thumbnail:hover {
    opacity: 0.8;
    transition: opacity 0.3s ease;
}
</style>

<script>
// 이미지 모달 함수들
function showImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    
    modalImg.src = imageSrc;
    modal.style.display = 'block';
    
    // ESC 키로 모달 닫기
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImageModal();
        }
    });
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    
    // 이벤트 리스너 제거
    document.removeEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImageModal();
        }
    });
}

var prdProviderInfoMatch = (function(){

    const API_ENDPOINT = {
        match_provider_product: '/router/matchProviderProduct/',
        match: '/ad/ajax/prd_provider_info_match'
    };

    function matchProviderProduct(mode='direct', db1_idx, db2_idx, option_match=null){

        const price = $(`#match_id_${db2_idx}`).data('price');
        const delivery_fee = $(`#match_id_${db2_idx}`).data('delivery-fee');
        const supplier_site = $(`#match_id_${db2_idx}`).data('supplier-site');
        const supplier_2nd_name = $(`#match_id_${db2_idx}`).data('supplier-2nd-name');
        const supplier_prd_pk = $(`#match_id_${db2_idx}`).data('supplier-prd-pk');
        const prd_name = $(`#match_id_${db2_idx}`).data('prd-name');
        const supplier_img_src = $(`#match_id_${db2_idx}`).data('supplier-img-src');
        const is_vat = $(`#match_id_${db2_idx}`).data('is-vat');

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
                $(`#match_id_${db2_idx}`).addClass('matched').css('background-color', '#f0f8ff');

                if( mode == 'direct' ){
                    $(`#match_id_${db2_idx} .match-btn`).prop('disabled', true).text('완료');
                }else{
                    $(`#match_id_${db2_idx} .option-match-btn`).prop('disabled', true).text('완료');
                }

                location.reload();
            } else {
                alert('매칭 실패: ' + (res.message || '알 수 없는 오류'));
            }
        })
        .catch(error => {
            console.error('AJAX 요청 실패:', error);
            alert('서버 통신에 실패했습니다.');
        });

    }

    function search(){

        const search_name = $('[name="search_name"]').val();

        ajaxRequest(API_ENDPOINT.match, {
            search_name,
            prd_idx
        }, {  method: 'GET', dataType: 'html' })
            .then((getdata) => {
                $('#crm_body').html(getdata);
            })
            .catch((error) => {
                alert('뷰 변경 실패');
            });

    }

    return {
        matchProviderProduct,
        search
    }

})();

$(function(){
    // 검색 인풋 엔터키 처리
    $('#search_name_input').on('keypress', function(e){
        if(e.which === 13){
            e.preventDefault();
            prdProviderInfoMatch.search();
        }
    });


    $('.match-btn').on('click', function(){
        const db1_idx = $(this).data('db1-idx');
        const db2_idx = $(this).data('db2-idx');
        prdProviderInfoMatch.matchProviderProduct('direct', db1_idx, db2_idx);
    });

    $('.option-match-btn').on('click', function(){
        const db1_idx = $(this).data('db1-idx');
        const db2_idx = $(this).data('db2-idx');
        const $select = $(`#option_match_${db2_idx}`);
        const option_match = $select.val();

        // 현재 선택된 option 요소 찾기
        const $selectedOption = $select.find('option:selected');

        console.log($selectedOption);
        if ($selectedOption.prop('disabled')) {
            alert('이미 매칭된 옵션입니다.');
            return; // 매칭 함수 실행하지 않음
        }

        prdProviderInfoMatch.matchProviderProduct('option', db1_idx, db2_idx, option_match);

    });

});
</script>    