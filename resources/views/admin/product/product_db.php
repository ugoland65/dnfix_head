<div id="contents_head">
	<h1>상품 DB</h1>
</div>
<style>
    .product-kind-cell,
    .product-name-cell {
        cursor: context-menu;
        position: relative;
        transition: box-shadow 0.12s ease, background-color 0.12s ease;
    }
    .product-kind-cell.product-kind-selected,
    .product-name-cell.product-kind-selected {
        outline: 2px solid #2f6fed;
        outline-offset: -2px;
        background-color: #eef4ff;
        z-index: 1;
    }
    .product-kind-cell.product-kind-updated,
    .product-name-cell.product-kind-updated {
        outline: 2px solid #16a34a;
        outline-offset: -2px;
        background-color: #ecfdf3;
        z-index: 1;
    }
    .product-kind-context-layer {
        display: none;
        position: fixed;
        min-width: 280px;
        max-width: 340px;
        background: #fff;
        border: 1px solid #d9dce3;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        z-index: 10020;
        padding: 12px;
    }
    .product-kind-context-layer.active {
        display: block;
    }
</style>
<?php
    $categoryRows = (isset($categories) && is_array($categories)) ? $categories : [];
    $categoryPrimaryOptions = [];
    $categoryChildrenByPrimary = [];
    $categoryNameByKey = [];
    $categoryNameByCode = [];
    $categoryCodeByKey = [];
    foreach ($categoryRows as $categoryRow) {
        if (!is_array($categoryRow)) {
            continue;
        }
        $parentKey = trim((string)($categoryRow['key'] ?? ''));
        $parentName = trim((string)($categoryRow['name'] ?? ''));
        $parentCode = trim((string)($categoryRow['code'] ?? ''));
        if ($parentKey === '' || $parentName === '') {
            continue;
        }
        $categoryPrimaryOptions[] = [
            'key' => $parentKey,
            'name' => $parentName,
        ];
        $categoryNameByKey[$parentKey] = $parentName;
        if ($parentCode !== '') {
            $categoryCodeByKey[$parentKey] = $parentCode;
            $categoryNameByCode[$parentCode] = $parentName;
        }

        $children = (isset($categoryRow['children']) && is_array($categoryRow['children'])) ? $categoryRow['children'] : [];
        $childOptions = [];
        foreach ($children as $childRow) {
            if (!is_array($childRow)) {
                continue;
            }
            $childKey = trim((string)($childRow['key'] ?? ''));
            $childName = trim((string)($childRow['name'] ?? ''));
            $childCode = trim((string)($childRow['code'] ?? ''));
            if ($childKey === '' || $childName === '') {
                continue;
            }
            $childOptions[] = [
                'key' => $childKey,
                'name' => $childName,
            ];
            $categoryNameByKey[$childKey] = $childName;
            if ($childCode !== '') {
                $categoryCodeByKey[$childKey] = $childCode;
                $categoryNameByCode[$childCode] = $childName;
            }
        }
        $categoryChildrenByPrimary[$parentKey] = $childOptions;
    }
