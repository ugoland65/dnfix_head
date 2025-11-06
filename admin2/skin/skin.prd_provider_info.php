<?php
/*
=================================================================================
뷰 파일: admin2/skin/skin.prd_provider_info.php
호출경로 : /ad/prd/prd_provider_info
설명: 상품 공급사 상세 화면
작성자: Lion65
수정일: 2025-03-30
=================================================================================

GET

CONTROLLER
/application/Controllers/Admin/ProductController.php

*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\RequestHandler;
use App\Controllers\Admin\ProductController;

$requestHandler = new RequestHandler();
$requestData = $requestHandler->getAll();

$productController = new ProductController(); 

$viewData = $productController->prdProviderInfoIndex();

$prd_data = $viewData['productPartnerInfo'];

$img_path = $prd_data['img_src'];

include ($docRoot."/admin2/layout/header_popup.php");
?>

<div class="prd-quick-left">

    <div class="prd-img">
		<img src="<?=$img_path?>" style="height:150px; border:1px solid #eee !important;">
	</div>

	<div class="prd-quick-info">
		<ul class="prd-brand-name"><?=$prd_data['brand_name'] ?? ''?></ul>
		<ul class="prd-name"><b><?=$prd_data['name'] ?? ''?></b></ul>
        <ul class="prd-stock-code m-t-10"><b><?=$prd_data['code'] ?? ''?></b></ul>

        <?php
            if( !empty($prd_data['godo_is_option']) ){
        ?>
        <ul class="prd-option m-t-10">옵션있음</ul>
        <?php
            }
        ?>
        <ul class="prd-memo m-t-10"><?=$prd_data['memo'] ?? ''?></ul>
    </div>

    <?php
        if( !empty($prd_data['supplier_img_src']) ){
    ?>
    <div class="prd-img">
		<img src="<?=$prd_data['supplier_img_src']?>" style="height:150px; border:1px solid #eee !important;">
	</div>
    <?php
        }
    ?>

    <?php
        if( !empty($prd_data['supplier_site']) && !empty($prd_data['supplier_prd_pk']) ){
    ?>
    <div class="prd-quick-info">
		<ul class="prd-name"><b><?=$prd_data['name_p'] ?? ''?></b></ul>
        <?php
            if( !empty($prd_data['matching_option']) ){
        ?>
        <ul class="">옵션 : <?=$prd_data['matching_option'] ?? ''?></ul>
        <?php
            }
        ?>
        <ul>
            <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm cancel-match-btn"
                data-db1-idx="<?=$prd_idx?>" 
                data-db2-idx="<?=$prd_data['supplier_prd_idx']?>"
            >매칭취소</button>
        </ul>
    </div>
    <?php
        }
    ?>

	<div class="crm-menu m-t-10">
		<ul class="<? if( $requestData['vmode'] == 'info' ) echo "active" ?>" data-mode="info">상품정보</ul>
		<ul class="<? if( $requestData['vmode'] == 'match' ) echo "active" ?>" data-mode="match">검색매칭</ul>
	</div>


</div>

<div class="crm-wrap">
	<ul class="crm-menu-wrap"></ul>
	<ul class="crm-gap "></ul>
	<ul class="crm-body">
		<div id="crm_body">
		


            <?php
            /*
                echo "<pre>";
                print_r($viewData);
                echo "</pre>";
                */
            ?>

		</div>
	</ul>
</div>

<script>
const prd_idx = '<?=$requestData['prd_idx']?>';

