<div id="contents_head">
	<h1>상품 재고</h1>
</div>
<div id="contents_body">
    <div id="contents_body_wrap" >

        <!-- 검색 영역 -->
        <div class="top-search-wrap">
			<ul class="">
				<select name="s_brand" id="s_brand" class="dn-select2">
					<option value="">전체 브랜드</option>
					<?
					foreach( $brandForSelect as $brand ){
					?>
					<option value="<?=$brand['BD_IDX']?>" <? if( $brand['BD_IDX'] == ($_s_brand ?? '') ) echo "selected";?> ><?=$brand['BD_NAME']?></option>
					<? } ?>
				</select>
			</ul>
            <ul>
                <select name="s_prd_kind" id="s_prd_kind" >
                    <option value="">전체 상품</option>
                    <?
                    foreach( $prdKindSelect as $code => $name ){
                    ?>
                    <option value="<?=$code?>" <? if( $code == ($_s_prd_kind ?? '') ) echo "selected";?> ><?=$name?></option>
                    <? } ?> 
                </select>
            </ul>
            <ul>
                <select name="s_importing_country" id="s_importing_country" >
                    <option value="">전체 수입국</option>
                    <?
                    foreach( $importingCountrySelect as $code => $name ){
                    ?>
                    <option value="<?=$code?>" <? if( $code == ($_s_importing_country ?? '') ) echo "selected";?> ><?=$name?></option>
                    <? } ?>
                </select>
            </ul>
            <ul>
                <input type='text' name='search_value' id='search_value' value="<?= $_GET['search_value'] ?? '' ?>" placeholder="검색어" style="min-width: 300px;">
            </ul>
            <ul>
                <button type="button" class="btn btnstyle1 btnstyle1-info btnstyle1-sm" id="searchBtn">
                    <i class="fas fa-search"></i> 검색
                </button>
                <button type="button" class="btnstyle1 btnstyle1-sm" id="search_reset">
                    <i class="far fa-trash-alt"></i> 초기화
                </button>
            </ul>
        </div>

        <div id="list_new_wrap">
            <div class="list-top">
                <span class="count">Total : <b><?=number_format($paginationArray['total']) ?></b></span>
                <span class="m-l-20"><b><?=$paginationArray['current_page']?></b></span>
                <span> / </span>
                <span><b><?=$paginationArray['last_page']?></b> page</span>

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
                            ?>
                                <tr>
                                    <td><input type="checkbox" name="check_idx[]" value="<?=$product['CD_IDX']?>"></td>
                                    <td class="text-center"><?=$product['CD_IDX']?></td>
                                    <td class="text-center"><?=$product['ps_idx']?></td>
                                    <td class="p-5">
                                        <p onclick="onlyAD.prdView('<?=$product['CD_IDX']?>','info');" style="cursor:pointer;" ><img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;"></p>
                                    </td>
                                    <td class="text-center"><?=$product['prd_kind_name']?></td>
                                    <td>
                                        <p onclick="onlyAD.prdView('<?=$product['CD_IDX']?>','info');" style="cursor:pointer;" ><b><?=$product['CD_NAME']?></b></p>
                                    </td>
                                    <td class="text-center">
                                        <?=$product['brand_name']?>
                                        <?php if( $product['CD_BRAND2_IDX'] ){ ?>
                                            <br>
                                            <?=$product['brand2_name']?>
                                        <?php } ?>
                                    </td>
                                    <td><?=$product['cd_code_fn']['jan']?></td>
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
                                    <td class="text-right"><?=$_margin_per?>%</td>
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
</div>

<script type="text/javascript">
<!--

    $(function(){
        $(".dn-select2").select2();
    });

    $("#sort_kind").change(function(){

        var url = "?sort_mode=" + $(this).val();
        window.location.href = url;

    });

    $("#search_reset").click(function(){
        var url = "?";
        window.location.href = url;
    });

//--> 
</script>