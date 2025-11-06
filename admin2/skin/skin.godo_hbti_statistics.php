<?php
/*
=================================================================================
뷰 파일: admin2/skin/skin.godo_hbti_statistics.php
호출경로 : /ad/showdang/godo_hbti_statistics
설명: 고도몰 회원 HBTI 통계 화면
작성자: Lion65
수정일: 2025-04-04
=================================================================================

GET
@getParam {int} $_prd_idx - 상품 시퀀스

CONTROLLER
/application/Controllers/Admin/ShowdangController.php

*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\ShowdangController;

$showdangController = new ShowdangController(); 

$viewData = $showdangController->godoHbtiStatisticsIndex();

?>
<div id="contents_head">
	<h1>HBTI 통계</h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">

        <div>총 등록자 : <?=$viewData['hbtiCount']['total']?></div>
        <div id="list_new_wrap" class="m-t-5">
            <div id="" class="table-wrap5">
                <div class=" scroll-wrap">

                    <table class="table-st1">
                        <thead>
                        <tr>
                            <th class="list-idx">순위</th>
                            <th class="">HBTI</th>
                            <th class="">카운터</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                                $i = 0;
                                foreach ($viewData['hbtiCount']['data'] as $value) {
                                    $i++;
                            ?>
                            <tr>
                                <td><?=$i?></td>
                                <td><?=$value['hbti']?></td>
                                <td><?=$value['cnt']?></td>
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
</div>