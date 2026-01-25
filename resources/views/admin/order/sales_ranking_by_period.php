<div id="contents_head">
	<h1>기간별 판매 조회</h1>
	<h3>기간별 판매 조회입니다.</h3>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

        <!-- 검색 영역 -->
        <div class="top-search-wrap">
            <ul class="count-wrap">
            </ul>
            <ul class="calendar-input">
                <input type='text' name="s_date" id="s_date" value="<?= $s_date ?>">
            </ul>
            <ul>~</ul>
            <ul class="calendar-input">
                <input type='text' name="e_date" id="e_date" value="<?= $e_date ?>">
            </ul>
            <ul>
                <button type="button" id="search_btn" class="btnstyle1 btnstyle1-inverse3 btnstyle1-sm" >기간검색</button>
            </ul>
        </div>

		<div id="list_new_wrap">
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
                                <th>판매수량</th>
                                <th>최근 입고일</th>
                                <th>최근 판매일</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ($salesDaily as $row) {

                                $img_path = "";
                                if( $row['cd_img'] ){
                                    $img_path = '/data/comparion/'.$row['cd_img'];
                                }
                        ?>
                            <tr>
                                <td><input type="checkbox" name="check_idx[]" value="<?= $row['ps_idx'] ?>"></td>
                                <td class="list-idx"><?= $row['ps_idx'] ?></td>
                                <td class="list-idx"><?= $row['prd_idx'] ?></td>
                                <td class="p-5">
                                    <p onclick="onlyAD.prdView('<?=$row['cd_idx']?>','info');" style="cursor:pointer;" ><img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;"></p>
                                </td>
                                <td class="text-center"><?= $row['prd_kind_name'] ?></td>
                                <td>
                                    <p onclick="onlyAD.prdView('<?=$row['cd_idx']?>','info');" style="cursor:pointer;" ><b><?=$row['prd_name']?></b></p>
                                    <?php if( !empty($row['cd_memo2']) ){ ?>
                                        <div class="m-t-3" style="color:#ff0000"><span class="prd-memo">- <?=$row['cd_memo2']?></span></div>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?=$row['brand_name']?>
                                    <?php if( !empty($row['brand_name2']) ){ ?>
                                        <br>
                                        <?=$row['brand_name2']?>
                                    <?php } ?>
                                </td>
                                <td class="text-center"><?= $row['sold_qty'] ?></td>
                                <td class="text-center"><?= date('Y-m-d', strtotime($row['ps_in_date'])) ?></td>
                                <td class="text-center"><?= date('Y-m-d', strtotime($row['ps_last_date'])) ?></td>
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
	<div class="pageing-wrap"><?= $paginationHtml ?? '' ?></div>
</div>

<script>

//검색 버튼 클릭 이벤트
$(document).ready(function(){

    $('#search_btn').click(function(){
        var s_date = $('#s_date').val();
        var e_date = $('#e_date').val();
        location.href = '/admin/sales/sales_ranking_by_period?s_date=' + s_date + '&e_date=' + e_date;
    });

});

</script>