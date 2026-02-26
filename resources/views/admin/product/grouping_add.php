<form id="form_prdGroupingReg">
    <input type="hidden" name="mode" value="<?= $mode ?>">
    <input type="hidden" name="prd_idxs" value="<?= implode(',', $idxs) ?>">

    <table class="table-style border01 width-full">
        <tr id="new_grouping_row">
            <th>신규생성</th>
            <td>
                <label for="new_grouping_new">
                    <input type="radio" name="new_grouping" value="new" id="new_grouping_new" checked>
                    신규 그룹핑 생성하기
                </label>
                <label for="new_grouping_old" class="m-l-10">
                    <input type="radio" name="new_grouping" value="old" id="new_grouping_old">
                    기존 [진행중]그룹핑에 추가하기
                </label>
            </td>
        </tr>
        <tr id="prd_count_row">
            <th>상품 수</th>
            <td>
                <span id="prd_count"><?= $data['prd_count'] ?></span>
            </td>
        </tr>

        <tr id="public_row">
            <th>공개여부</th>
            <td>
                <select name="public" id="public">
                    <option value="공개">공개</option>
                    <option value="개인">개인</option>
                </select>
            </td>
        </tr>

        <tr id="pg_mode_row">
            <th style="width:100px;">그룹핑 모드</th>
            <td>
                <select name="pg_mode" id="pg_mode" onchange="prdGroupingReg.pgModeOnchange(this.value)">
                    <option value="op">운영</option>
                    <option value="sale">데이할인</option>
                    <option value="period">기간할인</option>
                    <option value="event">기획전</option>
                    <option value="qty">수량 체크</option>
                </select>
                <div class="admin-guide-text">
                    - 데이할인 : 1일 기간지정<br>
                    - 기간할인 : 시작일 ~ 종료일 기간지정<br>
                    - 기획전 : 기간지정<br>
                    - 수량 체크 : 현재 수량을 점검하기 위한 그룹핑<br>
                    - 운영 : 운영 관리용으로 사용하는 그룹핑<br>
                </div>
            </td>
        </tr>
        <tr id="pg_subject_row">
            <th>그룹핑 제목</th>
            <td>
                <input type='text' name='pg_subject' value="" autocomplete="off">
            </td>
        </tr>
        <tr id="pg_date_row">
            <th>진행일</th>
            <td>
                <div class="calendar-input" style="display:none; width:105px;" id="pg_sday_wrap"><input type="text" name="pg_sday" id="pg_sday" value="<?= $data['pg_sday'] ?>" style="width:90px;" placeholder="시작일" autocomplete="off"> ~ </div>
                <div class="calendar-input" style="display:inline-block;" id="pg_day_wrap"><input type="text" name="pg_day" id="pg_day" value="<?= $data['pg_day'] ?>" style="width:90px;" autocomplete="off"></div>
            </td>
        </tr>
        <tr id="pg_select_row">
            <th>그룹핑 선택</th>
            <td>
                <select name="pg_select" id="pg_select">
                    <option value="">그룹핑 선택</option>
                    <?php foreach ($productGroupingForSelect as $item) { ?>
                        <option value="<?= $item['idx'] ?>"><?= $item['pg_subject'] ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
    </table>
</form>

<div class="m-t-10 text-center">
    <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="prdGroupingReg.addSave(this);">등록</button>
</div>

<script>

    var prdGrouping = function() {

        /**
         * 그룹핑 등록
         */
        function addSave(obj) {
            
            var isOldMode = $('input[name="new_grouping"]:checked').val() === 'old';
            if (!isOldMode) {
                var pgSubject = ($('input[name="pg_subject"]').val() || '').trim();
                if (!pgSubject) {
                    alert('그룹핑 제목을 입력해주세요.');
                    $('input[name="pg_subject"]').focus();
                    return;
                }
            }

            var formData = $('#form_prdGroupingReg').serializeArray();
            if (obj) {
                $(obj).prop('disabled', true);
            }

            $.ajax({
                url: '/admin/product/grouping_add_save',
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response && response.status === 'success') {
                        $('input[name="check_idx[]"]').prop('checked', false);
                        alert(response.message || '등록되었습니다.');
                        //location.reload();
                        return;
                    }
                    alert((response && response.message) ? response.message : '등록에 실패했습니다.');
                },
                error: function(response) {
                    var message = response && response.responseJSON && response.responseJSON.message
                        ? response.responseJSON.message
                        : '서버 통신 오류가 발생했습니다.';
                    alert(message);
                },
                complete: function(response) {
                    if (obj) {
                        $(obj).prop('disabled', false);
                    }
                }
            });

        }

        return {
            addSave
        }
    }();

    (function(){
        window.prdGroupingReg = window.prdGroupingReg || {};
        window.prdGroupingReg.addSave = prdGrouping.addSave;
        const hasGroupingForSelect = <?= !empty($productGroupingForSelect) ? 'true' : 'false' ?>;

        function toggleNewGroupingMode(mode) {
            if (mode === 'old') {
                $('#public_row, #pg_mode_row, #pg_subject_row, #pg_date_row').hide();
                $('#pg_select_row').show();
                return;
            }

            $('#public_row, #pg_mode_row, #pg_subject_row').show();
            $('#pg_select_row').hide();
            window.prdGroupingReg.pgModeOnchange($('#pg_mode').val() || 'sale');
        }

        window.prdGroupingReg.pgModeOnchange = function(mode) {
            if ($('input[name="new_grouping"]:checked').val() === 'old') {
                return;
            }

            if (mode === 'sale') {
                $('#pg_date_row').show();
                $('#pg_sday_wrap').hide();
                $('#pg_day_wrap').css({ display: 'inline-block' }).show();
            } else if (mode === 'period' || mode === 'event') {
                $('#pg_date_row').show();
                $('#pg_sday_wrap').css({ display: 'inline-block' }).show();
                $('#pg_day_wrap').css({ display: 'inline-block' }).show();
            } else {
                $('#pg_date_row').hide();
            }
        };

        $(function(){
            $('input[name="new_grouping"]').on('change', function(){
                if ($(this).val() === 'old' && !hasGroupingForSelect) {
                    alert('진행중인 그룹핑이 없습니다. 신규로 생성해주세요');
                    $('#new_grouping_new').prop('checked', true);
                    toggleNewGroupingMode('new');
                    return;
                }
                toggleNewGroupingMode($(this).val());
            });

            toggleNewGroupingMode($('input[name="new_grouping"]:checked').val() || 'new');
            window.prdGroupingReg.pgModeOnchange($('#pg_mode').val() || 'sale');
        });
    })();

</script>