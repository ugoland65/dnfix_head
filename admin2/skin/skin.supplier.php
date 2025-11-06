<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\RequestHandler;
use App\Services\PartnersService;

$requestHandler = new RequestHandler();
$requestData = $requestHandler->getAll();

$partnersService = new PartnersService();
$extraData = [
    'category' => '성인용품공급'
];

$partners = $partnersService->getPartnersList($requestData, $extraData);

?>
<div id="contents_head">
	<h1>공급사 관리</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap">

            <div class="table-wrap5">
                <div class="scroll-wrap">

                    <table class="table-st1">
                        <thead>
                        <tr>
                            <th>관리</th>
                            <th class="list-idx">고유번호</th>
                            <th class="">공급사명</th>
                            <th class="">사이트 주소</th>
                            <th class="">사이트 ID</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php 
                                foreach($partners as $partner) { 
                                    $info = json_decode($partner['info'], true);
                            ?>
                                <tr>
                                    <td>
                                        <button type="button"  class="btnstyle1 btnstyle1-success btnstyle1-sm btn-view" data-idx="<?=$partner['idx']?>">관리</button>
                                    </td>
                                    <td><?=$partner['idx']?></td>
                                    <td><?=$partner['name']?></td>
                                    <td><a href="<?=$info['hp']['url']?>" target="_blank"><?=$info['hp']['url'] ?? '' ?></a></td>
                                    <td><?=$info['hp']['id'] ?? ''?></td>
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
	<div class="pageing-wrap"></div>
</div>

<script>
const supplier = (function(){

    /**
     * 공급사 관리
     * @param {string} idx - 공급사 고유번호
     */
    function supplierView(idx){
		openDialog("/ad/ajax/partners_info",{ idx },"공급사 상세","800px"); 
    }

    return {
        supplierView
    }
    
}());

$(function(){

    $('.btn-view').on('click', function(){
        const idx = $(this).data('idx');
        supplier.supplierView(idx);
    });

});
</script>