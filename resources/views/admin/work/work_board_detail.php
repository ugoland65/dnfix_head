<style>


    .participant-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.4);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .participant-modal .modal-card {
        background: #fff;
        width: 520px;
        max-width: 90vw;
        border-radius: 6px;
        padding: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    }
    .participant-modal .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        font-weight: 600;
    }
    .participant-modal .modal-body {
        max-height: 360px;
        overflow: auto;
        border: 1px solid #eee;
        padding: 8px;
    }
    .participant-modal .modal-body label {
        display: block;
        padding: 4px 0;
    }
    .participant-modal .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 12px;
    }

    .bo_body_wrap{ }
    .bo_body_wrap img{ max-width:100% !important; }
</style>

<div id="contents_head">
	<h1>
        업무 게시판 - (<?=$workLog['category']?>) <?=$workLog['subject']?>
        <button type="button" id="" class="btnstyle1" onclick="footerGlobal.comment('log','<?=$workLog['idx']?>')" >
            이 게시물의 댓글 : 
            <? if( $workLog['cmt_s_count'] > 0 ) { ?> <b><?=$workLog['cmt_s_count']?></b>개<? }else{ ?>아직 댓글이 없습니다.<? } ?>
        </button>
    </h1>
</div>
<div id="contents_body" class="partition-body">
	<div id="contents_body_wrap">

        <div class="partition-wrap">
            <ul class="partition-body">
                <div class="scroll-wrap3 p-10">
                    <table class="table-reg th-150">

                        <tr>
                            <th>분류/제목</th>
                            <td>
                                [<?=$workLog['category']?>] <b><?=$workLog['subject']?></b>
                            </td>
                        </tr>

                        <tr>
                            <th>작성일/작성자</th>
                            <td>
                                <?=date("y.m.d H:i", strtotime($workLog['reg_date']))?> | 

                                <div class="mb-profile-box md" data-idx="<?= $workLog['reg_idx'] ?>" >
                                    <?php if( !empty($workLog['reg_image']) ){ ?>
                                        <div class="profile-img"><img src="/data/uploads/<?=$workLog['reg_image']?>" alt=""></div>
                                    <?php } else { ?>
                                        <div class="profile-img"><i class="far fa-user-circle"></i></div>
                                    <?php } ?>
                                    <span class="profile-name"><?= $workLog['reg_name'] ?></span>
                                </div>

                            </td>
                        </tr>

                        <tr>
                            <th>참여자</th>
                            <td>
                                <?php if( !empty($workLog['target_list']) ){ ?>
                                    <?php foreach($workLog['target_list'] as $target){ ?>
                                        <div class="mb-profile-box md participant-item" data-idx="<?= $target['idx'] ?>" data-name="<?= $target['name'] ?>" >
                                            <?php if( !empty($target['image']) ){ ?>
                                                <div class="profile-img"><img src="/data/uploads/<?=$target['image']?>" alt=""></div>
                                            <?php } else { ?>
                                                <div class="profile-img"><i class="far fa-user-circle"></i></div>
                                            <?php } ?>
                                            <span class="profile-name"><?= $target['name'] ?></span>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <button type="button" id="openParticipantModal" class="btnstyle1 btnstyle1-primary btnstyle1-xs" >참여자 추가</button>
                            </td>
                        </tr>

                        <?php if( $auth['ad_level'] == 100 ){ ?>
                        <tr>
                            <th>읽음 체크<br>( 관리자 래밸 100만 보임)</th>
                            <td>
                                <div>
                                <?php if( !empty($workLog['view_check']) ){ ?>
                                    <?php foreach($workLog['view_check'] as $view_check){ ?>
                                        <ul><?= $view_check['date'] ?> :: <?= $view_check['name'] ?> :: <?= $view_check['ip'] ?> (<?= $view_check['domain'] ?>)</ul>
                                    <?php } ?>
                                <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>

                        
                        <tr>
                            <th>체크</th>
                            <td>
                                
                                <?php if( $isViewCheck ){ ?>
                                    <!--
                                    <div>체크 완료</div>
                                    -->
                                <?php } else { ?>

                                    <?php if( $auth['ad_idx'] != $workLog['reg_idx'] ){ ?>
                                        <div class="m-b-20">
                                            <div style="color:#ff0000;">
                                                체크 여부는 해당 내용을 전달하거나 확인받았다는 피드백을 의미합니다.<br> 
                                                아직 이 게시물은 확인 처리되지 않았습니다. 내용을 확인한 후 확인 처리를 해주세요.
                                            </div>
                                            <button type="button" id="workViewCheck" class="btnstyle1 btnstyle1-primary btnstyle1-sm m-t-5" >체크하기 - 이 내용을 확인했습니다.</button>
                                        </div>
                                    <?php } ?>
                                    
                                <?php } ?>

                                <div>체크 완료자 리스트</div>
                                <div>
                                <?php if( !empty($viewCheckList) ){ ?>
                                    <?php foreach($viewCheckList as $viewCheck){ ?>
                                        <ul class="m-b-3">
                                            <div class="mb-profile-box md" data-idx="<?= $viewCheck['admin']['idx'] ?>" data-name="<?= $viewCheck['admin']['ad_name'] ?>" >
                                                <?php if( !empty($viewCheck['admin']['ad_image']) ){ ?>
                                                    <div class="profile-img"><img src="/data/uploads/<?=$viewCheck['admin']['ad_image']?>" alt=""></div>
                                                <?php } else { ?>
                                                    <div class="profile-img"><i class="far fa-user-circle"></i></div>
                                                <?php } ?>
                                                <span class="profile-name"><?= $viewCheck['admin']['ad_name'] ?></span>
                                            </div>
                                            :: 
                                            <?= $viewCheck['reg_date'] ?>
                                        </ul>
                                    <?php } ?>
                                <?php } ?>
                                </div>

                            </td>
                        </tr>

                        <tr>
                            <th>상태</th>
                            <td>
                                <button type="button" class="btnstyle1 btnstyle1-sm work-state-btn <?php if( $workLog['state'] == "대기" ) echo "btnstyle1-info"; ?>" data-state="대기">대기</button>
                                <button type="button" class="btnstyle1 btnstyle1-sm work-state-btn <?php if( $workLog['state'] == "확인" ) echo "btnstyle1-info"; ?>" data-state="확인">확인</button>
                                <button type="button" class="btnstyle1 btnstyle1-sm work-state-btn <?php if( $workLog['state'] == "완료" ) echo "btnstyle1-info"; ?>" data-state="완료">완료</button>
                                <button type="button" class="btnstyle1 btnstyle1-sm work-state-btn <?php if( $workLog['state'] == "반려" ) echo "btnstyle1-info"; ?>" data-state="반려">반려</button>
                            </td>
                        </tr>

                        <?php if( !empty($workLog['link']) ){ ?>
                            <tr>
                                <th>참조 링크</th>
                                <td>
                                    <div>
                                        <?php foreach($workLog['link'] as $link){ ?>
                                            <ul><a href="<?= $link ?>" target="_blank"><?= $link ?></a></ul>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if( !empty($withdb) && !empty($withdbdata) ){ ?>
                            <tr>
                                <td colspan="2" style="padding:15px;">

                                    <input type="hidden" name="withdb_mode" value="<?= $withdb ?>" >

                                    <?php if( $withdb == "provider_product" ){ ?>
                                        
                                        <h3 style="font-size:16px; font-weight: 600; margin-bottom:5px;">위탁상품 참조</h3>
                                        <table class="table-style">
                                            <thead>
                                                <tr>
                                                    <th class="list-idx">고유번호</th>
                                                    <th class="">등록상태</th>
                                                    <th class="" style="width:80px;">이미지</th>
                                                    <th class="" style="width:50px;">분류</th>
                                                    <th class="prd-name">이름</th>
                                                    <th class="">브랜드</th>
                                                    <th class="">공급사</th>
                                                    <th class="">코드</th>
                                                    <th class="">고도몰<br>상품코드</th>
                                                    <th class="">고도몰<br>판매가</th>
                                                    <th class="">상품원가<br>/주문가</th>
                                                    <th class="">공급사<br>이미지</th>
                                                    <th class="prd-name">공급사<br>상품명</th>
                                                    <th class="">공급사<br>상품코드</th>
                                                    <th class="">공급사<br>판매상태</th>
                                                    <th class="">공급 2차</th>
                                                    <th class="">수정일<br>등록일</th>
                                                    <th class="">처리</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($withdbdata as $item){ ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <?= $item['idx'] ?>
                                                            <input type="hidden" name="pks[]" value="<?= $item['idx'] ?>" >
                                                        </td>
                                                        <td class="text-center">
                                                            <?= $item['status'] ?>
                                                            <?php if( $item['status'] == '품절' ){ ?>
                                                                <br><span class="text-red"><?=date('Y.m.d', strtotime($item['sold_out_date'])) ?? ''?></span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <img src="<?= $item['img_src'] ?>" style="height:70px; border:1px solid #eee !important;">
                                                        </td>
                                                        <td class="text-center"><?= $prd_kind_name[$item['kind']] ?? "미지정" ?></td>
                                                        <td class="prd-name">
                                                            <a href="javascript:prdProviderQuick(<?= $item['idx'] ?>);"><?= $item['name'] ?></a>
                                                            <? if (!empty($item['memo'])) { ?>
                                                                <br><span class="prd-memo"><?= $item['memo'] ?></span>
                                                            <? } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if( !empty($item['brand_idx']) ){ ?>
                                                                <?= $item['brand_name'] ?>
                                                            <?php } else { ?>
                                                                <span class="text-red">미등록</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center"><?= $item['partner_name'] ?></td>
                                                        <td class="text-center"><?= $item['code'] ?></td>
                                                        <td class="text-center">
                                                            <?php if( !empty($item['godo_goodsNo']) ){ ?>
                                                                <div style="font-size: 12px;">
                                                                    #<?= $item['godo_goodsNo'] ?>
                                                                </div>
                                                                <div class="m-t-3">
                                                                    <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMall(<?= $item['godo_goodsNo'] ?>);">쑈당몰 상품보기</button>
                                                                </div>
                                                                <div class="m-t-5">
                                                                    <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goGodoMallAdmin(<?= $item['godo_goodsNo'] ?>);">관리자 상품보기</button>
                                                                </div>
                                                            <?php } else { ?>
                                                                <span class="text-red">미등록</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-right"><?= number_format($item['sale_price']) ?></td>
                                                        <td class="text-right">
                                                            <?= number_format($item['cost_price']) ?>
                                                            <br><b><?= number_format($item['order_price']) ?></b>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php
                                                            if (!empty($item['supplier_img_src'])) {
                                                            ?>
                                                                <img src="<?= $item['supplier_img_src'] ?>" style="height:70px; border:1px solid #eee !important;">
                                                            <?php } else { ?>
                                                                -
                                                            <?php } ?>
                                                        </td>
                                                        <td class="prd-name text-left">
                                                            <a href="javascript:goSupplierProductEdit('<?= $item['supplier_prd_idx'] ?>');"><?= $item['name_p'] ?? '-' ?></a>
                                                            <?php
                                                            if (!empty($item['matching_option'])) {
                                                            ?>
                                                                <br>( 옵션 : <?= $item['matching_option'] ?? '-' ?>)
                                                            <?php
                                                            }
                                                            ?>
                                                        </td>

                                                        <!-- 공급사 상품코드 -->
                                                        <td class="text-center">
                                                            <?php
                                                                if (!empty($item['supplier_prd_idx'])) {
                                                            ?>
                                                                <div style="font-size: 12px;">
                                                                    #<?= $item['supplier_prd_pk'] ?>
                                                                </div>
                                                                <div class="m-t-3">
                                                                    <button type="button" class="btnstyle1 btnstyle1-xs"
                                                                        onclick="goSupplierProduct('<?= $item['supplier_site'] ?>', '<?= $item['supplier_prd_pk'] ?>');">공급사 사이트</button>
                                                                </div>
                                                            <?php } else { ?>
                                                                -
                                                            <?php } ?>
                                                        </td>

                                                        <!-- 공급사 판매상태 -->
                                                        <td class="text-center">
                                                            <?php
                                                            if (!empty($item['supplier_prd_idx'])) {
                                                            ?>
                                                                <?= $supplierProductMap[$item['supplier_prd_idx']]['status'] ?? '-' ?>
                                                                <?php if( ($supplierProductMap[$item['supplier_prd_idx']]['status'] ?: '') == '품절' ){ ?>
                                                                    <br><span class="text-red"><?=date('Y.m.d', strtotime($supplierProductMap[$item['supplier_prd_idx']]['sold_out_date'])) ?? ''?></span>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                -
                                                            <?php } ?>
                                                        </td>

                                                        <td class="text-center"><?= $item['supplier_2nd_name'] ?? '-' ?></td>

                                                        <td class="text-center">
                                                            <?= date('Y.m.d H:i', strtotime($item['updated_at'])) ?? '-' ?><br>
                                                            <?= date('Y.m.d H:i', strtotime($item['created_at'])) ?? '-' ?>
                                                        </td>

                                                        <td class="text-center">
                                                            <button type="button" class="btnstyle1 btnstyle1-xs" onclick="goSupplierProductEdit('<?= $item['supplier_prd_idx'] ?>');">완료</button>
                                                        </td>

                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } ?>

                                </td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td colspan="2" class="bo_body_wrap p-30">
                                <?= html_entity_decode($workLog['body'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                            </td>
                        </tr>

                    </table>
                </div>                                   
            </ul>
            <ul class="partition-right ">

                <style>
                    .work-log-history-wrap{
                        padding:10px 15px;
                        h3{
                            font-size:16px;
                            font-weight:600;
                            margin-bottom:10px;
                        }
                        .work-log-history-list{
                            .work-log-history-item{
                                padding:10px 15px;
                                border-bottom:1px solid #ccc;
                                .work-log-history-item-date{
                                    font-size:12px;
                                    color:#555555;
                                }
                                .work-log-history-item-title{
                                    font-size:14px;
                                    font-weight:600;
                                    padding:5px 0 0;
                                }
                                .work-log-history-item-body{
                                    font-size:12px;
                                    color:#555555;
                                    padding:5px 0 0;
                                }
                            }
                        }
                    }
                </style>
                <div class="scroll-wrap3">
                    <div class="work-log-history-wrap">
                        <h3>업무 로그 기록</h3>              
                        <div class="work-log-history-list">
                            
                            <?php if( !empty($workLogHistoryList) ){ ?>
                                <?php foreach($workLogHistoryList as $workLogHistory){ ?>
                                    <div class="work-log-history-item">
                                        <div class="work-log-history-item-date">
                                            <?= date("y.m.d H:i", strtotime($workLogHistory['action_date'])) ?> |
                                            <?= $workLogHistory['action_name'] ?>
                                        </div>
                                        <div class="work-log-history-item-title"><?= $workLogHistory['action_summary'] ?></div>
                                        <?php if( !empty($workLogHistory['action_body']) ){ ?>
                                            <div class="work-log-history-item-body"><?= $workLogHistory['action_body'] ?></div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </ul>
        </div>

    </div>
</div>

<div class="participant-modal" id="participantModal">
    <div class="modal-card">
        <div class="modal-head">
            <span>참여자 추가</span>
            <button type="button" id="closeParticipantModal" class="btnstyle1 btnstyle1-xs">닫기</button>
        </div>
        <div class="modal-body">
            <?php
                $existingTargetIds = !empty($workLog['target_list']) ? array_column($workLog['target_list'], 'idx') : [];
                $existingTargetIds = array_map('strval', $existingTargetIds);
            ?>
            <?php if (!empty($mentionTarget)) { ?>
                <?php foreach ($mentionTarget as $member) { ?>
                    <?php $isSelected = in_array((string)$member['idx'], $existingTargetIds, true); ?>
                    <label>
                        <input type="checkbox" name="participant_mb_idx[]" value="<?= $member['idx'] ?>" <?= $isSelected ? 'checked disabled' : '' ?>>
                        <?= $member['ad_name'] ?>
                        <?php if ($isSelected) { ?>
                            <span class="text-muted"> (이미 추가됨)</span>
                        <?php } ?>
                    </label>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="modal-actions">
            <button type="button" id="submitParticipantModal" class="btnstyle1 btnstyle1-primary btnstyle1-sm">저장</button>
        </div>
    </div>
</div>

<script>
    $(function () {
        const $modal = $('#participantModal');
        const workIdx = '<?= $workLog['idx'] ?>';

        $('#openParticipantModal').on('click', function () {
            $modal.css('display', 'flex');
        });

        $('#closeParticipantModal').on('click', function () {
            $modal.hide();
        });

        $modal.on('click', function (event) {
            if (event.target === this) {
                $modal.hide();
            }
        });

        $('#submitParticipantModal').on('click', function () {
            const targetIds = [];
            $modal.find('input[name="participant_mb_idx[]"]:checked').each(function () {
                targetIds.push($(this).val());
            });

            $.ajax({
                url: '/admin/work/TaskRequest/action',
                method: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'add_participant',
                    idx: workIdx,
                    target_mb_idxs: targetIds,
                },
            }).done(function (res) {
                if( res.success ){
                    alert(res && res.message ? res.message : '저장되었습니다.');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '요청에 실패했습니다.');
                }
            }).fail(function (res) {
                //console.log(arguments);
                console.log(res);
                //alert('요청에 실패했습니다.');
            });
        });

        $('#workViewCheck').on('click', function () {
            const $button = $(this);
            $button.prop('disabled', true);

            $.ajax({
                url: '/admin/work/TaskRequest/action',
                method: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'view_check',
                    idx: workIdx,
                },
            }).done(function (res) {
                if (res && res.success) {
                    alert(res.message || '확인 처리 완료');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '요청에 실패했습니다.');
                    $button.prop('disabled', false);
                }
            }).fail(function () {
                alert('요청에 실패했습니다.');
                $button.prop('disabled', false);
            });
        });

        $(document).on('click', '.work-state-btn', function () {
            const $button = $(this);
            const targetState = $button.data('state');
            if (!targetState) {
                return;
            }

            if (!confirm('상태를 [' + targetState + ']로 변경하시겠습니까?')) {
                return;
            }

            $button.prop('disabled', true);

            $.ajax({
                url: '/admin/work/TaskRequest/action',
                method: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'change_state',
                    idx: workIdx,
                    state: targetState,
                },
            }).done(function (res) {
                if (res && res.success) {
                    alert(res.message || '상태 변경 완료');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '요청에 실패했습니다.');
                    $button.prop('disabled', false);
                }
            }).fail(function () {
                alert('요청에 실패했습니다.');
                $button.prop('disabled', false);
            });
        });

        $(document).on('click', '.participant-item', function () {
            const targetIdx = $(this).data('idx');
            const targetName = $(this).data('name') || '';
            if (!targetIdx) {
                return;
            }
            if (!confirm('참여자 ' + (targetName ? '[' + targetName + ']' : '') + ' 를 제거하시겠습니까?')) {
                return;
            }

            $.ajax({
                url: '/admin/work/TaskRequest/action',
                method: 'POST',
                dataType: 'json',
                data: {
                    action_mode: 'remove_participant',
                    idx: workIdx,
                    target_mb_idx: targetIdx,
                },
            }).done(function (res) {
                if (res && res.success) {
                    alert(res.message || '처리 완료');
                    location.reload();
                } else {
                    alert(res && res.message ? res.message : '요청에 실패했습니다.');
                }
            }).fail(function () {
                alert('요청에 실패했습니다.');
            });
        });
    });
</script>
<div id="contents_bottom">

	<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="location.href='/admin/work/TaskRequest'" > 
		<i class="fas fa-arrow-left"></i> 목록
	</button>

    <?php if( $auth['ad_idx'] == $workLog['reg_idx'] || $auth['ad_level'] == 100 ){ ?>
        <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="location.href='/admin/work/TaskRequest/modify/<?=$workLog['idx']?>'" > 
            <i class="far fa-check-circle"></i> 수정
        </button>
    <?php } ?>    

</div>