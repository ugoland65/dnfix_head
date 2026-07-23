<div id="contents_head">
	<h1>경쟁사 상품DB</h1>
    <h3>경쟁사 사이트에서 크롤링(수집)된 상품입니다.</h3>
    <div class="m-l-10">
<!--
        <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="competitorProductSyncBtn">상품 동기화</button>
-->
    </div>
    <div id="head_write_btn">
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

        <div class="supplier-site-selector-wrap">
            <div class="supplier-site-selector-title">경쟁사 사이트 선택</div>
            <input type="hidden" name="s_site" id="s_site" value="<?= htmlspecialchars((string)($site ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <div class="supplier-site-selector">
                <?php foreach($competitor_data as $key => $value){ ?>
                    <button
                        type="button"
                        class="supplier-site-btn <?=$site == $key ? 'active' : ''?>"
                        data-site="<?= htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') ?>">
                        <?=$value['name']?> (<?=$value['code']?>)
                    </button>
                <?php } ?>
            </div>
        </div>

		<div id="list_new_wrap">

            <div class="table-top">
				<ul class="total">
					Total : <b><?=number_format($pagination_total)?></b>
				</ul>
                <ul>
					<select name="s_status" id="s_status" >
						<option value="" >판매상태</option>
                        <option value="판매중" <?=$s_status == '판매중' ? 'selected' : ''?>>판매중</option>
                        <option value="품절" <?=$s_status == '품절' ? 'selected' : ''?>>품절</option>
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
                <ul class="m-l-10">
					<select name="s_keyword_mode" id="s_keyword_mode" >
                        <option value="name" <?=$s_keyword_mode == 'name' ? 'selected' : ''?>>상품명</option>
                        <option value="category" <?=$s_keyword_mode == 'category' ? 'selected' : ''?>>카테고리 명</option>
                        <option value="brand_name" <?=$s_keyword_mode == 'brand_name' ? 'selected' : ''?>>브랜드명</option>
					</select>
				</ul>
                <ul>
					<input type="text" name="s_keyword" id="s_keyword" placeholder="검색어" value="<?= $s_keyword ?? '' ?>">
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
                <ul class="right">
                    목록수 :
                    <select name="s_limit" id="s_limit">
                        <option value="100" <?= (int)($s_limit ?? 100) === 100 ? 'selected' : '' ?>>100개씩</option>
                        <option value="200" <?= (int)($s_limit ?? 100) === 200 ? 'selected' : '' ?>>200개씩</option>
                        <option value="300" <?= (int)($s_limit ?? 100) === 300 ? 'selected' : '' ?>>300개씩</option>
                        <option value="500" <?= (int)($s_limit ?? 100) === 500 ? 'selected' : '' ?>>500개씩</option>
                    </select>
                    &nbsp;&nbsp;
                    정렬 : 
                    <select name="s_sort_mode" id="s_sort_mode" >
                        <option value="code" <?=$s_sort_mode == 'code' ? 'selected' : ''?>>상품코드순</option>
                        <option value="updated_at" <?=$s_sort_mode == 'updated_at' ? 'selected' : ''?>>수정일</option>
                        <option value="created_at" <?=$s_sort_mode == 'created_at' ? 'selected' : ''?>>등록일</option>
                        <option value="last_price_changed_at" <?=$s_sort_mode == 'last_price_changed_at' ? 'selected' : ''?>>판매가<br>변경일</option>
                        <option value="last_status_changed_at" <?=$s_sort_mode == 'last_status_changed_at' ? 'selected' : ''?>>판매상태<br>변경일</option>
                        <option value="last_changed_at" <?=$s_sort_mode == 'last_changed_at' ? 'selected' : ''?>>정보<br>변경일</option>
                    </select>
                </ul>
            </div> 

            <div class="table-wrap5 m-t-5">
                <div class="scroll-wrap">

                    <table class="table-st1">
                        <thead>
                        <tr>
                            <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                            <th class="">공급사<br>사이트</th>
                            <th class="">판매상태</th>
                            <th class="list-idx">사이트<br>고유번호</th>
                            <th class="">이미지</th>
                            <th class="">사이트<br>카테고리</th>
                            <th style="width:100px; min-width:100px; max-width:100px;">브랜드</th>
                            <th>바로가기</th>
                            <th class="" style="width:300px;">상품명</th>
                            <th>판매가</th>
                            <th>변경이력</th>
                            <th>수정일<br>등록일</th>
                            <th>판매가<br>변경일</th>
                            <th>판매상태<br>변경일</th>
                            <th>정보<br>변경일</th>
                            <th>매칭</th>
                            <th>매칭상품</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                            foreach ( $CompetitorProductApiData['data']['competitorProducts'] ?? [] as $row ){
                        ?>
                        <tr id="trid_<?=$row['site']?>_<?=$row['prd_pk']?>" >
                            <td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$row['site']?>_<?=$row['prd_pk']?>" ></td>	
                            <td class="text-center">
                                <?php
                                    $rowSiteCode = (string)($row['site'] ?? '');
                                    $rowSiteInfo = $competitor_data[$rowSiteCode] ?? null;
                                    $rowSiteName = is_array($rowSiteInfo) ? (string)($rowSiteInfo['name'] ?? '') : '';
                                ?>
                                <div><?= htmlspecialchars($rowSiteCode, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php if ($rowSiteName !== '') { ?>
                                    <div style="font-size:11px; color:#6b7280;"><b><?= htmlspecialchars($rowSiteName, ENT_QUOTES, 'UTF-8') ?></b></div>
                                <?php } ?>
                            </td>
                            <td class="text-center"><?=$row['sale_status'] ?? ''?></td>
                            <td class="list-idx">
                                <div style="font-size: 12px;">
                                    #<?= $row['prd_pk'] ?>
                                </div>
                                <?php
                                /*
                                <div class="m-t-3">
                                    <button type="button" class="btnstyle1 btnstyle1-xs"
                                        onclick="goSupplierProduct('<?= $row['site'] ?>', '<?= $row['prd_pk'] ?>');">공급사 사이트</button>
                                </div>
                                */ ?>
                            </td>
                            <td >
                                <a href="javascript:goCompetitorProductEdit('<?= $row['site'] ?>', '<?= $row['prd_pk'] ?>');"><img src="<?=$row['image_url']?>" style="height:70px; border:1px solid #eee !important;"></a>
                            </td>
                            <td class="text-left"><?=$row['category'] ?? ''?></td>
                            <td class="text-center" style="width:100px; min-width:100px; max-width:100px; white-space: normal !important;"><?=$row['brand_name']?></td>
                            <td class="text-center">
                                <button
                                    type="button"
                                    class="btnstyle1 btnstyle1-xs competitor-detail-link-btn"
                                    data-detail-url="<?= htmlspecialchars((string)($row['detail_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    바로가기
                                </button>
                            </td>
                            <td class="text-left" style="white-space: normal !important;">
                                <?php foreach (($row['event_tags_json'] ?? []) as $eventTag) { ?>
                                    <span style="display:inline-block; margin-right:3px; padding:1px 4px; color:#fff; background:#dc2626; border-radius:3px; font-size:11px;"><?= htmlspecialchars($eventTag, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php } ?>
                                <p><b><a href="javascript:goCompetitorProductEdit('<?= $row['site'] ?>', '<?= $row['prd_pk'] ?>');"><?=$row['name']?></a></b></p>

                                <?php if ($row['memo']): ?>
                                    <p><span style="font-size:12px; color:#ff0000;"><?= htmlspecialchars($row['memo'], ENT_QUOTES, 'UTF-8') ?></span></p>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php
                                    $discountPercent = (float)($row['discount_percent'] ?? 0);
                                    $originalPrice = (int)($row['original_price'] ?? 0);
                                    $salePrice = (int)($row['price'] ?? 0);
                                    $calculatedDiscountPercent = 0;
                                    if ($discountPercent <= 0 && $originalPrice > 0 && $salePrice !== $originalPrice) {
                                        $calculatedDiscountPercent = round((($originalPrice - $salePrice) / $originalPrice) * 100);
                                    }
                                ?>
                                <div style="margin-bottom:2px;">
                                    <?php if ($discountPercent > 0): ?>
                                        <p><span style="font-size:12px; color:#ff0000;"><b><?= number_format($discountPercent, 0) ?></b>%</span></p>
                                    <?php elseif ($calculatedDiscountPercent !== 0): ?>
                                        <p><span style="font-size:12px; color:#16a34a;"><b><?= number_format($calculatedDiscountPercent) ?></b>%</span></p>
                                    <?php endif; ?>
                                    <?php if ($originalPrice > 0): ?>
                                        <span style="font-size:12px; color:#6b7280;"><?= number_format($originalPrice) ?></span>
                                    <?php endif; ?>
                                </div>
                                <b style="font-size:14px;"><?= number_format($salePrice) ?></b>
                            </td>
                            <td class="text-center">
                                <?=($row['info_change_count'] ?? 0)?>
                            </td>
                            <td class="text-center">
                                <?=date('Y.m.d H:i', strtotime($row['updated_at']))?><br>
                                <?=date('Y.m.d H:i', strtotime($row['created_at']))?>
                            </td>
                            <td class="text-center">
                                <?php if( $row['last_price_changed_at'] ): ?>
                                    <?=date('Y-m-d', strtotime($row['last_price_changed_at']))?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if( ($row['sale_status'] ?? '') === '품절' ): ?>
                                    <span style="color:#dc2626; font-weight:700;">품절</span><br>
                                <?php endif; ?>
                                <?php if( $row['last_status_changed_at'] ): ?>
                                    <?=date('Y-m-d', strtotime($row['last_status_changed_at']))?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if( $row['last_changed_at'] ): ?>
                                    <?=date('Y-m-d', strtotime($row['last_changed_at']))?>
                                <?php endif; ?>
                            </td>
                            <?php
                                $rowPrimaryMatchIdx = (int)($row['primary_match_idx'] ?? ($row['match_idx'] ?? 0));
                                $rowMatchedItems = $row['matched_items'] ?? [];
                                if (!is_array($rowMatchedItems)) {
                                    $rowMatchedItems = [];
                                }
                                $rowMatchedCdIdxList = [];
                                foreach ($rowMatchedItems as $rowMatchedItem) {
                                    if (!is_array($rowMatchedItem)) {
                                        continue;
                                    }
                                    $matchedCdIdx = (int)($rowMatchedItem['cd_idx'] ?? 0);
                                    if ($matchedCdIdx > 0) {
                                        $rowMatchedCdIdxList[] = $matchedCdIdx;
                                    }
                                }
                                if ($rowPrimaryMatchIdx > 0 && !in_array($rowPrimaryMatchIdx, $rowMatchedCdIdxList, true)) {
                                    $rowMatchedCdIdxList[] = $rowPrimaryMatchIdx;
                                }
                                $rowMatchedCdIdxList = array_values(array_unique($rowMatchedCdIdxList));
                                $rowMatchedCount = count($rowMatchedCdIdxList);
                                $rowMatchedCdIdxCsv = implode(',', $rowMatchedCdIdxList);
                            ?>
                            <td class="text-left">
                                <button
                                    type="button"
                                    class="btnstyle1 btnstyle1-info btnstyle1-sm competitor-product-match-btn"
                                    data-competitor-idx="<?= (int)($row['idx'] ?? 0) ?>"
                                    data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>"
                                    data-competitor-image="<?= htmlspecialchars((string)($row['image_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-competitor-name="<?= htmlspecialchars((string)($row['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-existing-match-idx-list="<?= htmlspecialchars($rowMatchedCdIdxCsv, ENT_QUOTES, 'UTF-8') ?>"
                                    data-primary-match-idx="<?= $rowPrimaryMatchIdx ?>">
                                    <?= $rowMatchedCount > 0 ? ('매칭관리(' . number_format($rowMatchedCount) . ')') : '매칭' ?>
                                </button>
                                <?php if ($rowMatchedCount > 0) { ?>
                                    <div class="m-t-3">
                                        <span class="text-green">매칭완료</span>
                                    </div>
                                <?php } ?>
                            </td>

                            <td class="text-left">
                                <?php
                                    $displayMatchedCdIdxList = $rowMatchedCdIdxList;
                                    if (empty($displayMatchedCdIdxList) && $rowPrimaryMatchIdx > 0) {
                                        $displayMatchedCdIdxList[] = $rowPrimaryMatchIdx;
                                    }
                                    $displayMatchedCdIdxList = array_values(array_unique(array_map('intval', $displayMatchedCdIdxList)));
                                ?>
                                <?php if( !empty($displayMatchedCdIdxList) ): ?>
                                    <?php if (count($displayMatchedCdIdxList) > 1) { ?>
                                        <div class="m-b-4">
                                            <button
                                                type="button"
                                                class="btnstyle1 btnstyle1-danger btnstyle1-xs competitor-product-unmatch-all-btn"
                                                data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>">
                                                전체 해지
                                            </button>
                                        </div>
                                    <?php } ?>
                                    <?php foreach ($displayMatchedCdIdxList as $displayIdx => $matchedIdx) { ?>
                                        <?php
                                            $matchedProduct = $matchedProductMap[(int)$matchedIdx] ?? null;
                                            if (empty($matchedProduct)) {
                                                continue;
                                            }
                                            $isPrimary = ((int)$matchedIdx === (int)$rowPrimaryMatchIdx);
                                            if ((int)$rowPrimaryMatchIdx <= 0 && $displayIdx === 0) {
                                                $isPrimary = true;
                                            }
                                            $ourSalePrice = (int)($matchedProduct['cd_sale_price'] ?? 0);
                                            $ourCostPrice = (int)($matchedProduct['cd_cost_price'] ?? 0);
                                            $ourMarginGrade = trim((string)($matchedProduct['margin_grade'] ?? ''));
                                            $competitorPrice = (int)($row['price'] ?? 0);
                                            $adjustedSalePrice = $competitorPrice;

                                            $deliveryType = trim((string)($matchedProduct['delivery_type'] ?? 'small'));
                                            if ($deliveryType === 'tiny_80') {
                                                $deliveryType = 'tiny';
                                            }
                                            $deliveryFeeMap = [
                                                'tiny' => 2300,
                                                'small' => 2800,
                                                'medium' => 3300,
                                                'large' => 5000,
                                                'xlarge' => 5400,
                                            ];
                                            $deliveryFee = (int)($deliveryFeeMap[$deliveryType] ?? 2800);

                                            $calculateMarginInfo = function ($salePrice, $costPrice, $shippingFee, $useShippingDeduction = null) {
                                                $salePrice = (int)$salePrice;
                                                $costPrice = (int)$costPrice;
                                                $shippingFee = (int)$shippingFee;
                                                $shouldDeductShipping = is_bool($useShippingDeduction)
                                                    ? $useShippingDeduction
                                                    : ($salePrice > 29999);

                                                $marginAmount = $salePrice - $costPrice;
                                                if ($shouldDeductShipping) {
                                                    $marginAmount = $salePrice - ($costPrice + $shippingFee);
                                                }
                                                $marginRate = 0;
                                                if ($salePrice > 0) {
                                                    $marginRate = round(($marginAmount / $salePrice) * 100, 2);
                                                }

                                                $grade = '';
                                                if ($marginRate > 39) $grade = 'A';
                                                else if ($marginRate >= 35) $grade = 'B';
                                                else if ($marginRate >= 30) $grade = 'C';
                                                else if ($marginRate >= 25) $grade = 'D';
                                                else if ($marginRate >= 20) $grade = 'E';
                                                else if ($marginRate >= 15) $grade = 'F';
                                                else if ($marginRate >= 10) $grade = 'G';
                                                else if ($marginRate >= 5) $grade = 'H';
                                                else if ($marginRate > 0) $grade = 'I';

                                                return [
                                                    'margin_amount' => $marginAmount,
                                                    'margin_rate' => $marginRate,
                                                    'grade' => $grade,
                                                ];
                                            };

                                            // 비교 구간에서는 배송비 차감 기준을 현재 판매가 기준으로 고정해
                                            // 가격을 내렸을 때 마진이 역전 증가하는 케이스를 방지한다.
                                            $baseUseShippingDeduction = ($ourSalePrice > 29999);
                                            $currentMarginInfo = $calculateMarginInfo($ourSalePrice, $ourCostPrice, $deliveryFee, $baseUseShippingDeduction);
                                            $adjustedMarginInfo = $calculateMarginInfo($adjustedSalePrice, $ourCostPrice, $deliveryFee, $baseUseShippingDeduction);

                                            $priceDiff = $ourSalePrice - $competitorPrice;
                                            $priceDiffColor = '#111827';
                                            $priceDiffText = '동일';
                                            if ($priceDiff > 0) {
                                                $priceDiffColor = '#dc2626';
                                                $priceDiffText = '높음';
                                            } else if ($priceDiff < 0) {
                                                $priceDiffColor = '#2563eb';
                                                $priceDiffText = '낮음';
                                            }

                                            $adjustmentAmount = $adjustedSalePrice - $ourSalePrice;
                                            $adjustmentColor = '#111827';
                                            if ($adjustmentAmount < 0) {
                                                $adjustmentColor = '#dc2626';
                                            } else if ($adjustmentAmount > 0) {
                                                $adjustmentColor = '#2563eb';
                                            }
                                        ?>
                                        <div style="display:flex; align-items:flex-start; gap:7px; margin-top:<?= $displayIdx > 0 ? '10px' : '0' ?>; padding-top:<?= $displayIdx > 0 ? '10px' : '0' ?>; border-top:<?= $displayIdx > 0 ? '1px dashed #e5e7eb' : '0' ?>;">
                                            <div style="width:60px; min-width:60px;">
                                                <?php if( !empty($matchedProduct['img_path']) ){ ?>
                                                    <img src="<?= htmlspecialchars((string)($matchedProduct['img_path'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:60px; height:60px; object-fit:cover; border:1px solid #eee !important;">
                                                <?php } ?>
                                            </div>
                                            <div style="width:178px; min-width:178px; max-width:178px;">
                                                <div style="display:flex; align-items:center; justify-content:space-between; gap:6px;">
                                                    <span style="color:#6b7280;">브랜드: <?= htmlspecialchars((string)($matchedProduct['brand_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
                                                    <?php if ($rowMatchedCount > 1 && $isPrimary) { ?><b style="color:#2563eb; font-size:11px;">대표</b><?php } ?>
                                                </div>
                                                <div class="m-t-3" style="font-size:12px; white-space:normal;">
                                                    <?= htmlspecialchars((string)($matchedProduct['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                                <div class="m-t-3" style="font-size:12px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                                                    <span style="display:inline-flex; align-items:center; gap:10px; white-space:nowrap;">
                                                        <span>판매가: <b><?= number_format((int)($matchedProduct['cd_sale_price'] ?? 0)) ?></b></span>
                                                        <span style="color:#6b7280;">재고: <b><?= number_format((int)($matchedProduct['stock_qty'] ?? 0)) ?></b></span>
                                                    </span>
                                                </div>
                                                <div class="m-t-3" style="display:flex; align-items:center; gap:4px; flex-wrap:wrap;">
                                                    <button type="button" class="btnstyle1 btnstyle1-xs"
                                                        onclick="onlyAD.prdView('<?= (int)($matchedProduct['CD_IDX'] ?? 0) ?>','info');">#<?=$matchedProduct['CD_IDX'] ?? ''?> 상품보기</button>
                                                    <?php if (!$isPrimary) { ?>
                                                        <button
                                                            type="button"
                                                            class="btnstyle1 btnstyle1-xs competitor-product-set-primary-btn"
                                                            data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                            data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>"
                                                            data-match-idx="<?= (int)$matchedIdx ?>">
                                                            대표지정
                                                        </button>
                                                    <?php } ?>
                                                    <button
                                                        type="button"
                                                        class="btnstyle1 btnstyle1-danger btnstyle1-xs competitor-product-unmatch-one-btn"
                                                        data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                        data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>"
                                                        data-match-idx="<?= (int)$matchedIdx ?>">
                                                        해지
                                                    </button>
                                                </div>
                                            </div>
                                            <div style="border-left:1px solid #e5e7eb; padding-left:10px; margin-left:3px;">
                                                원가 : <?php if ($ourCostPrice > 0) { ?><b><?= number_format($ourCostPrice) ?></b><?php } else { ?>원가정보가 없습니다<?php } ?><br>
                                                <?php if ($ourCostPrice > 0) { ?>
                                                    마진그룹 : 마진율 <b><?= number_format((float)($currentMarginInfo['margin_rate'] ?? 0), 2) ?>%</b> / 그룹 <b><?= htmlspecialchars(($ourMarginGrade !== '' ? $ourMarginGrade : '-'), ENT_QUOTES, 'UTF-8') ?></b><br>
                                                    <?php if ($competitorPrice > 0) { ?>
                                                        가격차이 :
                                                        <span style="color:<?= $priceDiffColor ?>;">
                                                            <b>
                                                                <?php if ($priceDiff > 0) { ?>+<?php } ?>
                                                                <?= number_format($priceDiff) ?>
                                                            </b>
                                                        </span>
                                                        <?= $priceDiffText ?><br>
                                                        <?php if ($priceDiff !== 0) { ?>

                                                            <div>
                                                                조정가 :
                                                                <b style="color:<?= $adjustmentColor ?>;"><?= number_format($adjustedSalePrice) ?></b>
                                                                (<span><?php if ($adjustmentAmount > 0) { ?>+<?php } ?><?= number_format($adjustmentAmount) ?></span>)
                                                                <?php if( $matchedProduct['cd_godo_code'] !== '' ){ ?>
                                                                    <button
                                                                        type="button"
                                                                        class="btnstyle1 btnstyle1-danger btnstyle1-xs competitor-product-adjust-price-btn"
                                                                        data-prd-idx="<?= (int)($matchedProduct['CD_IDX'] ?? 0) ?>"
                                                                        data-adjusted-sale-price="<?= (int)$adjustedSalePrice ?>"
                                                                        data-cost-price="<?= (int)$ourCostPrice ?>"
                                                                        data-godo-code="<?= htmlspecialchars((string)($matchedProduct['cd_godo_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-competitor-site="<?= htmlspecialchars((string)($row['site'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-competitor-site-name="<?= htmlspecialchars($rowSiteName, ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-competitor-prd-pk="<?= (int)($row['prd_pk'] ?? 0) ?>"
                                                                        data-competitor-name="<?= htmlspecialchars((string)($row['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                                                        data-competitor-price="<?= (int)$competitorPrice ?>"
                                                                        data-competitor-detail-url="<?= htmlspecialchars((string)($row['detail_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                                                        이가격으로 조정
                                                                    </button>
                                                                <?php } ?>
                                                            </div>


                                                            마진금 <b><?= number_format((int)$currentMarginInfo['margin_amount']) ?></b> → <b><?= number_format((int)$adjustedMarginInfo['margin_amount']) ?></b> <br>
                                                            마진율 <b><?= number_format((float)($currentMarginInfo['margin_rate'] ?? 0), 2) ?>% → <?= number_format((float)($adjustedMarginInfo['margin_rate'] ?? 0), 2) ?>%</b> / 그룹 <b><?= htmlspecialchars(($ourMarginGrade !== '' ? $ourMarginGrade : '-'), ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars(($adjustedMarginInfo['grade'] !== '' ? $adjustedMarginInfo['grade'] : '-'), ENT_QUOTES, 'UTF-8') ?></b>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php else: ?>
                                    -
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
        <!--
        <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" id="supplierProductRegBtn">공급사상품 등록대기로 등록</button>
        -->
    </div>
</div>
<style>
    .match-layer-overlay {
        display: none;
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(17, 24, 39, 0.4);
        z-index: 90000001;
    }
    .match-layer-overlay.active {
        display: block;
    }
    .match-layer-box {
        width: 920px;
        max-width: calc(100% - 40px);
        max-height: calc(100% - 60px);
        overflow: hidden;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 20px 48px rgba(15, 23, 42, 0.3);
        margin: 30px auto;
        display: flex;
        flex-direction: column;
    }
    .match-layer-head {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .match-layer-target {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .match-layer-target-thumb {
        width: 60px;
        height: 60px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        object-fit: cover;
        background: #f3f4f6;
        flex: 0 0 60px;
    }
    .match-layer-body {
        padding: 14px 16px;
        overflow: auto;
    }
    .match-layer-search {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
        align-items: center;
    }
    .match-layer-search input[type="text"] {
        flex: 1;
        min-width: 200px;
    }
    .match-layer-search .match-brand-select-wrap {
        width: 220px;
        min-width: 220px;
    }
    .match-layer-search .select2-container {
        width: 100% !important;
    }
    .match-result-table {
        width: 100%;
        border-collapse: collapse;
    }
    .match-result-table th,
    .match-result-table td {
        border: 1px solid #e5e7eb;
        padding: 8px;
        vertical-align: middle;
    }
    .match-result-table th {
        background: #f8fafc;
    }
    .match-result-table tr.is-selected {
        background: #ecfdf3;
    }
    .match-thumbnail {
        width: 58px;
        height: 58px;
        border: 1px solid #e5e7eb;
        object-fit: cover;
        background: #f3f4f6;
    }
    .match-layer-foot {
        padding: 10px 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .match-pagination {
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .match-pagination .match-page-btn {
        min-width: 32px;
    }
    .match-pagination .match-page-btn.is-active {
        background-color: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }
</style>

<div id="competitorProductMatchLayer" class="match-layer-overlay" aria-hidden="true">
    <div class="match-layer-box">
        <div class="match-layer-head">
            <div class="match-layer-target">
                <img id="match_layer_competitor_image" class="match-layer-target-thumb" src="" alt="매칭 대상 상품 이미지" style="display:none;">
                <div>
                    <b>상품 매칭</b>
                    <div id="match_layer_competitor_name" style="margin-top:3px; color:#4b5563; font-size:12px;"></div>
                </div>
            </div>
            <button type="button" class="btnstyle1 btnstyle1-sm" id="matchLayerCloseBtn">닫기</button>
        </div>
        <div class="match-layer-body">
            <input type="hidden" id="match_competitor_idx" value="">
            <div class="match-layer-search">
                <div class="match-brand-select-wrap">
                    <select id="match_brand" class="dn-select2">
                        <option value="">브랜드</option>
                        <?php foreach ($brandForSelect ?? [] as $brand) { ?>
                            <option value="<?= (int)($brand['BD_IDX'] ?? 0) ?>">
                                <?= htmlspecialchars((string)($brand['BD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <input type="text" id="match_keyword" placeholder="상품명(CD_NAME) 검색">
                <button type="button" class="btnstyle1 btnstyle1-sm" id="matchKeywordClearBtn">지우기</button>
                <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="matchSearchBtn">검색</button>
            </div>
            <div id="match_result_status" style="margin-bottom:8px; color:#6b7280; font-size:12px;">검색어 또는 브랜드를 선택하고 검색해주세요.</div>
            <table class="match-result-table">
                <colgroup>
                    <col width="90px">
                    <col width="120px">
                    <col width="*">
                    <col width="180px">
                    <col width="90px">
                </colgroup>
                <thead>
                    <tr>
                        <th>썸네일</th>
                        <th>CD_IDX</th>
                        <th>상품명</th>
                        <th>브랜드</th>
                        <th>선택</th>
                    </tr>
                </thead>
                <tbody id="match_result_list">
                    <tr>
                        <td colspan="5" class="text-center">검색 결과가 없습니다.</td>
                    </tr>
                </tbody>
            </table>
            <div id="match_pagination" class="match-pagination"></div>
        </div>
        <div class="match-layer-foot">
            <div id="match_selected_info">선택된 상품 없음</div>
            <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="matchSelectDoneBtn">선택완료</button>
        </div>
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
    var $matchLayer = $("#competitorProductMatchLayer");
    var selectedMatchProduct = null;
    var currentMatchTarget = null;
    var isMatchProcessing = false;
    var matchSearchPage = 1;
    var MATCH_SCROLL_STORAGE_KEY = 'competitor_product_db_scroll_top';

    function saveScrollWrapPosition() {
        try {
            var $scrollWrap = $('.scroll-wrap').first();
            if (!$scrollWrap.length) {
                return;
            }
            sessionStorage.setItem(MATCH_SCROLL_STORAGE_KEY, String($scrollWrap.scrollTop() || 0));
        } catch (e) {}
    }

    function restoreScrollWrapPosition() {
        try {
            var savedTop = sessionStorage.getItem(MATCH_SCROLL_STORAGE_KEY);
            if (savedTop === null) {
                return;
            }
            var $scrollWrap = $('.scroll-wrap').first();
            if (!$scrollWrap.length) {
                return;
            }
            var scrollTop = parseInt(savedTop, 10);
            if (!isNaN(scrollTop) && scrollTop > 0) {
                $scrollWrap.scrollTop(scrollTop);
            }
            sessionStorage.removeItem(MATCH_SCROLL_STORAGE_KEY);
        } catch (e) {}
    }

    restoreScrollWrapPosition();

    $(document).on('click', '.supplier-site-btn', function() {
        var site = String($(this).data('site') || '');
        $('#s_site').val(site);
        $('.supplier-site-btn').removeClass('active');
        $(this).addClass('active');
        $("#searchBtn").trigger('click');
    });

    function escapeHtml(text) {
        return $("<div>").text(String(text || "")).html();
    }

    function decodeHtmlEntities(text) {
        return $("<textarea>").html(String(text || "")).text();
    }

    function parseMatchIdxList(rawValue) {
        var text = String(rawValue || "").trim();
        if (!text) {
            return [];
        }
        var parts = text.split(",");
        var parsed = [];
        for (var i = 0; i < parts.length; i++) {
            var n = Number($.trim(parts[i]));
            if (n > 0) {
                parsed.push(Math.round(n));
            }
        }
        parsed = Array.from(new Set(parsed));
        return parsed;
    }

    function closeMatchLayer() {
        $matchLayer.removeClass("active").attr("aria-hidden", "true");
    }

    function setSelectedMatchProduct(item) {
        selectedMatchProduct = item || null;
        if (!selectedMatchProduct) {
            $("#match_selected_info").html("선택된 상품 없음");
            return;
        }
        $("#match_selected_info").html(
            "선택됨: <b>#" + escapeHtml(selectedMatchProduct.cd_idx) + "</b> " + escapeHtml(selectedMatchProduct.cd_name)
        );
    }

    function renderMatchResults(items) {
        var $list = $("#match_result_list");
        if (!items || !items.length) {
            $list.html('<tr><td colspan="5" class="text-center">검색 결과가 없습니다.</td></tr>');
            return;
        }

        var html = "";
        for (var i = 0; i < items.length; i++) {
            var item = items[i] || {};
            var cdName = decodeHtmlEntities(item.cd_name);
            var brandName = decodeHtmlEntities(item.brand_name || "-");
            var thumb = item.thumbnail_url
                ? '<img src="' + escapeHtml(item.thumbnail_url) + '" class="match-thumbnail">'
                : '<div class="match-thumbnail" style="display:flex;align-items:center;justify-content:center;font-size:11px;color:#9ca3af;">NO IMG</div>';
            html += ''
                + '<tr class="match-result-row" data-cd-idx="' + escapeHtml(item.cd_idx) + '" data-cd-name="' + escapeHtml(cdName) + '" data-brand-name="' + escapeHtml(brandName) + '">'
                + '  <td class="text-center">' + thumb + '</td>'
                + '  <td class="text-center"><b>#' + escapeHtml(item.cd_idx) + '</b></td>'
                + '  <td><p  onclick="onlyAD.prdView(' + escapeHtml(item.cd_idx) + ',\'info\');" style="cursor:pointer;">' + escapeHtml(cdName) + '</p></td>'
                + '  <td>' + escapeHtml(brandName) + '</td>'
                + '  <td class="text-center"><button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs match-select-btn">선택</button></td>'
                + '</tr>';
        }
        $list.html(html);
    }

    function renderMatchPagination(pagination) {
        var $pagination = $("#match_pagination");
        var total = Number((pagination && pagination.total) || 0);
        var currentPage = Number((pagination && pagination.current_page) || 1);
        var lastPage = Number((pagination && pagination.last_page) || 1);

        if (total <= 0 || lastPage <= 1) {
            $pagination.empty();
            return;
        }

        var html = '';
        html += '<button type="button" class="btnstyle1 btnstyle1-xs match-page-btn" data-page="' + (currentPage - 1) + '" ' + (currentPage <= 1 ? 'disabled' : '') + '>이전</button>';

        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(lastPage, startPage + 4);
        startPage = Math.max(1, endPage - 4);
        for (var p = startPage; p <= endPage; p++) {
            html += '<button type="button" class="btnstyle1 btnstyle1-xs match-page-btn ' + (p === currentPage ? 'is-active' : '') + '" data-page="' + p + '">' + p + '</button>';
        }

        html += '<button type="button" class="btnstyle1 btnstyle1-xs match-page-btn" data-page="' + (currentPage + 1) + '" ' + (currentPage >= lastPage ? 'disabled' : '') + '>다음</button>';
        $pagination.html(html);
    }

    function submitCompetitorMatch(selectedProduct) {
        if (isMatchProcessing) {
            return;
        }
        if (!currentMatchTarget || !currentMatchTarget.site || !currentMatchTarget.prd_pk) {
            alert("매칭 대상 상품 정보가 없습니다.");
            return;
        }
        if (!selectedProduct || !selectedProduct.cd_idx) {
            alert("매칭할 상품을 선택해주세요.");
            return;
        }

        isMatchProcessing = true;
        $("#match_result_status").text("매칭 처리 중...");

        var existingIdxList = (currentMatchTarget && currentMatchTarget.existingMatchIdxList) ? currentMatchTarget.existingMatchIdxList : [];
        var mergedMatchIdxList = existingIdxList.slice();
        var selectedCdIdx = Number(selectedProduct.cd_idx || 0);
        if (selectedCdIdx > 0 && mergedMatchIdxList.indexOf(selectedCdIdx) < 0) {
            mergedMatchIdxList.push(selectedCdIdx);
        }
        if (!mergedMatchIdxList.length) {
            alert("매칭할 상품을 선택해주세요.");
            isMatchProcessing = false;
            return;
        }

        ajaxRequest('/admin/competitor/match', {
            site: currentMatchTarget.site,
            prd_pk: currentMatchTarget.prd_pk,
            action_mode: 'upsert_many',
            match_idx_list: mergedMatchIdxList,
            primary_cd_idx: selectedCdIdx
        })
            .done(function(res) {
                if (!(res && (res.success || res.status === 'success'))) {
                    alert(res && res.message ? res.message : '매칭 처리에 실패했습니다.');
                    return;
                }
                if (typeof toast2 === 'function') {
                    toast2('success', '경쟁사 매칭', '매칭이 성공적으로 완료되었습니다.');
                }
                $("#match_result_status").text("매칭 처리 완료");
                closeMatchLayer();
                saveScrollWrapPosition();
                location.reload();
            })
            .fail(function(res) {
                alert(res && res.message ? res.message : '매칭 처리 중 오류가 발생했습니다.');
            })
            .always(function() {
                isMatchProcessing = false;
            });
    }

    function submitCompetitorUnmatch(site, prdPk) {
        if (isMatchProcessing) {
            return;
        }
        if (!site || !prdPk) {
            alert("매칭 해지 대상 정보가 없습니다.");
            return;
        }
        if (!confirm("정말 매칭 해지 하시겠습니까?\n매칭해지 해도 다시 매칭이 가능합니다.")) {
            return;
        }

        isMatchProcessing = true;
        ajaxRequest('/admin/competitor/unmatch', {
            site: site,
            prd_pk: prdPk,
            action_mode: 'unmatch_all'
        })
            .done(function(res) {
                if (!(res && (res.success || res.status === 'success'))) {
                    alert(res && res.message ? res.message : '매칭 해지에 실패했습니다.');
                    return;
                }
                if (typeof toast2 === 'function') {
                    toast2('success', '경쟁사 매칭', '매칭이 해지되었습니다.');
                }
                saveScrollWrapPosition();
                location.reload();
            })
            .fail(function(res) {
                alert(res && res.message ? res.message : '매칭 해지 중 오류가 발생했습니다.');
            })
            .always(function() {
                isMatchProcessing = false;
            });
    }

    function submitCompetitorUnmatchOne(site, prdPk, matchIdx) {
        if (isMatchProcessing) {
            return;
        }
        if (!site || !prdPk || !matchIdx) {
            alert("매칭 해지 대상 정보가 없습니다.");
            return;
        }
        if (!confirm("선택한 매칭만 해지하시겠습니까?")) {
            return;
        }

        isMatchProcessing = true;
        ajaxRequest('/admin/competitor/unmatch', {
            site: site,
            prd_pk: prdPk,
            action_mode: 'unmatch_one',
            match_idx: matchIdx
        })
            .done(function(res) {
                if (!(res && (res.success || res.status === 'success'))) {
                    alert(res && res.message ? res.message : '선택 매칭 해지에 실패했습니다.');
                    return;
                }
                if (typeof toast2 === 'function') {
                    toast2('success', '경쟁사 매칭', '선택한 매칭이 해지되었습니다.');
                }
                saveScrollWrapPosition();
                location.reload();
            })
            .fail(function(res) {
                alert(res && res.message ? res.message : '선택 매칭 해지 중 오류가 발생했습니다.');
            })
            .always(function() {
                isMatchProcessing = false;
            });
    }

    function submitCompetitorSetPrimary(site, prdPk, matchIdx) {
        if (isMatchProcessing) {
            return;
        }
        if (!site || !prdPk || !matchIdx) {
            alert("대표 매칭 변경 대상 정보가 없습니다.");
            return;
        }

        isMatchProcessing = true;
        ajaxRequest('/admin/competitor/match', {
            site: site,
            prd_pk: prdPk,
            action_mode: 'set_primary',
            match_idx: matchIdx
        })
            .done(function(res) {
                if (!(res && (res.success || res.status === 'success'))) {
                    alert(res && res.message ? res.message : '대표 매칭 변경에 실패했습니다.');
                    return;
                }
                if (typeof toast2 === 'function') {
                    toast2('success', '경쟁사 매칭', '대표 매칭이 변경되었습니다.');
                }
                saveScrollWrapPosition();
                location.reload();
            })
            .fail(function(res) {
                alert(res && res.message ? res.message : '대표 매칭 변경 중 오류가 발생했습니다.');
            })
            .always(function() {
                isMatchProcessing = false;
            });
    }

    function searchMatchProducts(page) {
        var keyword = $.trim($("#match_keyword").val() || "");
        var brandIdx = Number($("#match_brand").val() || 0);
        var targetPage = Number(page || 1);
        if (targetPage <= 0) {
            targetPage = 1;
        }
        if (!keyword && brandIdx <= 0) {
            alert("검색어를 입력하거나 브랜드를 선택해주세요.");
            $("#match_keyword").focus();
            return;
        }

        matchSearchPage = targetPage;
        setSelectedMatchProduct(null);
        $("#match_result_status").text("검색 중...");
        ajaxRequest('/admin/competitor/search_product', {
            keyword: keyword,
            brand_idx: brandIdx > 0 ? brandIdx : '',
            page: targetPage,
            limit: 50
        })
            .done(function(res) {
                var items = (res && res.data && res.data.items) ? res.data.items : [];
                var pagination = (res && res.data && res.data.pagination) ? res.data.pagination : null;
                renderMatchResults(items);
                renderMatchPagination(pagination);
                if (pagination) {
                    $("#match_result_status").text("검색 결과 " + Number(pagination.total || 0) + "건 (" + Number(pagination.current_page || 1) + "/" + Number(pagination.last_page || 1) + " 페이지)");
                } else {
                    $("#match_result_status").text("검색 결과 " + items.length + "건");
                }
            })
            .fail(function(res) {
                $("#match_result_status").text("검색 실패");
                $("#match_pagination").empty();
                alert(res && res.message ? res.message : "검색 중 오류가 발생했습니다.");
            });
    }

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

    $(document).on("click", ".competitor-product-match-btn", function() {
        var $btn = $(this);
        var competitorIdx = Number($btn.data("competitor-idx") || 0);
        var competitorName = String($btn.data("competitor-name") || "").trim();
        var competitorSite = String($btn.data("competitor-site") || "").trim();
        var competitorPrdPk = Number($btn.data("competitor-prd-pk") || 0);
        var competitorImage = String($btn.data("competitor-image") || "").trim();
        var existingMatchIdxList = parseMatchIdxList($btn.data("existing-match-idx-list") || "");
        var primaryMatchIdx = Number($btn.data("primary-match-idx") || 0);

        $("#match_competitor_idx").val(competitorIdx);
        $("#match_layer_competitor_name").text(competitorName);
        if (competitorImage) {
            $("#match_layer_competitor_image").attr("src", competitorImage).show();
        } else {
            $("#match_layer_competitor_image").attr("src", "").hide();
        }
        $("#match_keyword").val(competitorName);
        $("#match_brand").val("").trigger("change");
        setSelectedMatchProduct(null);
        currentMatchTarget = {
            site: competitorSite,
            prd_pk: competitorPrdPk,
            name: competitorName,
            existingMatchIdxList: existingMatchIdxList,
            primaryMatchIdx: primaryMatchIdx
        };
        $matchLayer.addClass("active").attr("aria-hidden", "false");

        searchMatchProducts(1);
        $("#match_keyword").focus().select();
    });

    $("#matchLayerCloseBtn").on("click", function() {
        closeMatchLayer();
    });

    $("#matchSearchBtn").on("click", function() {
        searchMatchProducts(1);
    });

    $("#matchKeywordClearBtn").on("click", function() {
        $("#match_keyword").val("").focus();
    });

    $("#match_keyword").on("keydown", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            searchMatchProducts(1);
        }
    });

    $(document).on("click", ".match-page-btn", function() {
        var page = Number($(this).data("page") || 1);
        if ($(this).prop("disabled")) {
            return;
        }
        if (page === matchSearchPage) {
            return;
        }
        searchMatchProducts(page);
    });

    if ($.fn.select2 && !$("#match_brand").hasClass("select2-hidden-accessible")) {
        $("#match_brand").select2({
            width: "100%",
            placeholder: "브랜드",
            allowClear: true,
            dropdownParent: $("#competitorProductMatchLayer .match-layer-box")
        });
    }

    $(document).on("click", ".match-select-btn", function() {
        var $row = $(this).closest(".match-result-row");
        $(".match-result-row").removeClass("is-selected");
        $row.addClass("is-selected");

        setSelectedMatchProduct({
            cd_idx: Number($row.data("cd-idx") || 0),
            cd_name: String($row.data("cd-name") || ""),
            brand_name: String($row.data("brand-name") || "")
        });

        // 선택 즉시 매칭 처리
        submitCompetitorMatch(selectedMatchProduct);
    });

    $("#matchSelectDoneBtn").on("click", function() {
        submitCompetitorMatch(selectedMatchProduct);
    });

    $(document).on("click", ".competitor-product-unmatch-all-btn", function() {
        var site = String($(this).data("competitor-site") || "").trim();
        var prdPk = Number($(this).data("competitor-prd-pk") || 0);
        submitCompetitorUnmatch(site, prdPk);
    });

    $(document).on("click", ".competitor-product-unmatch-one-btn", function() {
        var site = String($(this).data("competitor-site") || "").trim();
        var prdPk = Number($(this).data("competitor-prd-pk") || 0);
        var matchIdx = Number($(this).data("match-idx") || 0);
        submitCompetitorUnmatchOne(site, prdPk, matchIdx);
    });

    $(document).on("click", ".competitor-product-adjust-price-btn", function() {
        var $btn = $(this);
        var prdIdx = Number($btn.data("prd-idx") || 0);
        var adjustedSalePrice = Number($btn.data("adjusted-sale-price") || 0);
        var costPrice = Number($btn.data("cost-price") || 0);
        var godoCode = String($btn.data("godo-code") || "").trim();
        var competitorSite = String($btn.data("competitor-site") || "").trim();
        var competitorSiteName = String($btn.data("competitor-site-name") || "").trim();
        var competitorPrdPk = Number($btn.data("competitor-prd-pk") || 0);
        var competitorName = String($btn.data("competitor-name") || "").trim();
        var competitorPrice = Number($btn.data("competitor-price") || 0);
        var competitorDetailUrl = String($btn.data("competitor-detail-url") || "").trim();

        if (prdIdx <= 0 || adjustedSalePrice <= 0 || costPrice <= 0 || !godoCode || !competitorSite || competitorPrdPk <= 0) {
            alert("가격 조정에 필요한 상품, 경쟁사 기준가, 원가 또는 고도몰 상품코드 정보가 없습니다.");
            return;
        }

        if (!confirm(
            "판매가를 " + adjustedSalePrice.toLocaleString() + "원으로 조정하고,\n"
            + "고도몰 상품 #" + godoCode + "의 판매가와 원가(" + costPrice.toLocaleString() + "원)를 업데이트할까요?"
        )) {
            return;
        }

        saveScrollWrapPosition();
        $btn.prop("disabled", true).text("업데이트 중...");
        $.ajax({
            url: "/admin/competitor/adjust_matched_product_price",
            type: "POST",
            dataType: "json",
            data: {
                prd_idx: prdIdx,
                adjusted_sale_price: adjustedSalePrice,
                cd_cost_price: costPrice,
                cd_godo_code: godoCode,
                competitor_site: competitorSite,
                competitor_site_name: competitorSiteName,
                competitor_prd_pk: competitorPrdPk,
                competitor_name: competitorName,
                competitor_price: competitorPrice,
                competitor_detail_url: competitorDetailUrl
            }
        }).done(function(res) {
            if (!(res && res.success)) {
                alert((res && res.message) ? res.message : "가격 업데이트에 실패했습니다.");
                return;
            }
            alert(res.message || "가격 업데이트를 완료했습니다.");
            location.reload();
        }).fail(function(xhr) {
            var response = xhr.responseJSON || {};
            alert(response.message || "가격 업데이트 중 오류가 발생했습니다.");
        }).always(function() {
            $btn.prop("disabled", false).text("이가격으로 조정");
        });
    });

    $(document).on("click", ".competitor-product-set-primary-btn", function() {
        var site = String($(this).data("competitor-site") || "").trim();
        var prdPk = Number($(this).data("competitor-prd-pk") || 0);
        var matchIdx = Number($(this).data("match-idx") || 0);
        submitCompetitorSetPrimary(site, prdPk, matchIdx);
    });

    $(document).on("click", ".competitor-detail-link-btn", function() {
        var detailUrl = String($(this).data("detail-url") || "").trim();
        if (!detailUrl) {
            alert("상세 링크가 없습니다.");
            return;
        }
        if (!/^https?:\/\//i.test(detailUrl)) {
            alert("올바른 상세 링크가 아닙니다.");
            return;
        }

        var popup = window.open(detailUrl, "_blank", "noopener,noreferrer");
        if (popup) {
            popup.opener = null;
        }
    });

    $(document).on("keydown", function(e) {
        if (e.key === "Escape" && $matchLayer.hasClass("active")) {
            closeMatchLayer();
        }
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
            's_keyword_mode': $("#s_keyword_mode").val(),
            's_keyword': $("#s_keyword").val(),
            's_status': $("#s_status").val(),
            's_limit': $("#s_limit").val(),
            's_sort_mode': $("#s_sort_mode").val(),
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
        location.href = '/admin/competitor/competitor_product_db' + (queryString ? '?' + queryString : '');
    });

    $("#resetBtn").on('click',function(){

        let s_site = $("#s_site").val();
        let s_match_status = $("#s_match_status").val();

        location.href = '/admin/competitor/competitor_product_db?s_site=' + s_site + '&s_match_status=' + s_match_status + '&s_limit=100';
    });

    $("#s_limit").on('change', function() {
        $("#searchBtn").trigger('click');
    });

    $("#s_status, #s_match_status, #s_keyword_mode").on('change', function() {
        $("#searchBtn").trigger('click');
    });

    $("#s_sort_mode").on('change', function() {
        $("#searchBtn").trigger('click');
    });



});

function updateSelectedCount() {
    var count = $('input[name="key_check[]"]:checked').length;
    $("#selected_product_count").text(count);
}
</script>   