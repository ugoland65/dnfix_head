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
						<option value="" >공급사 사이트</option>
                        <?php foreach($supplier_code_data as $key => $value){ ?>
                            <option value="<?=$key?>" <?=$site == $key ? 'selected' : ''?>><?=$value['name']?> (<?=$value['code']?>)</option>
                        <?php } ?>
					</select>
				</ul>
                <ul>
					<select name="s_status" id="s_status" >
						<option value="" >판매상태</option>
                        <option value="판매중" <?=$s_status == '판매중' ? 'selected' : ''?>>판매중</option>
                        <option value="일시품절" <?=$s_status == '일시품절' ? 'selected' : ''?>>일시품절</option>
                        <option value="품절" <?=$s_status == '품절' ? 'selected' : ''?>>품절</option>
                        <option value="판매중단" <?=$s_status == '판매중단' ? 'selected' : ''?>>판매중단</option>
                        <option value="수집실패" <?=$s_status == '수집실패' ? 'selected' : ''?>>수집실패</option>
					</select>
				</ul>
                <ul>
					<select name="s_match_status" id="s_match_status" >
						<option value="all_match" <?=$s_match_status == 'all_match' ? 'selected' : ''?>>전체매칭</option>
                        <option value="matched" <?=$s_match_status == 'matched' ? 'selected' : ''?>>매칭완료</option>
                        <option value="unmatched" <?=$s_match_status == 'unmatched' ? 'selected' : ''?>>매칭안됨</option>
                        <option value="match_excluded" <?=$s_match_status == 'match_excluded' ? 'selected' : ''?>>매칭제외</option>
					</select>
				</ul>
                <ul>
					<input type="text" name="s_keyword" id="s_keyword" placeholder="상품명 검색" value="<?= $s_keyword ?? '' ?>">
				</ul>
                <ul>
					<button type="button" id="searchBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm"  > 
						<i class="fas fa-search"></i> 검색
					</button>
				</ul>
                <ul>
					<button type="button" id="resetBtn" class="btnstyle1 btnstyle1-sm"  > 
						<i class="fas fa-undo"></i> 초기화
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
                            <th class="">판매상태</th>
                            <th class="list-idx">사이트<br>고유번호</th>
                            <th class="">이미지</th>
                            <th class="">사이트<br>카테고리</th>
                            <th class="" style="width:300px;">상품명</th>
                            <th>옵션</th>
                            <th>매칭상품</th>
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
                            <th>수정일<br>등록일</th>
                            <th>처리일</th>
                            <th>매칭제외</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                            foreach ( $SupplierProductApiData['data']['supplierProducts'] ?? [] as $row ){

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

                                $matchExcludedRaw = $row['match_excluded_data'] ?? '';
                                $match_excluded_data = [];
                                if (is_string($matchExcludedRaw) && trim($matchExcludedRaw) !== '') {
                                    $decodedMatchExcludedData = json_decode($matchExcludedRaw, true);
                                    $match_excluded_data = is_array($decodedMatchExcludedData) ? $decodedMatchExcludedData : [];
                                }

                        ?>
                        <tr id="trid_<?=$row['idx']?>" >
                            <td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$row['idx']?>" ></td>	
                            <td class="list-idx"><?=$row['idx']?></td>
                            <td class=""><?=$row['site']?></td>
                            <td class="text-center"><?=$row['status'] ?? ''?></td>
                            <td class="list-idx">
                                <div style="font-size: 12px;">
                                    #<?= $row['prd_pk'] ?>
                                </div>
                                <div class="m-t-3">
                                    <button type="button" class="btnstyle1 btnstyle1-xs"
                                        onclick="goSupplierProduct('<?= $row['site'] ?>', '<?= $row['prd_pk'] ?>');">공급사 사이트</button>
                                </div>
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
                            <td class="text-left">
                                <?php if( !empty($row['provider_prd_idx']) ): ?>
                                    #<?=$row['provider_prd_idx']?>
                                    <div class="m-t-3" style="font-size: 12px;">
                                        <?= $productPartnerMatchData[$row['provider_prd_idx']]['name'] ?? ''?>
                                    </div>
                                    <div class="m-t-3">
                                        <button type="button" class="btnstyle1 btnstyle1-xs"
                                            onclick="onlyAD.prdProviderQuick('<?=$row['provider_prd_idx']?>', 'info');">매칭된 상품보기</button>
                                    </div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <b><?= $supplier_code_data[$row['site']]['name'] ?></b><br>
                                <?=$row['supplier']?>
                            </td>
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
                            <td class="text-center">
                                <?=date('Y.m.d H:i', strtotime($row['updated_at']))?><br>
                                <?=date('Y.m.d H:i', strtotime($row['created_at']))?>
                            </td>
                            <td class="text-center">
                                <?php if( $row['sold_out_date'] ): ?>
                                    품절처리일 : <?=date('Y.m.d H:i', strtotime($row['sold_out_date']))?>
                                <?php endif; ?>

                            </td>

                            <td class="text-left">
                                
                                <?php if( $row['status'] == '매칭제외' ): ?>


                                    <?php if( $row['match_excluded_date'] ): ?>
                                        처리일 : <?=date('Y.m.d H:i', strtotime($row['match_excluded_date']))?><br>
                                    <?php endif; ?>

                                    사유 : <?=$row['match_excluded_memo'] ?? ''?><br>

                                    <?php if (!empty($match_excluded_data['reg']['name'] ?? '')): ?>
                                        처리자 : <?=$match_excluded_data['reg']['name']?>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-sm match-excluded-btn" 
                                            data-idx="<?=$row['idx']?>" 
                                        >매칭제외</button>
                                <?php endif; ?>
                            </td>
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
	<div class="pageing-wrap"><?=$paginationHtml ?? ''?></div>
    <div class="m-l-20">
        선택된 상품 <span id="selected_product_count">0</span>
        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" id="supplierProductRegBtn">공급사상품 등록대기로 등록</button>
    </div>
</div>

<script>

function select_all() {
    var checkboxes = document.getElementsByName('key_check[]');
    var selectAll = event.target.checked;

    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = selectAll;
        if (selectAll) {
            $(checkboxes[i]).closest('tr').addClass('selected-row');
        } else {
            $(checkboxes[i]).closest('tr').removeClass('selected-row');
        }
    }
    updateSelectedCount();
}

