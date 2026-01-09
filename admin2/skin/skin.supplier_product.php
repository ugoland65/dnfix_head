<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\RequestHandler;
use App\Utils\HttpClient; 
use App\Services\SimpleTokenMatcher;
use App\Services\ProductPartnerService;
use App\Utils\Pagination;
use App\Core\Config;

$requestHandler = new RequestHandler();
$requestData = $requestHandler->getAll();

$s_match_status = $requestData['s_match_status'] ?? 'unmatched';
$site = $requestData['s_site'] ?? null;
$page = $requestData['page'] ?? 1;

if( $site ){

    $url = "https://dnetc01.mycafe24.com/api/SupplierProduct?site=".$site."&match_status=".$s_match_status."&page=".$page."&limit=500";

    // 보낼 API Key
    $headers = [
        "Content-Type: application/json",
        "X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f"
    ];

    // GET 요청
    $response = HttpClient::getData($url, $headers);
    $data = json_decode($response, true);

    /*
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    */

    $pagination_total = $data['data']['pagination']['total'];
    $pagination_per_page = $data['data']['pagination']['per_page'];
    $pagination_current_page = $data['data']['pagination']['current_page'];


    $pagination = new Pagination($pagination_total, $pagination_per_page, $pagination_current_page, 10);
    $paginationHtml = $pagination->renderLinks();

}else{

    $data = [];
    $pagination_total = 0;
    $pagination_per_page = 0;
    $pagination_current_page = 0;
    $paginationHtml = '';

}
?>
<div id="contents_head">
	<h1>공급사 사이트 상품DB</h1>
    <h3>공급사 사이트에서 크롤링(수집)된 상품입니다.</h3>
    <div id="head_write_btn">
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap">

            <div class="table-top">
				<ul class="total">
					Total : <b><?=number_format($pagination_total)?></b>
				</ul>
                <ul>
					<select name="s_site" id="s_site" >
						<option value=""  >공급사 사이트</option>
                        <option value="mobe" <?=$site == 'mobe' ? 'selected' : ''?>>mobe (모브)</option>
                        <option value="byedam" <?=$site == 'byedam' ? 'selected' : ''?>>byedam (바이담)</option>
                        <option value="doradora" <?=$site == 'doradora' ? 'selected' : ''?>>doradora (도라도라)</option>
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
                            <th class="">공급사<br>사이트</th>
                            <th class="list-idx">사이트<br>고유번호</th>
                            <th class="">이미지</th>
                            <th class="">사이트<br>카테고리</th>
                            <th class="" style="width:300px;">상품명</th>
                            <th>옵션</th>
                            <th>매칭</th>
                            <th class="">공급<br>입점사</th>
                            <th>공급가</th>
                            <th>배송비</th>
                            <th>택배사</th>
                            <th>VAT</th>
                            <th>10%</th>
                            <th>20%</th>
                            <th>30%</th>
                            <th>40%</th>
                            <th>최저판매가</th>
                            <th>최저가 마진율</th>
                            <th>수정일</th>
                            <th>등록일</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ( $data['data']['supplierProducts'] ?? [] as $row ){

                                $cost_price = $row['price'] + $row['delivery_fee'];
                                $margin10 = $cost_price / (1 - 0.10);
                                $margin20 = $cost_price / (1 - 0.20);
                                $margin30 = $cost_price / (1 - 0.30);
                                $margin40 = $cost_price / (1 - 0.40);
                                
                                // 최저판매가 마진율 계산
                                $min_margin_rate = 0;
                                if ($row['min_sale_price'] > 0 && $cost_price > 0) {
                                    $min_margin_rate = (($row['min_sale_price'] - $cost_price) / $row['min_sale_price']) * 100;
                                }

                        ?>
                        <tr id="trid_><?=$row['idx']?>" class="">
                            <td class="list-checkbox"><input type="checkbox" name="key_check[]" value="><?=$row['idx']?>" ></td>	
                            <td class="list-idx"><?=$row['idx']?></td>
                            <td class="list-idx"><?=$row['site']?></td>
                            <td class="list-idx">
                                <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
                                onclick="goSupplierProduct('<?=$row['site']?>', '<?=$row['prd_pk']?>');" >#<?=$row['prd_pk']?></button>
                            </td>
                            <td >
                                <img src="<?=$row['image_url']?>" style="height:70px; border:1px solid #eee !important;">
                            </td>
                            <td class="text-center"><?=$row['category']?></td>
                            <td class="text-left" style="white-space: normal !important;">
                                <a href="javascript:goSupplierProductEdit('<?=$row['idx']?>');"><?=$row['name']?></a>
                            </td>
                            <td>
                                <?php 
                                    if( $row['is_option'] == 'Y' && !empty($row['option_data']) ){ 
                                        $option_data = json_decode($row['option_data'], true);
                                        foreach($option_data as $option){
                                            echo $option['name']."<br>";
                                            foreach($option['items'] as $item){
                                                echo "-".$item['value']."<br>";
                                            }
                                        }
                                    }else{
                                ?>
                                    -
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?php if( !empty($row['provider_prd_idx']) ): ?>
                                    <?=$row['provider_prd_idx']?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?=$row['supplier']?></td>
                            <td class="text-right"><?=number_format($row['price'])?></td>
                            <td class="text-right"><?=number_format($row['delivery_fee'])?></td>
                            <td class="text-center"><?=$row['delivery_com']?></td>
                            <td class="text-center"><?=$row['is_vat']?></td>
                            <td class="text-right"><?=number_format($margin10)?><br><b><?=number_format($margin10 - $cost_price)?></b></td>
                            <td class="text-right"><?=number_format($margin20)?><br><b><?=number_format($margin20 - $cost_price)?></b></td>
                            <td class="text-right"><?=number_format($margin30)?><br><b><?=number_format($margin30 - $cost_price)?></b></td>
                            <td class="text-right"><?=number_format($margin40)?><br><b><?=number_format($margin40 - $cost_price)?></b></td>
                            <td class="text-right">
                                <?=number_format($row['min_sale_price'])?>
                                <?php if ($row['min_sale_price'] > 0): ?>   
                                    <br><b><?=number_format($row['min_sale_price'] - $cost_price)?></b>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php if ($row['min_sale_price'] > 0): ?>
                                    <?=number_format($min_margin_rate, 1)?>%
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?=$row['updated_at']?></td>
                            <td class="text-center"><?=$row['created_at']?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>    

        </div>
    </div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap"><?=$paginationHtml?></div>
</div>
<script>

$(function(){

    $("#searchBtn").on('click',function(){

        // 검색 파라미터 수집
        var params = {};

        // URL에서 viewMode 파라미터 가져오기
        var urlParams = new URLSearchParams(window.location.search);

        // 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
        var fields = {
            's_site': $("#s_site").val(),
            's_match_status': $("#s_match_status").val(),
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
        location.href = '/ad/provider/supplier_product' + (queryString ? '?' + queryString : '');
    });

});
</script>   