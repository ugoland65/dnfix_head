<div id="contents_head">
	<h1>상품 재고</h1>
</div>
<div id="contents_body">
    <div id="contents_body_wrap" >

        <div class="table-wrap5 m-t-5">
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
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"><?=$paginationHtml?></div>
</div>