$(function(){

    // 개별 체크박스 선택 시 행 배경색 변경
    $(document).on('change', 'input[name="key_check[]"]', function() {
        if ($(this).is(':checked')) {
            $(this).closest('tr').addClass('selected-row');
        } else {
            $(this).closest('tr').removeClass('selected-row');
        }
        updateSelectedCount();
    });

    // 선택된 상품 등록
    $("#supplierProductRegBtn").on('click', function() {
        var selectedItems = [];
        $('input[name="key_check[]"]:checked').each(function() {
            selectedItems.push($(this).val());
        });

        if (selectedItems.length === 0) {
            alert('등록할 상품을 선택해주세요.');
            return;
        }

        if (!confirm(selectedItems.length + '개 상품을 등록대기로 처리할까요?\n선택한 상품이 이미 공급사 상품으로 등록되있는지 확인을 꼼꼼하게 해주세요.')) {
            return;
        }

        var payload = {
            action_mode: 'product_standby_register',
            partner_idx: '<?= $supplier_code_data[$site]['idx'] ?? '' ?>',
            pks: selectedItems
        };

        ajaxRequest('/admin/provider_product/action', payload)
            .done(function(res) {
                if (res && (res.success || res.status === 'success')) {
                    alert(res.message || '등록대기 처리되었습니다.');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '처리 실패');
                }
            })
            .fail(function(res) {
                alert(res && res.message ? res.message : '에러');
            });
    });

    updateSelectedCount();

    $("#searchBtn").on('click',function(){

        let s_site = $("#s_site").val();

        if( !s_site ){
            alert('공급사 사이트를 선택해주세요.');
            return false;
        }

        // 검색 파라미터 수집
        var params = {};

        // URL에서 viewMode 파라미터 가져오기
        var urlParams = new URLSearchParams(window.location.search);

        // 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
        var fields = {
            's_site': $("#s_site").val(),
            's_match_status': $("#s_match_status").val(),
            's_keyword': $("#s_keyword").val(),
            's_status': $("#s_status").val(),
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
        location.href = '/admin/provider_product/db' + (queryString ? '?' + queryString : '');
    });

    $("#resetBtn").on('click',function(){

        let s_site = $("#s_site").val();
        let s_match_status = $("#s_match_status").val();

        location.href = '/admin/provider_product/db?s_site=' + s_site + '&s_match_status=' + s_match_status;
    });

    // 매칭제외 버튼 클릭
    $(".match-excluded-btn").on('click', function(){
        const idx = $(this).data('idx');
        prdProviderMatchExcluded(null, idx);
    });

});

function updateSelectedCount() {
    var count = $('input[name="key_check[]"]:checked').length;
    $("#selected_product_count").text(count);
}
</script>   