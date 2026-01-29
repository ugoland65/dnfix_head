<div id="contents_head">
	<h1>상품 재고</h1>
</div>
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
            <ul>
                <input type='text' name='search_value' id='search_value' value="<?= $_GET['search_value'] ?? '' ?>" placeholder="검색어" style="min-width: 200px;">
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
                                <th class="list-idx">재고코드</th>
                                <th>이미지</th>
                                <th>분류</th>
                                <th>상품명</th>
                                <th>브랜드</th>
                                <th>바코드</th>
                                <th>재고</th>
                                <th>랙코드</th>
                                <th>무게</th>
                                <th>패키지 사이즈</th>
                                <th>수입국</th>
                                <th>판매가</th>
                                <th>책정원가</th>
                                <th>마진율</th>
                                <th>마진등급</th>
                                <th>최근판매일</th>
                                <th>최근입고일</th>
                                <th>최근품절일</th>
                                <th>최근할인일</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?

                                $_national_text['jp'] = "일본";
                                $_national_text['cn'] = "중국";
                                $_national_text['kr'] = "한국";

                                foreach ($productList as $product) {

                                    $img_path = "";
                                    if( $product['CD_IMG'] ){
                                        $img_path = '/data/comparion/'.$product['CD_IMG'];
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
                                    <td><input type="checkbox" name="check_idx[]" value="<?=$product['ps_idx']?>"></td>
                                    <td class="text-center"><?=$product['CD_IDX']?></td>
                                    <td class="text-center"><?=$product['ps_idx']?></td>
                                    <td class="p-5">
                                        <p onclick="onlyAD.prdView('<?=$product['CD_IDX']?>','info');" style="cursor:pointer;" ><img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;"></p>
                                    </td>
                                    <td class="text-center"><?=$product['prd_kind_name']?></td>
                                    <td>
                                        <p onclick="onlyAD.prdView('<?=$product['CD_IDX']?>','info');" style="cursor:pointer;" ><b><?=$product['CD_NAME']?></b></p>
                                        <?php if( !empty($product['cd_memo2']) ){ ?>
                                            <div class="m-t-3" style="color:#ff0000"><span class="prd-memo">- <?=$product['cd_memo2']?></span></div>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <?=$product['brand_name']?>
                                        <?php if( $product['CD_BRAND2_IDX'] ){ ?>
                                            <br>
                                            <?=$product['brand2_name']?>
                                        <?php } ?>
                                    </td>
                                    <td><?=$product['barcode']?></td>
                                    <td class="text-center">
                                        <?php if( $product['ps_stock'] == 0 ){ ?>
                                            <span style="color:#ff0000;">재고없음</span>
                                        <?php }else{ ?>
                                            <b style="font-size:15px; color:#5e41ff;"><?=number_format($product['ps_stock'])?></b>
                                        <?php } ?>
                                        <?php if( $product['ps_stock_hold'] > 0 ){ ?>
                                            <br><b style="font-size:14px; color:#999;"><?=number_format($product['ps_stock_hold'])?></b>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center"><?=$product['ps_rack_code']?></td>
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
                                    <td class="text-center"><?=$_national_text[$product['cd_national']]?></td>
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
        선택된 상품 <span id="selected_product_count"></span>
        <input type='text' name='rack_change_code' id='rack_change_code' value="" placeholder="변경할 랙코드" style="width:80px;">
        <button type="button" class="btn btnstyle1 btnstyle1-info btnstyle1-sm" id="rackChangeBtn">랙 일괄변경</button>
    </div>
</div>

<style>
    tr.selected-row {
        background-color: #e3f2fd !important;
    }
    tr.selected-row:hover {
        background-color: #bbdefb !important;
    }
</style>

<script>
    $(function(){
        $(".dn-select2").select2();
        
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
    });
    
    // 선택된 상품 개수 업데이트
    function updateSelectedCount() {
        var count = $('input[name="check_idx[]"]:checked').length;
        $('#selected_product_count').text(count + '개');
    }

    // 검색 파라미터 수집 공통 함수
    function getSearchParams(additionalParams) {
        var params = {};
        
        // 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
        var fields = {
            's_site': $("#s_site").val(),
            's_brand': $("#s_brand").val(),
            's_prd_kind': $("#s_prd_kind").val(),
            's_importing_country': $("#s_importing_country").val(),
            's_margin_group': $("#s_margin_group").val(),
            'rack_code': $("#rack_code").val(),
            'in_stock': $("#in_stock").val(),
            'search_value': $("#search_value").val(),
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
        location.href = '/admin/product/product_stock' + (queryString ? '?' + queryString : '');
    }

    // 전체 선택 함수 수정
    function select_all() {
        var checkboxes = document.getElementsByName('check_idx[]');
        var selectAll = event.target.checked;
        
        for(var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = selectAll;
            
            // 행 배경색 변경
            if(selectAll) {
                $(checkboxes[i]).closest('tr').addClass('selected-row');
            } else {
                $(checkboxes[i]).closest('tr').removeClass('selected-row');
            }
        }
        updateSelectedCount();
    }



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

    // 랙코드 일괄 변경
    $("#rackChangeBtn").on('click', function() {
        // 선택된 상품 확인
        var selectedItems = [];
        $('input[name="check_idx[]"]:checked').each(function() {
            selectedItems.push($(this).val());
        });

        if (selectedItems.length === 0) {
            alert('변경할 상품을 선택해주세요.');
            return;
        }

        // 랙코드 확인
        var rackCode = $('#rack_change_code').val().trim();
        if (rackCode === '') {
            alert('변경할 랙코드를 입력해주세요.');
            $('#rack_change_code').focus();
            return;
        }

        // 확인 메시지
        if (!confirm(selectedItems.length + '개 상품의 랙코드를 "' + rackCode + '"(으)로 변경하시겠습니까?')) {
            return;
        }

        // AJAX POST 요청
        $.ajax({
            url: '/admin/product/proc/rack_change_batch',
            type: 'POST',
            data: {
                check_idx: selectedItems,
                rack_code: rackCode
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message || '랙코드가 변경되었습니다.');
                    location.reload();
                } else {
                    alert(response.message || '랙코드 변경에 실패했습니다.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('랙코드 변경 중 오류가 발생했습니다.');
            }
        });
    });

</script>