?>
<div id="contents_body">
    <div id="contents_body_wrap" >

        <!-- 검색 영역 -->
        <div class="top-search-wrap">
            <ul class="count-wrap">
                <span class="count">Total : <b><?=number_format($paginationArray['total']) ?></b></span>
                <span class="m-l-10"><b><?=$paginationArray['current_page']?></b></span>
                <span>/</span>
                <span><b><?=$paginationArray['last_page']?></b> page</span>
            </ul>
            <ul class="m-l-10">
				<select name="in_stock" id="in_stock" >
					<option value="all" <? if( $in_stock == 'all' ) echo "selected";?>>전체상품</option>
                    <option value="have" <? if( $in_stock == 'have' ) echo "selected";?>>재고보유</option>
                    <option value="no" <? if( $in_stock == 'no' ) echo "selected";?>>재고없음</option>
				</select>
			</ul>
			<ul class="">
				<select name="s_brand" id="s_brand" class="dn-select2">
					<option value="">브랜드</option>
					<?
					foreach( $brandForSelect as $brand ){
					?>
					<option value="<?=$brand['BD_IDX']?>" <? if( $brand['BD_IDX'] == ($s_brand ?? '') ) echo "selected";?> ><?=$brand['BD_NAME']?></option>
					<? } ?>
				</select>
			</ul>
            <ul>
                <select name="s_prd_kind" id="s_prd_kind" >
                    <option value="">상품분류</option>
                    <?
                    foreach( $prdKindSelect as $code => $name ){
                    ?>
                    <option value="<?=$code?>" <? if( $code == ($s_prd_kind ?? '') ) echo "selected";?> ><?=$name?></option>
                    <? } ?> 
                </select>
            </ul>
            <ul>
                <select name="s_prd_kind_second" id="s_prd_kind_second" style="display:none;">
                    <option value="">2차 카테고리</option>
                </select>
            </ul>

            <? /*
            <ul>
                <select name="s_work_task_code" id="s_work_task_code">
                    <option value="">작업체크 항목</option>
                    <?php foreach (($workTaskItemOptions ?? []) as $taskItem) { ?>
                        <?php $taskCode = (string)($taskItem['task_code'] ?? ''); ?>
                        <?php if ($taskCode === '') { continue; } ?>
                        <option value="<?= htmlspecialchars($taskCode, ENT_QUOTES, 'UTF-8') ?>" <?php if ($taskCode === (string)($s_work_task_code ?? '')) echo 'selected'; ?>>
                            <?= htmlspecialchars((string)($taskItem['task_label'] ?? $taskCode), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php } ?>
                </select>
            </ul>
            <ul>
                <select name="s_work_task_done" id="s_work_task_done">
                    <option value="">작업체크 상태</option>
                    <option value="Y" <?php if ((string)($s_work_task_done ?? '') === 'Y') echo 'selected'; ?>>완료</option>
                    <option value="N" <?php if ((string)($s_work_task_done ?? '') === 'N') echo 'selected'; ?>>미완료</option>
                </select>
            </ul>

            <ul>
                <select name="s_importing_country" id="s_importing_country" >
                    <option value="">수입국</option>
                    <?
                    foreach( $importingCountrySelect as $code => $name ){
                    ?>
                    <option value="<?=$code?>" <? if( $code == ($_s_importing_country ?? '') ) echo "selected";?> ><?=$name?></option>
                    <? } ?>
                </select>
            </ul>
            <ul>
                <select name="s_sale_mode" id="s_sale_mode" >
                    <option value="">할인모드</option>
                    <option value="monthly" <? if( $s_sale_mode == 'monthly' ) echo "selected";?> >월간할인</option>
                    <option value="special" <? if( $s_sale_mode == 'special' ) echo "selected";?> >특가할인</option>
                    <option value="sale_all" <? if( $s_sale_mode == 'sale_all' ) echo "selected";?> >할인전체</option>
                </select>
            </ul>

            <ul>
                <select name="s_margin_group" id="s_margin_group" >
                    <option value="">마진그룹 </option>
                    <?
                    $marginGroupSelect = [
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                        'E' => 'E',
                        'F' => 'F',
                        'G' => 'G',
                        'H' => 'H',
                        'I' => 'I',
                    ];
                    foreach( $marginGroupSelect as $code => $name ){
                    ?>
                    <option value="<?=$code?>" <? if( $code == ($s_margin_group ?? '') ) echo "selected";?> ><?=$name?></option>
                    <? } ?>
                </select>
            </ul>
            <ul>
                <input type='text' name='rack_code' id='rack_code' value="<?=$rack_code ?? '' ?>" placeholder="랙코드" style="width:80px;">
            </ul>
            */?>

            <ul>
                <select name="s_discontinued" id="s_discontinued" >
                    <option value="">단종여부</option>
                    <option value="1" <? if( $s_discontinued == '1' ) echo "selected";?> >단종</option>
                    <option value="0" <? if( $s_discontinued == '0' ) echo "selected";?> >미단종</option>
                </select>
            </ul>
            <ul>
                <input type='text' name='search_value' id='search_value' value="<?= $search_value?? '' ?>" placeholder="검색어" style="min-width: 200px;">
            </ul>
            <ul>
                <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" id="searchBtn">
                    <i class="fas fa-search"></i> 검색
                </button>
                <button type="button" class="btnstyle1 btnstyle1-sm" id="search_reset">
                    <i class="far fa-trash-alt"></i> 초기화
                </button>
            </ul>
            <ul class="right">
                <select name="sort_kind" id="sort_kind" >
                    <option value="stock" <? if( $sort_mode == "stock" ) echo "selected";?>>재고 많은순</option>
                    <option value="stock_asc" <? if( $sort_mode == "stock_asc" ) echo "selected";?>>재고 적은순</option>
                    <option value="idx" <? if( $sort_mode == "idx" ) echo "selected";?> >상품 등록순</option>
                    <option value="rack_code" <? if( $sort_mode == "rack_code" ) echo "selected";?> >랙코드순</option>
                    <option value="soldout" <? if( $sort_mode == "soldout" ) echo "selected";?> >품절일 최근순</option>
                    <option value="soldout_asc" <? if( $sort_mode == "soldout_asc" ) echo "selected";?> >품절일 오랜순</option>
                    <option value="price_desc" <? if( $sort_mode == "price_desc" ) echo "selected";?> >판매가 높은순</option>
                    <option value="price_asc" <? if( $sort_mode == "price_asc" ) echo "selected";?> >판매가 낮은순</option>
                    <option value="margin" <? if( $sort_mode == "margin" ) echo "selected";?> >마진율 높은순</option>
                    <option value="release_date" <? if( $sort_mode == "release_date" ) echo "selected";?> >출시일 최근순</option>
                    <option value="old_release_date" <? if( $sort_mode == "old_release_date" ) echo "selected";?> >출시일 오랜순</option>
                    <option value="old_sale_date" <? if( $sort_mode == "old_sale_date" ) echo "selected";?> >판매일 오랜순</option>
                    <option value="new_dis_date" <? if( $sort_mode == "old_dis_date" ) echo "selected";?> >할인일 최근</option>
                    <option value="old_dis_date" <? if( $sort_mode == "new_dis_date" ) echo "selected";?> >할인일 오랜순</option>
                </select>
            </ul>
        </div>

        <div id="list_new_wrap">
            <div class="table-wrap5">
                <div class="scroll-wrap">

                    <table class="table-st1">
                        <thead>
                            <tr class="list">
                                <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                                <th class="list-idx">고유번호</th>
                                <th>이미지</th>
                                <th>분류</th>
                                <th>상품명</th>
                                <th>브랜드</th>
                                <th>바코드</th>
                                <th>출시일</th>

                                <? /*
                                <th>랙코드</th>
                                */?>

                                <th>무게</th>
                                <th>패키지 사이즈</th>

                                <? /*
                                <th>수입국</th>
                                <th>할인모드</th>
                                <th>판매가</th>
                                <th>책정원가</th>
                                <th>마진율</th>
                                <th>마진등급</th>
                                <th>최근판매일</th>
                                <th>최근입고일</th>
                                <th>최근품절일</th>
                                <th>최근할인일</th>
                                */?>

                            </tr>
                        </thead>
                        <tbody>
                            <?

                                $_national_text['jp'] = "일본";
                                $_national_text['cn'] = "중국";
                                $_national_text['kr'] = "한국";

                                foreach ($productList as $product) {

                                    $img_path = "";

                                    if( $product['img_mode'] == 'out' ){
                                        $img_path = $product['CD_IMG'];
                                    }else{
                                        if( $product['CD_IMG'] ){
                                            $img_path = '/data/comparion/'.$product['CD_IMG'];
                                        }
                                    }

                                    /*
                                    if( $list['cd_sale_price'] > 0 && $list['cd_cost_price'] > 0 ){
                                        if( $_sort_kind == "margin" ){
                                            $_margin_per = round($list['margin_per'],2);
                                        }else{ 
                                            if( $list['cd_sale_price'] < 29999 ){
                                                $_margin_per =  round( ($list['cd_sale_price'] - $list['cd_cost_price'] ) / $list['cd_sale_price'] * 100, 2);
                                            }else{
                                                $_margin_per =  round( ($list['cd_sale_price'] - ($list['cd_cost_price'] + 2500) ) / $list['cd_sale_price'] * 100, 2);
                                            }
                                        }
                                    }
                                    */

                                    $_margin_per = round($product['margin_per'],2) ?? 0;

                                    if( $product['cd_sale_price'] > 0 && $product['cd_cost_price'] > 0 ){
                                        if( $product['cd_sale_price'] < 29999 ){
                                            $_margin_per =  round( ($product['cd_sale_price'] - $product['cd_cost_price'] ) / $product['cd_sale_price'] * 100, 2);
                                        }else{
                                            $_margin_per =  round( ($product['cd_sale_price'] - ($product['cd_cost_price'] + 2500) ) / $product['cd_sale_price'] * 100, 2);
                                        }
                                    }

                                    // 등급 계산 (40% 기준, 5단위)
                                    $grade = '';
                                    $gradeColor = '';
                                    if ($_margin_per > 39) {
                                        $grade = 'A';
                                        $gradeColor = '#28a745'; // 초록색
                                    } elseif ($_margin_per >= 35) {
                                        $grade = 'B';
                                        $gradeColor = '#20c997'; // 연두색
                                    } elseif ($_margin_per >= 30) {
                                        $grade = 'C';
                                        $gradeColor = '#17a2b8'; // 청록색
                                    } elseif ($_margin_per >= 25) {
                                        $grade = 'D';
                                        $gradeColor = '#0dcaf0'; // 하늘색
                                    } elseif ($_margin_per >= 20) {
                                        $grade = 'E';
                                        $gradeColor = '#ffc107'; // 노란색
                                    } elseif ($_margin_per >= 15) {
                                        $grade = 'F';
                                        $gradeColor = '#fd7e14'; // 오렌지색
                                    } elseif ($_margin_per >= 10) {
                                        $grade = 'G';
                                        $gradeColor = '#dc3545'; // 빨간색
                                    } elseif ($_margin_per >= 5) {
                                        $grade = 'H';
                                        $gradeColor = '#d63384'; // 진한 빨강
                                    } elseif ($_margin_per > 0) {
                                        $grade = 'I';
                                        $gradeColor = '#6c757d'; // 회색
                                    }
                            ?>
                                <tr>
                                    <td><input type="checkbox" name="check_idx[]" value="<?=$product['CD_IDX']?>"></td>
                                    <td class="text-center"><?=$product['CD_IDX']?></td>
                                    <td class="p-5">
                                        <p onclick="onlyAD.prdView('<?=$product['CD_IDX']?>','info');" style="cursor:pointer;" ><img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;"></p>
                                    </td>
                                    <?php
                                        $primaryCategoryName = trim((string)($product['prd_kind_name'] ?? '미지정'));
                                        $secondaryCategoryName = trim((string)($product['cd_category_name'] ?? ''));
                                        $currentKindCode = trim((string)($product['CD_KIND_CODE'] ?? ''));
                                        $currentCategoryCode = trim((string)($product['CD_CATEGORY_CODE'] ?? ''));
                                        $hasSecondaryCategory = $currentCategoryCode !== ''
                                            && $secondaryCategoryName !== ''
                                            && $secondaryCategoryName !== $primaryCategoryName;
                                    ?>
                                    <td class="text-center product-kind-cell"
                                        data-prd-idx="<?= (int)($product['CD_IDX'] ?? 0) ?>"
                                        data-current-kind="<?= htmlspecialchars($currentKindCode, ENT_QUOTES, 'UTF-8') ?>"
                                        data-current-category-code="<?= htmlspecialchars($currentCategoryCode, ENT_QUOTES, 'UTF-8') ?>">
                                        <?php
                                        ?>
                                        <span class="product-kind-primary"><?= htmlspecialchars($primaryCategoryName, ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="product-kind-secondary-wrap" style="<?= $hasSecondaryCategory ? '' : 'display:none;' ?>">
                                            <br><b class="product-kind-secondary"><?= htmlspecialchars($secondaryCategoryName, ENT_QUOTES, 'UTF-8') ?></b>
                                        </span>
                                    </td>

                                    <td class="product-name-cell"
                                        data-prd-idx="<?= (int)($product['CD_IDX'] ?? 0) ?>"
                                        data-current-memo2="<?= htmlspecialchars((string)($product['cd_memo2'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                        <?php if( $product['is_sale_month'] ){ ?>
                                            <label class="on_sale_label xs monthly">월간할인</label>
                                        <?php } ?>
                                        <?php if( $product['is_sale_special'] ){ ?>
                                            <label class="on_sale_label xs special">특가할인</label>
                                        <?php } ?>
                                        <?php if( $product['is_discontinued'] ){ ?>
                                            <label class="on_sale_label xs discontinued">단종</label>
                                        <?php } ?>

                                        <p onclick="onlyAD.prdView('<?=$product['CD_IDX']?>','info');" style="cursor:pointer;" ><b><?=$product['CD_NAME']?></b></p>
                                        <div class="m-t-3 prd-memo-wrap" style="color:#ff0000;<?= empty($product['cd_memo2']) ? 'display:none;' : '' ?>">
                                            <span class="prd-memo">- <?= htmlspecialchars((string)($product['cd_memo2'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <a href="/admin/product/product_db?s_brand=<?=$product['CD_BRAND_IDX']?>"><?=$product['brand_name']?></a>
                                        <?php if( $product['CD_BRAND2_IDX'] ){ ?>
                                            <br>
                                            <a href="/admin/product/product_db?s_brand=<?=$product['CD_BRAND2_IDX']?>"><?=$product['brand2_name']?></a>
                                        <?php } ?>
                                    </td>
                                    <td><?=$product['barcode']?></td>

                                    <!-- 출시일 -->
                                    <td class="text-center">
                                        <?php
                                            $releaseDate = trim((string)($product['CD_RELEASE_DATE'] ?? ''));
                                            $isValidReleaseDate = $releaseDate !== '' && $releaseDate !== '0000-00-00' && $releaseDate !== '0000-00-00 00:00:00';
                                        ?>
                                        <?php if( $isValidReleaseDate ){ ?>
                                            <?= date('Y-m-d', strtotime($releaseDate)) ?>
                                        <?php }else{ ?>
                                            -
                                        <?php } ?>
                                    </td>

                                    <? /*
                                    <!-- 랙코드 -->
                                    <td class="text-center"><?=$product['ps_rack_code']?></td>
                                    */?>

                                    <td class="text-center">
                                        <?php if( $product['weight'] ){ ?>
                                            <b><?=number_format($product['weight'])?></b>g
                                        <?php }else{ ?>
                                            -
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            if( 
                                                !empty($product['cd_size_fn']['package']['W']) && 
                                                !empty($product['cd_size_fn']['package']['H']) && 
                                                !empty($product['cd_size_fn']['package']['D']) 
                                            ){
                                        ?>
                                        <div>
                                            <?php
                                                /*
                                                <ul class="text-center" style="font-size:12px;"><?=number_format($product['package_volume'])?>cm³</ul>
                                                */
                                            ?>
                                            <ul><b><?=round($product['package_volume_m3'],3)?></b>m³</ul>
                                            <ul style="font-size:11px;" class="m-t-3"><?=$product['cd_size_fn']['package']['W']?> x <?=$product['cd_size_fn']['package']['H']?> x <?=$product['cd_size_fn']['package']['D']?></ul>
                                            <ul class="m-t-3"><b style="font-size:14px;"><?=$product['package_volume_level']?></b></ul>
                                        </div>
                                        <?php
                                            }else{
                                                echo '-';
                                            }
                                        ?>
                                    </td>

                                    <? /*
                                    <td class="text-center"><?=$_national_text[$product['cd_national']]?></td>
                                    <td class="text-center">
                                        <?=$product['is_sale_month'] ? '월간할인' : ($product['is_sale_special'] ? '특가할인' : '할인전체')?>
                                    </td>
                                    <td class="text-right"><?=number_format($product['cd_sale_price'])?></td>
                                    <td class="text-right"><?=number_format($product['cd_cost_price'])?></td>
                                    <td class="text-right"><b><?=$_margin_per?>%</b></td>
                                    <td class="text-center">
                                        <?php if (!empty($grade)) { ?>
                                            <span class="grade-badge grade-<?=$grade?>">
                                                <?=$grade?>
                                            </span>
                                        <?php } else { ?>
                                            -
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            $lastSaleDate = $product['ps_last_date'] ?? null;
                                            if (
                                                !empty($lastSaleDate) &&
                                                $lastSaleDate !== '0000-00-00 00:00:00' &&
                                                $lastSaleDate !== '0000-00-00' &&
                                                ($ts = strtotime($lastSaleDate)) // strtotime 실패하면 false
                                            ) {
                                                echo date('y.m.d', $ts);
                                            } else {
                                                echo '-';
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            $inDate = $product['ps_in_date'] ?? null;
                                            if (
                                                !empty($inDate) &&
                                                $inDate !== '0000-00-00 00:00:00' &&
                                                $inDate !== '0000-00-00' &&
                                                ($ts = strtotime($inDate)) // strtotime 실패하면 false
                                            ) {
                                                echo date('y.m.d', $ts);
                                            } else {
                                                echo '-';
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            $soldoutDate = $product['ps_soldout_date'] ?? null;
                                            if( $product['ps_stock'] < 1 ){
                                                if (
                                                    !empty($soldoutDate) &&
                                                    $soldoutDate !== '0000-00-00 00:00:00' &&
                                                    $soldoutDate !== '0000-00-00' &&
                                                    ($ts = strtotime($soldoutDate)) // strtotime 실패하면 false
                                                ) {
                                                    echo date('y.m.d', $ts);
                                                } else {
                                                    echo '-';
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            $saleDate = $product['ps_sale_date'] ?? null;
                                            if (
                                                !empty($saleDate) &&
                                                $saleDate !== '0000-00-00 00:00:00' &&
                                                $saleDate !== '0000-00-00' &&
                                                ($ts = strtotime($saleDate)) // strtotime 실패하면 false
                                            ) {
                                        ?>
                                            <div>
                                                <ul class="text-center"><?=date('y.m.d', $ts)?></ul>
                                                <ul class="text-center m-t-5" style="font-size:12px;">총 할인수 : <?=$product['last_sale']['sale_count'] ?? 0?></ul>
                                                <ul class="text-center" style="font-size:11px;"><?=$product['last_sale']['sale_subject'] ?? ''?></ul>
                                                <ul class="text-center"><?=$product['last_sale']['sale_per'] ?? 0?>%</ul>
                                            </div>
                                        <?php
                                            } else {
                                                echo '-';
                                            }
                                        ?>
                                    </td>
                                    */?>

                                </tr>
                            <? } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"><?=$paginationHtml?></div>
	<div class="m-l-20">
		선택된 상품 <span id="selected_product_count">0</span>
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" id="workRequestBtn">선택상품 업무요청</button>
		<button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" id="groupingBtn">선택상품 그룹핑</button>
	</div>
</div>
<div id="productCategoryContextLayer" class="product-kind-context-layer" aria-hidden="true">
    <div style="font-weight:700; margin-bottom:8px;">상품 분류 수정</div>
    <div style="margin-bottom:8px;">
        <select id="quick_category_primary" style="width:100%;">
            <option value="">1차 카테고리 선택</option>
        </select>
    </div>
    <div id="quick_category_secondary_wrap" style="display:none; margin-bottom:10px;">
        <select id="quick_category_secondary" style="width:100%;">
            <option value="">2차 카테고리 선택</option>
        </select>
    </div>
    <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btnstyle1 btnstyle1-sm" id="quick_category_cancel_btn">취소</button>
        <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="quick_category_save_btn">저장</button>
    </div>
</div>
<div id="productMemoContextLayer" class="product-kind-context-layer" aria-hidden="true">
    <div style="font-weight:700; margin-bottom:8px;">리스트 메모 수정</div>
    <div style="margin-bottom:10px;">
        <input type="text" id="quick_memo2_input" style="width:100%;" placeholder="리스트 메모 입력">
    </div>
    <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btnstyle1 btnstyle1-sm" id="quick_memo2_cancel_btn">취소</button>
        <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="quick_memo2_save_btn">저장</button>
    </div>
</div>
<script type="text/javascript">
const PRODUCT_CATEGORY_PRIMARY_OPTIONS = <?= json_encode($categoryPrimaryOptions ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const PRODUCT_CATEGORY_CHILDREN_BY_PRIMARY = <?= json_encode($categoryChildrenByPrimary ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const PRODUCT_CATEGORY_NAME_BY_KEY = <?= json_encode($categoryNameByKey ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const PRODUCT_CATEGORY_NAME_BY_CODE = <?= json_encode($categoryNameByCode ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const PRODUCT_CATEGORY_CODE_BY_KEY = <?= json_encode($categoryCodeByKey ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

function select_all() {
		var checkboxes = document.getElementsByName('check_idx[]');
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

	function updateSelectedCount() {
		$('input[name="check_idx[]"]').each(function() {
			$(this).closest('tr').toggleClass('selected-row', $(this).is(':checked'));
		});
		var count = $('input[name="check_idx[]"]:checked').length;
		$("#selected_product_count").text(count);
	}

	// 검색 파라미터 수집 공통 함수
	function getSearchParams(additionalParams) {
		var params = {};

		// 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
		var fields = {
			's_prd_kind': $("#s_prd_kind").val(),
            's_prd_kind_second': $("#s_prd_kind_second").val(),
			'search_value': $("#search_value").val(),
			's_brand': $("#s_brand").val(),
			's_importing_country': $("#s_importing_country").val(),
			's_margin_group': $("#s_margin_group").val(),
            's_sale_mode': $("#s_sale_mode").val(),
            's_work_task_code': $("#s_work_task_code").val(),
            's_work_task_done': $("#s_work_task_done").val(),
			'sort_mode': $("#sort_kind").val(),
			's_discontinued': $("#s_discontinued").val(),
            'rack_code': $("#rack_code").val(),
            'in_stock': $("#in_stock").val(),
		};

		// 추가 파라미터가 있으면 병합
		if (additionalParams) {
			fields = Object.assign(fields, additionalParams);
		}

		// 유효한 값만 params에 추가
		for (var key in fields) {
			if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '') {
				params[key] = fields[key];
			}
		}

		return params;
	}

	// 검색 파라미터로 페이지 이동
	function navigateWithParams(params) {
		// URL 쿼리 문자열 생성
		var queryString = Object.keys(params)
			.map(function(key) {
				return key + '=' + encodeURIComponent(params[key]);
			})
			.join('&');

		// 페이지 이동
		location.href = '/admin/product/product_db' + (queryString ? '?' + queryString : '');
	}

    $(function(){
        var $categoryLayer = $('#productCategoryContextLayer');
        var $memoLayer = $('#productMemoContextLayer');
        var $selectedCategoryCell = null;
        var $selectedMemoCell = null;
        var $updatedCategoryCell = null;

        function setUpdatedCategoryCell($cell) {
            if ($updatedCategoryCell && $updatedCategoryCell.length) {
                $updatedCategoryCell.removeClass('product-kind-updated');
            }
            $updatedCategoryCell = $cell && $cell.length ? $cell : null;
            if ($updatedCategoryCell && $updatedCategoryCell.length) {
                $updatedCategoryCell.addClass('product-kind-updated');
            }
        }

        function clearUpdatedCategoryCell() {
            if ($updatedCategoryCell && $updatedCategoryCell.length) {
                $updatedCategoryCell.removeClass('product-kind-updated');
            }
            $updatedCategoryCell = null;
        }

        function closeCategoryLayer() {
            $categoryLayer.removeClass('active').hide().attr('aria-hidden', 'true');
            if ($selectedCategoryCell && $selectedCategoryCell.length) {
                $selectedCategoryCell.removeClass('product-kind-selected');
            }
            $selectedCategoryCell = null;
        }

        function closeMemoLayer() {
            $memoLayer.removeClass('active').hide().attr('aria-hidden', 'true');
            if ($selectedMemoCell && $selectedMemoCell.length) {
                $selectedMemoCell.removeClass('product-kind-selected');
            }
            $selectedMemoCell = null;
        }

        function positionLayer($layer, clientX, clientY) {
            $layer.css({ left: 0, top: 0, display: 'block', visibility: 'hidden' });
            var layerWidth = $layer.outerWidth();
            var layerHeight = $layer.outerHeight();
            var viewportWidth = $(window).width();
            var viewportHeight = $(window).height();

            var left = clientX;
            var top = clientY;
            if (left + layerWidth > viewportWidth - 10) {
                left = viewportWidth - layerWidth - 10;
            }
            if (top + layerHeight > viewportHeight - 10) {
                top = viewportHeight - layerHeight - 10;
            }

            left = Math.max(10, left);
            top = Math.max(10, top);

            $layer.css({
                left: left + 'px',
                top: top + 'px',
                visibility: 'visible'
            }).addClass('active').attr('aria-hidden', 'false');
        }

        function fillPrimarySelect(selectedKind) {
            var $primary = $('#quick_category_primary');
            $primary.empty().append('<option value="">1차 카테고리 선택</option>');
            (PRODUCT_CATEGORY_PRIMARY_OPTIONS || []).forEach(function(item) {
                if (!item || !item.key) {
                    return;
                }
                $primary.append($('<option>', {
                    value: item.key,
                    text: item.name || item.key
                }));
            });
            $primary.val(String(selectedKind || '').trim());
        }

        function fillSecondarySelect(primaryKind, selectedSecondKind) {
            var kind = String(primaryKind || '').trim();
            var childOptions = Array.isArray(PRODUCT_CATEGORY_CHILDREN_BY_PRIMARY[kind]) ? PRODUCT_CATEGORY_CHILDREN_BY_PRIMARY[kind] : [];
            var $wrap = $('#quick_category_secondary_wrap');
            var $secondary = $('#quick_category_secondary');

            $secondary.empty().append('<option value="">2차 카테고리 선택</option>');
            if (childOptions.length === 0) {
                $wrap.hide();
                return;
            }

            childOptions.forEach(function(item) {
                if (!item || !item.key) {
                    return;
                }
                $secondary.append($('<option>', {
                    value: item.key,
                    text: item.name || item.key
                }));
            });

            $secondary.val(String(selectedSecondKind || '').trim());
            $wrap.show();
        }

        function findSecondKindByCategoryCode(primaryKind, categoryCode) {
            var kind = String(primaryKind || '').trim();
            var code = String(categoryCode || '').trim();
            if (!kind || !code) {
                return '';
            }
            var childOptions = Array.isArray(PRODUCT_CATEGORY_CHILDREN_BY_PRIMARY[kind]) ? PRODUCT_CATEGORY_CHILDREN_BY_PRIMARY[kind] : [];
            for (var i = 0; i < childOptions.length; i++) {
                var child = childOptions[i] || {};
                var childKey = String(child.key || '').trim();
                if (childKey && String(PRODUCT_CATEGORY_CODE_BY_KEY[childKey] || '').trim() === code) {
                    return childKey;
                }
            }
            return '';
        }

        function fillSearchSecondarySelect(resetSelection) {
            var primaryKind = String($('#s_prd_kind').val() || '').trim();
            var $second = $('#s_prd_kind_second');
            var childOptions = Array.isArray(PRODUCT_CATEGORY_CHILDREN_BY_PRIMARY[primaryKind]) ? PRODUCT_CATEGORY_CHILDREN_BY_PRIMARY[primaryKind] : [];

            $second.empty().append('<option value="">2차 카테고리</option>');
            if (childOptions.length === 0) {
                $second.val('');
                $second.hide();
                return;
            }

            childOptions.forEach(function(item) {
                if (!item || !item.key) {
                    return;
                }
                $second.append($('<option>', {
                    value: item.key,
                    text: item.name || item.key
                }));
            });

            if (resetSelection) {
                $second.val('');
            } else {
                var currentVal = String('<?= htmlspecialchars((string)($s_prd_kind_second ?? ''), ENT_QUOTES, 'UTF-8') ?>');
                $second.val(currentVal);
            }
            $second.show();
        }

        function openCategoryLayer($cell, clientX, clientY) {
            var prdIdx = Number($cell.data('prd-idx') || 0);
            if (prdIdx <= 0) {
                return;
            }

            closeMemoLayer();
            closeCategoryLayer();
            $selectedCategoryCell = $cell;
            $selectedCategoryCell.addClass('product-kind-selected');

            var currentKind = String($cell.data('current-kind') || '').trim();
            var currentCategoryCode = String($cell.data('current-category-code') || '').trim();
            var secondKind = findSecondKindByCategoryCode(currentKind, currentCategoryCode);

            fillPrimarySelect(currentKind);
            fillSecondarySelect(currentKind, secondKind);
            positionLayer($categoryLayer, clientX, clientY);
        }

        function openMemoLayer($cell, clientX, clientY) {
            var prdIdx = Number($cell.data('prd-idx') || 0);
            if (prdIdx <= 0) {
                return;
            }

            closeCategoryLayer();
            closeMemoLayer();
            $selectedMemoCell = $cell;
            $selectedMemoCell.addClass('product-kind-selected');

            var currentMemo = String($cell.data('current-memo2') || '').trim();
            $('#quick_memo2_input').val(currentMemo);

            positionLayer($memoLayer, clientX, clientY);
            $('#quick_memo2_input').trigger('focus').trigger('select');
        }

        function applyProductMemo2($cell, memoText) {
            var normalizedMemo = String(memoText || '').trim();
            $cell.data('current-memo2', normalizedMemo);
            $cell.attr('data-current-memo2', normalizedMemo);

            var $memoWrap = $cell.find('.prd-memo-wrap');
            var $memoText = $cell.find('.prd-memo');
            if (normalizedMemo === '') {
                $memoText.text('');
                $memoWrap.hide();
                return;
            }

            $memoText.text('- ' + normalizedMemo);
            $memoWrap.show();
        }
        
        $(".dn-select2").select2();
        fillSearchSecondarySelect(false);
        $('#s_prd_kind').on('change', function() {
            fillSearchSecondarySelect(true);
        });
        
        // 개별 체크박스 선택 시 행 배경색 변경
        $(document).on('change', 'input[name="check_idx[]"]', function() {
            if($(this).is(':checked')) {
                $(this).closest('tr').addClass('selected-row');
            } else {
                $(this).closest('tr').removeClass('selected-row');
            }
            updateSelectedCount();
        });
        
        // 초기 선택 개수 업데이트
        updateSelectedCount();

        $("#search_reset").click(function(){
            var url = "?";
            window.location.href = url;
        });

        $("#sort_kind").change(function(){
            // 정렬 모드 추가하여 검색 파라미터 수집
            var params = getSearchParams({
                'sort_mode': $(this).val()
            });
            
            // 페이지 이동
            navigateWithParams(params);
        });

        $("#searchBtn").on('click',function(){
            // 검색 파라미터 수집
            var params = getSearchParams();
            
            // 페이지 이동
            navigateWithParams(params);
        });

        $("#search_value").on('keydown', function(e){
            if (e.key === 'Enter') {
                e.preventDefault();
                $("#searchBtn").trigger('click');
            }
        });

        $(document).on('contextmenu', '.product-kind-cell', function(e) {
            e.preventDefault();
            openCategoryLayer($(this), e.clientX, e.clientY);
        });

        $(document).on('contextmenu', '.product-name-cell', function(e) {
            e.preventDefault();
            openMemoLayer($(this), e.clientX, e.clientY);
        });

        $('#quick_category_primary').on('change', function() {
            fillSecondarySelect($(this).val(), '');
        });

        $('#quick_category_cancel_btn').on('click', function() {
            closeCategoryLayer();
        });

        $('#quick_memo2_cancel_btn').on('click', function() {
            closeMemoLayer();
        });

        $('#quick_category_save_btn').on('click', function() {
            if (!$selectedCategoryCell || !$selectedCategoryCell.length) {
                closeCategoryLayer();
                return;
            }

            var prdIdx = Number($selectedCategoryCell.data('prd-idx') || 0);
            var primaryKind = String($('#quick_category_primary').val() || '').trim();
            var secondaryKind = String($('#quick_category_secondary').val() || '').trim();

            if (!primaryKind) {
                alert('1차 카테고리를 선택해주세요.');
                return;
            }

            var categoryCode = '';
            if (secondaryKind) {
                categoryCode = String(PRODUCT_CATEGORY_CODE_BY_KEY[secondaryKind] || '').trim();
            }
            if (!categoryCode) {
                categoryCode = String(PRODUCT_CATEGORY_CODE_BY_KEY[primaryKind] || '').trim();
            }

            var payload = {
                action_mode: 'update_product_category',
                prd_idx: prdIdx,
                cd_kind_code: primaryKind,
                cd_kind_code_second: secondaryKind,
                cd_category_code: categoryCode
            };

            ajaxRequest('/admin/product/action', payload)
                .done(function(res) {
                    if (!(res && res.success)) {
                        alert(res && res.message ? res.message : '분류 수정에 실패했습니다.');
                        return;
                    }

                    var primaryName = String(PRODUCT_CATEGORY_NAME_BY_KEY[primaryKind] || primaryKind);
                    var primaryCode = String(PRODUCT_CATEGORY_CODE_BY_KEY[primaryKind] || '').trim();
                    var savedCategoryCode = String((res && res.data && res.data.cd_category_code) ? res.data.cd_category_code : categoryCode).trim();
                    var secondaryName = String(PRODUCT_CATEGORY_NAME_BY_CODE[savedCategoryCode] || '').trim();
                    var hasSecondary = savedCategoryCode !== '' && savedCategoryCode !== primaryCode && secondaryName !== '';

                    $selectedCategoryCell.data('current-kind', primaryKind);
                    $selectedCategoryCell.attr('data-current-kind', primaryKind);
                    $selectedCategoryCell.data('current-category-code', savedCategoryCode);
                    $selectedCategoryCell.attr('data-current-category-code', savedCategoryCode);
                    $selectedCategoryCell.find('.product-kind-primary').text(primaryName);

                    var $secondaryWrap = $selectedCategoryCell.find('.product-kind-secondary-wrap');
                    var $secondary = $selectedCategoryCell.find('.product-kind-secondary');
                    if (hasSecondary) {
                        $secondary.text(secondaryName);
                        $secondaryWrap.show();
                    } else {
                        $secondary.text('');
                        $secondaryWrap.hide();
                    }

                    setUpdatedCategoryCell($selectedCategoryCell);
                    closeCategoryLayer();
                })
                .fail(function(res) {
                    alert(res && res.message ? res.message : '서버 통신에 실패했습니다.');
                });
        });

        function saveQuickMemo2() {
            if (!$selectedMemoCell || !$selectedMemoCell.length) {
                closeMemoLayer();
                return;
            }

            var prdIdx = Number($selectedMemoCell.data('prd-idx') || 0);
            if (prdIdx <= 0) {
                closeMemoLayer();
                return;
            }

            var nextMemo = String($('#quick_memo2_input').val() || '').trim();
            ajaxRequest('/admin/product/action', {
                action_mode: 'update_product_memo2',
                prd_idx: prdIdx,
                cd_memo2: nextMemo
            })
                .done(function(res) {
                    if (!(res && res.success)) {
                        alert(res && res.message ? res.message : '리스트 메모 저장에 실패했습니다.');
                        return;
                    }
                    applyProductMemo2($selectedMemoCell, nextMemo);
                    setUpdatedCategoryCell($selectedMemoCell);
                    closeMemoLayer();
                })
                .fail(function(res) {
                    alert(res && res.message ? res.message : '서버 통신에 실패했습니다.');
                });
        }

        $('#quick_memo2_save_btn').on('click', function() {
            saveQuickMemo2();
        });

        $('#quick_memo2_input').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveQuickMemo2();
            }
        });

        $(document).on('click', function(e) {
            clearUpdatedCategoryCell();
            var $target = $(e.target);
            if (
                !$target.closest('#productCategoryContextLayer').length &&
                !$target.closest('#productMemoContextLayer').length &&
                !$target.closest('.product-kind-cell').length &&
                !$target.closest('.product-name-cell').length
            ) {
                closeCategoryLayer();
                closeMemoLayer();
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCategoryLayer();
                closeMemoLayer();
            }
        });

        $(window).on('scroll resize', function() {
            closeCategoryLayer();
            closeMemoLayer();
        });

		// 선택상품 업무요청
		$("#workRequestBtn").on('click', function() {
			var selectedItems = [];
			$('input[name="check_idx[]"]:checked').each(function() {
				selectedItems.push($(this).val());
			});

			if (selectedItems.length === 0) {
				alert('업무요청할 상품을 선택해주세요.');
				return;
			}

			var pks = selectedItems.join(',');
			var url = '/admin/work/TaskRequest/create?category=' + encodeURIComponent('업무요청')
				+ '&withdb=' + encodeURIComponent('product_db')
				+ '&pks=' + encodeURIComponent(pks);
			location.href = url;
		});


		// 선택상품 그룹핑
		$("#groupingBtn").on('click', function() {
			var selectedItems = [];
			$('input[name="check_idx[]"]:checked').each(function() {
				selectedItems.push($(this).val());
			});

			if (selectedItems.length === 0) {
				alert('그룹핑할 상품을 선택해주세요.');
				return;
			}

			onlyAD.prdGrouping('product_db', selectedItems);
			
		});

    });
</script>