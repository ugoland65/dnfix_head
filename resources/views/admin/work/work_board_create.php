<style type="text/css">
    .target-mb-id-div {}

    .target-mb-id-div label {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 5px;
    }

    #file_list_wrap {}

    .file-list-wrap {
        width: 500px;
        display: inline-block;
    }

    .file-list {
        padding: 5px;
        margin-bottom: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .link-list-wrap{
        display:flex;
        flex-direction: column;
        gap: 5px;
    }
    .link-list{
        display: flex;
        align-items: center;
        gap: 5px;
        input[type="text"]{
            width: 700px;
        }
    }
</style>
<div id="contents_head">
    <h1><?= $title ?? '업무 게시판 등록' ?></h1>
</div>
<div id="contents_body">
    <div id="contents_body_wrap">

        <form id="work_board_create_form" method="post" action="/admin/work/TaskRequest/save" enctype="multipart/form-data">

            <?php if( $mode == 'modify' ){ ?>
                <input type="hidden" name="idx" value="<?= $workInfo['idx'] ?? '' ?>" >
                <input type="hidden" name="old_link" value="<?= $workInfo['link'] ?? '' ?>" >
            <?php } ?>
            <input type="hidden" name="mode" value="<?= $mode ?? 'create' ?>" >

            <table class="table-reg th-150">
                <tr>
                    <th>제목</th>
                    <td>
                        <div class="form-group">

                            <?php if( !empty($category) ){ ?>
                                <input type="hidden" name="category" value="<?= $category ?>" >
                                <b style="width: 100px; text-align: center;"><?= $category ?></b>
                            <?php } else{ ?>?>
                                <select name="category" id="category">
                                    <?php foreach ($workLogCate as $cate) { ?>
                                        <option value="<?= $cate['name'] ?>" <?= $category == $cate['name'] ? 'selected' : '' ?>><?= $cate['name'] ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>

                            <input type="text" name="subject" id="subject" class="width-full" value="<?= $workInfo['subject'] ?? '' ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>참여자</th>
                    <td>

                        <div id="target_mb_id_div" class="target-mb-id-div">
                            참여자 :
                            <label><input type="checkbox" name="target_mb_idx_all" id="target_mb_idx_all" > 전체선택</label>
                            <?php
                            foreach ($mentionTarget as $mb) {

                                $_checked = "";
                                $_target_mb_text = "@" . $mb['idx'];
                                if (isset($workInfo['target_mb']) && strstr($workInfo['target_mb'], $_target_mb_text)) {
                                    $_checked = "checked";
                                }
                            ?>
                                <label><input type="checkbox" name="target_mb_idx[]" class="target-mb-id" value="<?= $mb['idx'] ?>" <?= $_checked ?>> <?= $mb['ad_name'] ?></label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>업무상태</th>
                    <td>
                        <select name="state">
                            <option value="대기" <?= (isset($workInfo['state']) && $workInfo['state'] == '대기') ? 'selected' : '' ?>>대기</option>
                            <option value="확인" <?= (isset($workInfo['state']) && $workInfo['state'] == '확인') ? 'selected' : '' ?>>확인</option>
                            <option value="완료" <?= (isset($workInfo['state']) && $workInfo['state'] == '완료') ? 'selected' : '' ?>>완료</option>
                            <option value="반려" <?= (isset($workInfo['state']) && $workInfo['state'] == '반려') ? 'selected' : '' ?>>반려</option>
                        </select>
                    </td>
                </tr>

                <?php /*
                <tr>
                    <th>첨부파일</th>
                    <td>
                        <div class="file-list-wrap" id="file_list_wrap">
                            <div class="file-list">
                                <input name="work_log_file[]" type="file">
                            </div>
                        </div>
                        <div class="">
                            <button type="button" id="addFileBtn" class="btnstyle1 btnstyle1-success btnstyle1-sm">첨부파일 추가</button>
                        </div>
                    </td>
                </tr>
                */ ?>

                <tr>
                    <th>참조 링크</th>
                    <td>
                        <div class="link-list-wrap" id="link_list_wrap">

                            <?php if( !empty($workInfo['link']) ){ ?>
                                <?php foreach($workInfo['link'] as $l){ ?>
                                    <div class="link-list">
                                        <input name="link[]" type="text" value="<?= $l ?>" >
                                        <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs link-remove">삭제</button>
                                    </div>
                                <?php } ?>
                            <?php }else{ ?>
                                <div class="link-list">
                                    <input name="link[]" type="text"  >
                                    <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs link-remove">삭제</button>
                                </div>
                            <?php } ?>
                            
                        </div>
                        <div class="m-t-5">
                            <button type="button" id="addLinkBtn" class="btnstyle1 btnstyle1-success btnstyle1-sm">링크 추가</button>
                        </div>
                    </td>
                </tr>
                
                <?php if( !empty($withdb) && !empty($withdbdata) ){ ?>
                    <tr>
                        <th>참조 상품</th>
                        <td style="padding:15px;">

                            <input type="hidden" name="withdb_mode" value="<?= $withdb ?>" >

                            <?php if( $withdb == "provider_product" ){ ?>
                                
                                <h3 style="font-size:16px; font-weight: 600; margin-bottom:5px;">공급사 상품 참조</h3>
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

                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>

                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <th>내용</th>
                    <td>
                        <textarea name="body" id="summernote"><?= $workInfo['body'] ?? '' ?></textarea>
                    </td>
                </tr>
            </table>
        </form>

    </div>
</div>
<div id="contents_bottom">
    <div class="submitBtnWrap">
        <button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-md" onclick="location.href='/admin/work/TaskRequest?category=<?= $category ?? '' ?>'">
            <i class="fas fa-arrow-left"></i> 목록
        </button>
        <button type="button" id="saveBtn" class="btnstyle1 btnstyle1-primary btnstyle1-md" >
            <i class="far fa-check-circle"></i> <?= $mode == 'create' ? '등록' : '수정' ?>
        </button>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote-bs5.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote-bs5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/lang/summernote-ko-KR.min.js"></script>


<script>
    $('#summernote').summernote({
        lang: 'ko-KR',
        height: 400, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true, // set focus to editable area after initializing summernote
        dialogsInBody: true,

        toolbar: [
            // [groupName, [list of button]]
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['color', ['forecolor', 'color']],
            ['table', ['table']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['picture', 'link', 'video']],
            ['view', ['help']],
            ['view', ['codeview']],
        ],
        popover: {
            image: [
                ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                ['remove', ['removeMedia']]
            ],
            link: [
                ['link', ['linkDialogShow', 'unlink']]
            ],
            table: [
                ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
            ],
            air: [
                ['color', ['color']],
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']]
            ]
        }

    });

    $('#target_mb_idx_all').on('change', function() {
        if($(this).is(':checked')) {
            $('.target-mb-id').prop('checked', true);
        } else {
            $('.target-mb-id').prop('checked', false);
        }
    });

    //첨부파일 추가
    $('#addFileBtn').on('click', function() {
        var shtml = '<div class="file-list">'
            + '<input name="work_log_file[]" type="file">'
            + '</div>';
        $('#file_list_wrap').append(shtml);
    });

    //링크 추가
    $('#addLinkBtn').on('click', function() {
        var shtml = '<div class="link-list">'
            + '<input name="link[]" type="text">'
            + '<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs link-remove">삭제</button>'
            + '</div>';
        $('#link_list_wrap').append(shtml);
    });

    //링크 삭제 (마지막 1개는 값만 초기화)
    $(document).on('click', '.link-remove', function() {
        var $list = $(this).closest('.link-list');
        if ($('#link_list_wrap .link-list').length <= 1) {
            $list.find('input[name="link[]"]').val('');
            return;
        }
        $list.remove();
    });

    //저장
    $('#saveBtn').on('click', function() {

        var subject = $('#subject').val();
        var category = $('#category').val();

        if(subject == '') {
            alert('제목을 입력해주세요.');
            $('#subject').focus();
            return false;
        }

        $('#work_board_create_form').submit();
    });
</script>