const prdProviderInfo = (function(){

    const API_ENDPOINTS = {
        procSave: '/ad/proc/Admin/Product/saveProductPartner',
        info: '/ad/ajax/prd_provider_info_form',
        match: '/ad/ajax/prd_provider_info_match',
        cancelMatchProviderProduct: '/router/cancelMatchProviderProduct/',
        loadGodoGoodsInfo: '/router/loadGodoGoodsInfo/',
    };

    /**
     * 뷰 변경
     * 
     * @param {string} mode - 뷰 모드
     */
    function view(mode){

        if( mode == 'info'){
            endPoint = API_ENDPOINTS.info;
        }else if( mode == 'match'){
            endPoint = API_ENDPOINTS.match;
        }

        ajaxRequest(endPoint, {
                prd_idx
            }, {  method: 'GET', dataType: 'html' })
            .then((getdata) => {
                $('#crm_body').html(getdata);
            })
            .catch((error) => {
                alert('뷰 변경 실패');
            });

    }


	/**
	 * 공급사 상품 매칭 취소
	 * @param {string} db1_idx - 상품 고유번호
	 * @param {string} db2_idx - 공급사 상품 고유번호
	 */
	function cancelMatchProviderProduct(db1_idx, db2_idx){

        ajaxRequest(API_ENDPOINTS.cancelMatchProviderProduct, {
            db1_idx,
            db2_idx,
        })
        .then(res => {
            if( res.status === 'success' ){
                alert(res.message);
                location.reload();
            }else{
                alert(res.message);
            }
        })
        .catch(error => {
            console.error('AJAX 요청 실패:', error);
            alert('서버 통신에 실패했습니다.');
        });

    }


    /**
     * 고도몰 매칭 상품 정보 갱신
     * @param {string} prd_idx - 상품 고유번호
     * @param {string} godo_goodsNo - 고도몰 상품코드
     */
    function loadGodoGoodsInfo(prd_idx, godo_goodsNo){
        
        ajaxRequest(API_ENDPOINTS.loadGodoGoodsInfo, {
            prd_idx,
            godo_goodsNo,
        })
        .then(res => {
            console.log(res);
        })
        .catch(error => {
            console.error('AJAX 요청 실패:', error);
            alert('서버 통신에 실패했습니다.');
        });

    }


    function save(){
        // FormData 객체를 사용하여 폼 데이터 직접 전송
        var formData = new FormData(document.getElementById("prd_provider_info_form"));

        // AJAX 요청 보내기
        ajaxRequest(API_ENDPOINTS.procSave, formData, { 
                processData: false, 
                contentType: false 
            })
            .done(res => {
                if(res.status == 'success'){
                    alert('저장되었습니다.');
                }else{
                    alert('저장에 실패했습니다: ' + (res.message || '알 수 없는 오류'));
                }
            })
            .catch(function(error){
                console.error('Error:', error);
                alert('저장 중 오류가 발생했습니다.');
            });
    };

    return {
        view,
        cancelMatchProviderProduct,
        loadGodoGoodsInfo,
        save
    }

})();


$(function(){

    <?php
        if( isset($requestData['vmode']) && $requestData['vmode'] == 'info'){
    ?>
        prdProviderInfo.view('info');
    <?
        }elseif( isset($requestData['vmode']) && $requestData['vmode'] == 'match'){
    ?>
        prdProviderInfo.view('match');
    <?php } ?>

    $('.crm-menu ul').on('click', function(){
        const mode = $(this).data('mode');

        $(".crm-menu ul").removeClass('active');
        $(this).addClass('active');

        prdProviderInfo.view(mode);
    });

    // 매칭취소 버튼 클릭
    $('.cancel-match-btn').on('click', function(){

        const db1_idx = $(this).data('db1-idx');
        const db2_idx = $(this).data('db2-idx');

		dnConfirm(
			'정말 매칭취소 하시겠습니까?',
			'취소하시면 데이터는 복구되지 않습니다.',
			() => {
                prdProviderInfo.cancelMatchProviderProduct(db1_idx, db2_idx);
            }
		);

    });

    $(document).on('click', '#loadGodoGoodsInfoBtn', function(){
        const prd_idx = $(this).data('prd-idx');
        const godo_goodsNo = $(this).data('godo-goods-no');
        prdProviderInfo.loadGodoGoodsInfo(prd_idx, godo_goodsNo);
    });

});

</script>

<?php
include ($docRoot."/admin2/layout/footer_popup.php");
exit;
?>
