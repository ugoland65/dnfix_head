<div id="contents_head">
	<h1>상품 그룹핑 목록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

        <!-- 검색 영역 -->
        <div class="top-search-wrap">
            <ul class="count-wrap">
                <span class="count">Total : <b><?=number_format($paginationArray['total']) ?></b></span>
                <span class="m-l-10"><b><?=$paginationArray['current_page']?></b></span>
                <span>/</span>
                <span><b><?=$paginationArray['last_page']?></b> page</span>
            </ul>
            <ul class="m-l-10">
				<select name="s_pg_mode" id="s_pg_mode" >
                    <option value="all" <? if( $s_pg_mode == '' ) echo "selected";?>>전체</option>
					<option value="sale" <? if( $s_pg_mode == 'sale' ) echo "selected";?>>데이할인</option>
                    <option value="period" <? if( $s_pg_mode == 'period' ) echo "selected";?>>기간할인</option>
                    <option value="qty" <? if( $s_pg_mode == 'qty' ) echo "selected";?>>수량체크</option>
                    <option value="event" <? if( $s_pg_mode == 'event' ) echo "selected";?>>기획전</option>
                    <option value="op" <? if( $s_pg_mode == 'op' ) echo "selected";?>>운영</option>
				</select>
			</ul>
            <ul>
				<select name="s_pg_state" id="s_pg_state" >
                    <option value="전체" <? if( $s_pg_state == '전체' ) echo "selected";?>>전체</option>
					<option value="진행" <? if( $s_pg_state == '진행' ) echo "selected";?>>진행</option>
                    <option value="마감" <? if( $s_pg_state == '마감' ) echo "selected";?>>마감</option>
                    <option value="취소" <? if( $s_pg_state == '취소' ) echo "selected";?>>취소</option>
				</select>
			</ul>
            <ul>
                <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" id="searchBtn">
                    <i class="fas fa-search"></i> 검색
                </button>
            </ul>
        </div>

        <div id="list_new_wrap">

            <div class="table-wrap5 m-t-5">
                <div class="scroll-wrap">
                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                                <th>고유번호</th>
                                <th>공개</th>
                                <th>모드</th>
                                <th>그룹핑 이름</th>
                                <th>상품모드</th>
                                <th>상품수</th>
                                <th>진행일</th>
                                <th>상태</th>
                                <th>등록자</th>
                                <th>등록일</th>
                                <th>관리</th>
                                <th>상품</th>
                                <th>삭제</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productGroupingList as $productGrouping) { ?>
                                <tr>
                                    <td><input type="checkbox" name="key_check[]" value="<?= $productGrouping['idx'] ?>"></td>
                                    <td class="text-center"><?= $productGrouping['idx'] ?></td>
                                    <td class="text-center"><?= $productGrouping['public'] ?></td>
                                    <td class="text-center"><?= $productGrouping['pg_mode_text'] ?></td>
                                    <td><?= $productGrouping['pg_subject'] ?></td>
                                    <td class="text-center"><?= $productGrouping['prd_mode_text'] ?></td>
                                    <td class="text-center"><?= $productGrouping['prd_count'] ?></td>
                                    <td>
                                        <?php if ($productGrouping['pg_mode'] == 'period') { ?>
                                            <?= $productGrouping['pg_sday'] ?> ~ <?= $productGrouping['pg_day'] ?>
                                        <?php } else { ?>
                                            <?php if ($productGrouping['pg_day'] != '0000-00-00') { ?>
                                                <?= $productGrouping['pg_day'] ?>
                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                    <td><?= $productGrouping['pg_state'] ?></td>
                                    <td><?= $productGrouping['reg_name'] ?></td>
                                    <td><?= $productGrouping['reg_date'] ?></td>
                                    <td>
                                        <button type="button" class="btn btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='/admin/product/grouping_view/<?= $productGrouping['idx'] ?>'"> 관리 </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btnstyle1 btnstyle1-info btnstyle1-sm" > 상품 </button>
                                    </td>
                                    <td>
                                        /<?=$auth['ad_id'] ?>/<?= $auth['ad_level']?>
                                        <button type="button" class="btn btnstyle1 btnstyle1-danger btnstyle1-sm" > 삭제 </button>
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
   <div class="pageing-wrap" id="pageing_ajax_show">
        <?= $paginationHtml ?>
   </div>
</div>