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
</style>
<div id="contents_head">
    <h1><?= $title ?? '업무 게시판 등록' ?></h1>
</div>
<div id="contents_body">
    <div id="contents_body_wrap">

        <form id="work_board_create_form" method="post" action="/admin/work/TaskRequest/save" enctype="multipart/form-data">

            <?php if( $mode == 'modify' ){ ?>
                <input type="hidden" name="idx" value="<?= $workInfo['idx'] ?? '' ?>" >
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