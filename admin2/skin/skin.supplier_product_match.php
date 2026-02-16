<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\RequestHandler;
use App\Utils\HttpClient; 
use App\Services\SimpleTokenMatcher;
use App\Services\ProductPartnerService;
use App\Models\BrandModel;

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
    'doradora' => [
        'name' => '도라도라',
        'idx' => 6,
    ],
    'bunny' => [
        'name' => '바니컴퍼니',
        'idx' => 8,
    ],
    'allcon' => [
        'name' => '올컨코리아',
        'idx' => 7,
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
        'is_match_excluded' => 'N',
    ];

    $getProductPartnerList = $productPartnerService->getProductPartnerList($payLoad);
    $db1Rows = $getProductPartnerList->toArray();

    $brandIdxList = array_unique(array_column($db1Rows, 'brand_idx'));
    $brandIdxList = array_values($brandIdxList);

    if (!empty($brandIdxList)) {
        $brands = BrandModel::whereIn('BD_IDX', $brandIdxList)
            ->select('BD_NAME', 'BD_IDX')
            ->get()
            ->toArray();
    } else {
        $brands = [];
    }

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
	<h1>공급사 상품 매칭</h1>
    <h3>인트라넷에 등록된 상품과 공급사 사이트 상품을 매칭합니다.</h3>
</div>

<?php
//dd($matchAllResult);
?>

<style>
	.prd-name{
		width:200px;
		white-space:normal !important;
		word-wrap:break-word;
		word-break:break-all;
	}

