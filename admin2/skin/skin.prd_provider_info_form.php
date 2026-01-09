<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\ProductController;

$productController = new ProductController(); 

$viewData = $productController->prdProviderInfoIndex();

$prd_data = $viewData['productPartnerInfo'];

?>
<form id="prd_provider_info_form">
<input type="hidden" name="prd_idx" value="<?=$prd_data['idx']?>">
<table class="table-style ">
    <colgroup>
        <col width="170px"/>
        <col  />
    </colgroup>
    <tr>
        <td colspan="2" class="none-bg title">
            <h1>상품 기본정보</h1>
        </td>
    </tr>
    <tbody>
        <tr>
            <th>상품 구분</th>
            <td>
                <select name="kind">
                    <option value=''>상품 구분 선택</option>
                <? foreach($koedge_prd_kind_array as $kind){ ?>
                    <option value="<?=$kind['code']?>" <? if($prd_data['kind'] == $kind['code'] ) echo "selected"; ?>><?=$kind['name']?></option>
                <? } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>브랜드</th>
            <td>
                <select name="brand_idx">
                    <option value=''>브랜드 선택</option>
                    <?
                        foreach ($viewData['brandForSelect'] as $brand) {
                    ?>
                    <option value='<?=$brand['BD_IDX']?>'<? if( $brand['BD_IDX'] == $prd_data['brand_idx'] ) echo "selected"; ?>><?=$brand['BD_NAME']?></option>
                    <? } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>공급사</th>
            <td>
                <select name="partner_idx">
                    <option value=''>공급사 선택</option>
                    <?
                        foreach ($viewData['partnerForSelect'] as $partner) {
                    ?>
                    <option value='<?=$partner['idx']?>'<? if( $partner['idx'] == $prd_data['partner_idx'] ) echo "selected"; ?>><?=$partner['name']?></option>
                    <? } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>상품코드</th>
            <td><input type='text' name='code'  size='40' value="<?=$prd_data['code']?>" style="width:150px;"></td>
        </tr>
        <tr>
            <th>판매 상품명</th>
            <td><input type='text' name='name'  size='40' value="<?=$prd_data['name']?>" ></td>
        </tr>
        <tr>
            <th>공급사 상품명</th>
            <td><input type='text' name='name_p'  size='40' value="<?=$prd_data['name_p']?>" ></td>
        </tr>
        <tr>
            <th>판매가</th>
            <td><?=number_format($prd_data['sale_price'])?></td>
        </tr>
        <tr>
            <th>상품원가</th>
            <td>
                <?=number_format($prd_data['cost_price'])?><br>
                <?php
                    if( !empty($prd_data['sale_price']) && !empty($prd_data['cost_price']) ){
                        $margin = $prd_data['sale_price'] - $prd_data['cost_price'];
                        $margin_rate = $margin / $prd_data['sale_price'] * 100;
                ?>
                    마진 : <?=number_format($margin)?>원 / 마진율 : <?=number_format($margin_rate, 2)?>%
                <?php
                    }
                ?>
            </td>
        </tr>
        <tr>
            <th>주문가</th>
            <td>
                <input type='text' name='order_price'  size='40' value="<?=number_format($prd_data['order_price'])?>" style="width:150px;" class="comma-input">
                <?php
                    if( !empty($prd_data['sale_price']) && !empty($prd_data['order_price']) ){
                        $margin = $prd_data['sale_price'] - $prd_data['order_price'];
                        $margin_rate = $margin / $prd_data['sale_price'] * 100;
                ?>
                    마진 : <?=number_format($margin)?>원 / 마진율 : <?=number_format($margin_rate, 2)?>%
                <?php
                    }
                ?>
            </td>
        </tr>
        <tr>
            <th>부가세</th>
            <td>
                <label><input type="radio" name="is_vat" value="Y" <? if($prd_data['price_data']['is_vat'] == 'Y' ) echo "checked"; ?>> 포함</label>
                <label><input type="radio" name="is_vat" value="N" <? if($prd_data['price_data']['is_vat'] == 'N' ) echo "checked"; ?>> 미포함</label>
            </td>
        </tr>
        <tr>
            <th>주문가 상세</th>
            <td>
                상품원가 : <b><?=number_format($prd_data['price_data']['cost_price'] ?? 0)?></b> + 
                상품 부가세 : <b><?=number_format($prd_data['price_data']['vat'] ?? 0)?></b> + 
                배송비 : <input type='text' name='delivery_fee'  size='40' value="<?=number_format($prd_data['price_data']['delivery_fee'] ?? 0)?>" style="width:150px;" class="comma-input">
                <? /*
                <div class="admin-guide-text">
                    - 원가를 비워두고 주문가 + 배송비를 입력하면 부가세 10% 자동계산되어 원가가 입력됩니다.<br>
                    - 원가를 입력하면 자동계산되지 않습니다.
                </div>
                */ ?>

            </td>
        </tr>

        <tr>
            <th>메모</th>
            <td><input type='text' name='memo' value="<?=$prd_data['memo']?>" ></td>
        </tr>
    <tbody>

    <?php
        if( $prd_data['kind'] == "ONAHOLE" ){
    ?>
    <tbody>
        <tr>
            <td colspan="2" class="none-bg" style="height:10px;"></td>
        </tr>
        <tr>
            <td colspan="2" class="none-bg title">
                <h1>HBTI</h1>
            </td>
        </tr>
    </tbody>

    <tbody>
        <tr>
            <th>HBTI</th>
            <td>
            <select name="hbti_type" id="hbti_type" >
                <option value="">HBTI 선택</option>
                <?
                foreach($hbtiTypes as $hbtiType){
                ?>
                <option value="<?=$hbtiType?>" <? if( ($prd_data['hbti_type'] ?? '') == $hbtiType ) echo "selected";?>><?=$hbtiType?></option>
                <? } ?>
            </select>
            </td>
        </tr>
    </tbody>
    <?php
        }
    ?>


    <tbody>
        <tr>
            <td colspan="2" class="none-bg" style="height:10px;"></td>
        </tr>
        <tr>
            <td colspan="2" class="none-bg title">
                <div>
                    <ul>
                        <h1>고도몰</h1>
                    </ul>

                    <?php
                        if( !empty($prd_data['godo_goodsNo']) ){
                    ?>
                    <ul class="right">
                        <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" 
                            onclick="goGodoMall(<?=$prd_data['godo_goodsNo']?>);" >고도몰 상품보기</button>

                        <button type="button" id="loadGodoGoodsInfoBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm" 
                            data-prd-idx="<?=$prd_data['idx']?>"
                            data-godo-goods-no="<?=$prd_data['godo_goodsNo']?>"
                          >
                            고도몰 정보로 반영
                        </button>
                    </ul>
                    <?php
                        }
                    ?>
                </div>
            </td>
        </tr>
    </tbody>

    <tbody>
        <tr>
            <th>고도몰 상품코드</th>
            <td><input type='text' name='godo_goodsNo'  size='10' value="<?=$prd_data['godo_goodsNo']?>" style="width:150px;"></td>
        </tr>
        <tr>
            <th>공급사 매칭코드</th>
            <td><input type='text' name='matching_code'  size='10' value="<?=$prd_data['matching_code']?>" style="width:150px;"></td>
        </tr>
        <tr>
            <th>판매가 ( 고도몰 등록가격)</th>
            <td><input type='text' name='sale_price'  size='40' value="<?=number_format($prd_data['sale_price'])?>" style="width:150px;" class="comma-input"></td>
        </tr>


        <?php
            $godoOptionNames = $prd_data['godo_option']['name'] ?? [];
            $godoOptionItems = $prd_data['godo_option']['items'] ?? [];
            if( !empty($prd_data['godo_is_option']) && is_array($godoOptionNames) && is_array($godoOptionItems) ){
        ?>
        <tr>
            <th>고도몰 옵션</th>
            <td>
                <table class="table-style ">
                    <colgroup>
                        <col width="100px"/>
                        <col width="150px"/>
                        <col width="100px"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>옵션번호</th>
                            <?php foreach($godoOptionNames as $optionName){ ?>
                            <th><?=$optionName?></th>
                            <?php } ?>
                            <th>옵션가격</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($godoOptionItems as $i => $option){ ?>
                    <tr>
                        <td><?=$option['optionNo']?></td>
                        <?php
                            $valueNum = 0;
                            foreach($godoOptionNames as $optionName){
                                $valueNum++;
                                $keyName = 'optionValue'.$valueNum;
                        ?>
                        <td><?=$option[$keyName] ?? ''?></td>
                        <?php
                            }
                        ?>
                        <td class="text-right"><?=number_format($option['optionPrice'] ?? 0)?></td>
                        <td></td>
                    </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php
            }
        ?>
        <tr>
            <th>이미지 모드</th>
            <td>
                <label><input type="radio" name="img_mode" value="out" <? if($prd_data['img_mode'] == 'out' ) echo "checked"; ?>> 외부 이미지</label>
                <label><input type="radio" name="img_mode" value="this" <? if($prd_data['img_mode'] == 'this' ) echo "checked"; ?>> 서버에 등록</label>
            </td>
        </tr>
        <tr>
            <th>이미지 URL</th>
            <td><input type='text' name='img_src'  size='40' value="<?=$prd_data['img_src']?>" ></td>
        </tr>
    </tbody>

    <tbody>
        <tr>
            <td colspan="2" class="none-bg" style="height:10px;"></td>
        </tr>
        <tr>
            <td colspan="2" class="none-bg title">
                <h1>공급사</h1>
            </td>
        </tr>
    </tbody>

    <tbody>
        <tr>
            <th>공급사 사이트</th>
            <td><input type='text' name='supplier_site'  size='10' value="<?=$prd_data['supplier_site']?>" style="width:150px;"></td>
        </tr>
        <tr>
            <th>공급사 사이트 고유번호</th>
            <td><input type='text' name='supplier_prd_pk'  size='10' value="<?=$prd_data['supplier_prd_pk']?>" style="width:150px;"></td>
        </tr>
        <tr>
            <th>공급사 매칭 번호</th>
            <td><input type='text' name='supplier_prd_idx'  size='10' value="<?=$prd_data['supplier_prd_idx']?>" style="width:150px;"></td>
        </tr>
        <tr>
            <th>상품원가 (공급사 제공가격)</th>
            <td><input type='text' name='cost_price'  size='40' value="<?=number_format($prd_data['cost_price'])?>" style="width:150px;" class="comma-input"></td>
        </tr>

        <?php
            if( !empty($prd_data['matching_option']) ){
        ?>
        <tr>
            <th>공급사 매칭 옵션</th>
            <td><input type='text' name='matching_option'  size='10' value="<?=$prd_data['matching_option']?>" style="width:300px;"></td>
        </tr>
        <?php
            }
        ?>

        <tr>
            <th>공급사 이미지 모드</th>
            <td>
                <label><input type="radio" name="supplier_img_mode" value="out" <? if($prd_data['supplier_img_mode'] == 'out' ) echo "checked"; ?>> 외부 이미지</label>
                <label><input type="radio" name="supplier_img_mode" value="this" <? if($prd_data['supplier_img_mode'] == 'this' ) echo "checked"; ?>> 서버에 등록</label>
            </td>
        </tr>
        <tr>
            <th>공급사 이미지 URL</th>
            <td><input type='text' name='supplier_img_src'  size='40' value="<?=$prd_data['supplier_img_src']?>" ></td>
        </tr>
    </tbody>

    
</table>
</form>

<? if( !empty($prd_data['idx']) ){ ?>
<div class="button-wrap-back">
</div>
<div class="button-wrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdProviderInfo.save()" >상품수정</button>
</div>
<? } ?>