/* 행 선택 시 강조 */
.table-st1 tbody tr.row-selected {
    background: #f0f8ff !important;
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
                        <option value="doradora" <?=$site == 'doradora' ? 'selected' : ''?>>doradora (도라도라)</option>
                        <option value="bunny" <?=$site == 'bunny' ? 'selected' : ''?>>bunny (바니컴퍼니)</option>
                        <option value="allcon" <?=$site == 'allcon' ? 'selected' : ''?>>allcon (올컨코리아)</option>
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
                            <th class="">고도몰<br>등록상태</th>
                            <th class="">고도몰<br>판매가</th>
                            <th class="" style="width:100px;">브랜드</th>
                            <th class="list-">상품명</th>
                            <th class="list-idx">이미지</th>
                            <th class="">매칭 스코어</th>
                            <th class="">공급사<br>이미지</th>
                            <th class="">추천 매칭 상품명</th>
                            <th class="">옵션</th>
                            <th>공급사<br>매칭번호</th>
                            <th>공급사<br>사이트</th>
                            <th>공급사<br>상품번호</th>
                            <th>공급사<br>판매상태</th>
                            <th>공급<br>입점사</th>
                            <th>공급가격</th>
                            <th style="width:80px;">배송비</th>
                            <th>최총공급가</th>
                            <th>마진율</th>
                            <th style="width:95px;">매칭</th>
                            <th class="" style="width:95px;">검색매칭</th>
                            <th class="" style="width:95px;">매칭제외</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ( $matchAllResult as $row ){

                                // match_data 안전 처리
                                $matchData   = $row['match_data'] ?? [];
                                $isOption    = ($matchData['is_option'] ?? 'N') === 'Y';
                                $option_data = [];
                                if( $isOption && !empty($matchData['option_data']) ){
                                    $option_data = json_decode($matchData['option_data'], true) ?: [];
                                }

                        ?>
                        <tr id="match_id_<?=$row['db1_idx']?>"
                            data-price="<?=$matchData['price'] ?? ''?>"
                            data-delivery-fee="<?=$matchData['delivery_fee'] ?? ''?>"
                            data-supplier-site="<?=$matchData['site'] ?? ''?>"
                            data-supplier-2nd-name="<?=$matchData['supplier'] ?? ''?>"
                            data-supplier-prd-pk="<?=$matchData['prd_pk'] ?? ''?>"
                            data-prd-name="<?=htmlspecialchars($matchData['name'] ?? '', ENT_QUOTES, 'UTF-8')?>"
                            data-supplier-img-src="<?=$matchData['image_url'] ?? ''?>"
                            data-is-vat="<?=$matchData['is_vat'] ?? ''?>"
                        >
                            <td><input type="checkbox" name="" value="<?php echo $row['db1_idx']; ?>"></td>
                            <td class="text-center"><?=$row['db1_idx']?></td>
                            <td class="text-center">
                                <?php if (!empty($row['prd_data']['godo_goodsNo'])) { ?>
                                    <div style="font-size: 12px;">
                                        #<?=$row['prd_data']['godo_goodsNo']?>
                                    </div>
                                    <div class="m-t-3">
                                        <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall('<?=$row['prd_data']['godo_goodsNo']?>');">쑈당몰 상품보기</button>
                                    </div>
                                    <div class="m-t-5">
                                        <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin('<?=$row['prd_data']['godo_goodsNo']?>');">관리자 상품보기</button>
                                    </div>
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?=$row['prd_data']['status'] ?? ''?>
                                <?php if( $row['prd_data']['status'] == '품절' ){ ?>
                                    <br><span class="text-red"><?=date('Y.m.d', strtotime($row['prd_data']['sold_out_date'])) ?? ''?></span>
                                <?php } ?>
                            </td>
                            <td class="text-right"><?=number_format($row['prd_data']['sale_price'])?></td>
                            <td class="text-center">
                                <a href="javascript:koegAd.brandModify(<?=$row['prd_data']['brand_idx']?>);"><?=$row['db1_brand_name']?></a>
                            </td>
                            
                            <td class="text-right prd-name">
                                <a href="javascript:prdProviderQuick(<?=$row['db1_idx']?>);"><b><?=$row['db1_name']?></b></a>

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
                            <td class="text-center"><?=$row['score'] ?? '-' ?></td>
                            <td><img src="<?=$matchData['image_url'] ?? ''?>" style="height:70px; border:1px solid #eee !important;"></td>
                            <td class="text-left prd-name">
                                <a href="javascript:goSupplierProductEdit('<?=$matchData['idx'] ?? ''?>');"><?=$matchData['name'] ?? ''?></a>
                            </td>
                            <td>
                                <?php
                                    if( ($matchData['is_option'] ?? 'N')=="Y") {
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
                                <button type="button" class="btnstyle1 btnstyle1-xs" 
                                    onclick="goSupplierProductEdit('<?=$matchData['idx'] ?? ''?>');" >#<?=$matchData['idx'] ?? ''?></button>
                            </td>
                            <td><?=$matchData['site'] ?? ''?></td>
                            <td class="text-center">
                                <?php
                                    if (!empty($matchData['prd_pk'])) {
                                ?>
                                    <div style="font-size: 12px;">
                                        #<?= $matchData['prd_pk'] ?>
                                    </div>
                                    <div class="m-t-3">
                                        <button type="button" class="btnstyle1 btnstyle1-xs"
                                            onclick="goSupplierProduct('<?= $matchData['site'] ?>', '<?= $matchData['prd_pk'] ?>');">공급사 사이트</button>
                                    </div>
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>

                            <!-- 공급사 판매상태 -->
                            <td class="text-center">
                                <?php $matchStatus = $matchData['status'] ?? ''; ?>
                                <?=$matchStatus?>
                                <?php if ($matchStatus === '품절') { ?>
                                    <br><span class="text-red"><?=date('Y.m.d', strtotime($matchData['sold_out_date'] ?? ''))?></span>
                                <?php } ?>
                            </td>

                            <!-- 공급 입점사 -->
                            <td><?=$matchData['supplier'] ?? ''?></td>
                            <td class="text-right">
                                <?php
                                    if( !empty($matchData['price']) ){
                                ?>
                                        <?=number_format($matchData['price'])?>
                                <?php }else{ ?>
                                        -
                                <?php } ?>
                            </td>
                            <td class="text-right">
                                <?php
                                    if( !empty($matchData['delivery_fee']) ){
                                ?>
                                    <span style="display:block; font-size:11px; color:#666;">(<?=$matchData['delivery_com']?>)</span>
                                    <?=number_format($matchData['delivery_fee'])?>
                                <?php }else{ ?>
                                        -
                                <?php } ?>
                            </td>
                            <td class="text-right">
                                <?php
                                    $total_cost_price = ($matchData['price'] ?? 0) + ($matchData['delivery_fee'] ?? 0);
                                    if( !empty($total_cost_price) ){
                                ?>
                                    <?=number_format($total_cost_price)?>
                                <?php }else{ ?>
                                        -
                                <?php } ?>
                            </td>
                            <td class="text-right">
                                <?php
                                    $margin_price = ($row['prd_data']['sale_price'] ?? 0) - ($total_cost_price ?? 0);
                                    $margin_rate  = (($row['prd_data']['sale_price'] ?? 0) > 0)
                                        ? ($margin_price / $row['prd_data']['sale_price'] * 100)
                                        : null;

                                    if( !empty($margin_price) ){
                                ?>
                                    <?=number_format($margin_price)?>
                                    <div style="font-size:11px; color:#666;">
                                        <?php if( !is_null($margin_rate) ){ ?>
                                            <?=number_format($margin_rate, 2)?>%
                                        <?php }else{ ?>
                                            -
                                        <?php } ?>
                                    </div>
                                <?php }else{ ?>
                                        -
                                <?php } ?>
                            </td>

                            <?php
                                //이미 매칭이 완료된 상품일 경우
                                if( !empty($row['prd_data']['supplier_prd_idx']) ){
                            ?>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                            <?php }else{ ?>

                                <td class="text-center">
                                    

                                    <?php if( ($row['prd_data']['godo_is_option'] ?? 'N') == "Y" ): ?>
                                        옵션있는 상품
                                    <?php else: ?>

                                        <?php
                                            if( !empty($row['match_data']['idx'])){
                                                if( !empty($row['match_data']['is_option']=="N") ){
                                        ?>
                                        <button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-sm match-btn" 
                                            data-db1-idx="<?=$row['db1_idx']?>" 
                                            data-db2-idx="<?=$row['match_data']['idx']?>"
                                            id="match_btn_<?=$row['match_data']['idx']?>"
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
                                            id="option_match_btn_<?=$row['match_data']['idx']?>"
                                        >옵션매칭</button>
                                        <?php } } ?>

                                    <?php endif; ?>

                                </td>
                                <td class="text-center">
                                    <button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-sm match-btn-one" 
                                        data-db1-idx="<?=$row['db1_idx']?>" 
                                        data-db2-idx="<?=$matchData['idx'] ?? ''?>"
                                    >검색매칭</button>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-sm match-excluded-btn" 
                                        data-db1-idx="<?=$row['db1_idx']?>" 
                                    >매칭제외</button>
                                </td>

                            <?php } ?>

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
                    $(`#match_id_${db1_idx} .match-btn`).prop('disabled', true).text('완료');
                }else{
                    $(`#match_id_${db1_idx} .option-match-btn`).prop('disabled', true).text('완료');
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

    // 행 클릭/버튼 클릭 시 선택 표시
    $('.table-st1 tbody').on('click', 'tr', function(e){
        $('.table-st1 tbody tr').removeClass('row-selected');
        $(this).addClass('row-selected');
    });

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

    $('.match-excluded-btn').on('click', function(){
        const $btn = $(this);
        const db1_idx = $btn.data('db1-idx');

        $.confirm({
            title: '매칭제외 처리',
            boxWidth: '420px',
            useBootstrap: false,
            content: ''
                + '<div style="text-align:left;">'
                + '  <div style="margin-bottom:8px; color:#666;">매칭제외 사유를 입력해주세요.</div>'
                + '  <input type="text" id="matchExcludedReasonInput" class="form-control" placeholder="처리사유 입력" />'
                + '  <div style="margin-top:10px;">'
                + '      <button type="button" id="quickReasonSupplierStop" class="btnstyle1 btnstyle1-gary btnstyle1-xs">공급사 판매중단</button>'
                + '  </div>'
                + '</div>',
            onContentReady: function(){
                const self = this;
                this.$content.find('#quickReasonSupplierStop').on('click', function(){
                    self.$content.find('#matchExcludedReasonInput').val('공급사 판매중단');
                    self.$$submit.trigger('click');
                });
            },
            buttons: {
                cancel: {
                    text: '취소'
                },
                submit: {
                    text: '처리',
                    btnClass: 'btn-blue',
                    action: function(){
                        const reason = (this.$content.find('#matchExcludedReasonInput').val() || '').trim();
                        if (!reason) {
                            alert('처리사유를 입력해주세요.');
                            return false;
                        }

                        $.ajax({
                            url: '/admin/provider_product/action',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action_mode: 'product_match_excluded',
                                idx: db1_idx,
                                process_reason: reason
                            }
                        }).done(function(res){
                            if (res && res.status === 'success') {
                                const $targetRow = $(`#match_id_${db1_idx}`);
                                $targetRow.fadeOut(150, function(){
                                    $(this).remove();
                                });
                                showToast("매칭제외 처리 완료", new Date().toLocaleTimeString());
                            } else {
                                alert((res && res.message) ? res.message : '매칭제외 처리에 실패했습니다.');
                            }
                        }).fail(function(){
                            alert('서버 통신에 실패했습니다.');
                        });
                    }
                }
            }
        });
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