<?php
$modeLabels = [
    'series' => '시리즈',
    'custom_group' => '특정 그룹',
];
$escape = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<div id="contents_head">
    <h1>상품 시리즈 관리</h1>
</div>
<div id="contents_body">
    <div id="contents_body_wrap">
        <div class="top-search-wrap">
            <ul class="count-wrap">
                <span class="count">Total : <b><?= number_format((int)($pagination['total'] ?? 0)) ?></b></span>
                <span class="m-l-10"><b><?= (int)($pagination['current_page'] ?? 1) ?></b></span>
                <span>/</span>
                <span><b><?= (int)($pagination['last_page'] ?? 1) ?></b> page</span>
            </ul>
            <ul class="m-l-10">
                <select id="s_mode">
                    <option value="">전체 구분</option>
                    <option value="series" <?= $s_mode === 'series' ? 'selected' : '' ?>>시리즈</option>
                    <option value="custom_group" <?= $s_mode === 'custom_group' ? 'selected' : '' ?>>특정 그룹</option>
                </select>
            </ul>
            <ul>
                <select id="s_use_yn">
                    <option value="">전체 상태</option>
                    <option value="Y" <?= $s_use_yn === 'Y' ? 'selected' : '' ?>>사용</option>
                    <option value="N" <?= $s_use_yn === 'N' ? 'selected' : '' ?>>사용 중지</option>
                </select>
            </ul>
            <ul>
                <select id="s_brand_idx">
                    <option value="">전체 브랜드</option>
                    <?php foreach ($brandOptions as $brandOption) { ?>
                        <option value="<?= (int)($brandOption['BD_IDX'] ?? 0) ?>" <?= $s_brand_idx === (int)($brandOption['BD_IDX'] ?? 0) ? 'selected' : '' ?>>
                            <?= $escape($brandOption['BD_NAME'] ?? '') ?>
                        </option>
                    <?php } ?>
                </select>
            </ul>
            <ul>
                <input type="text" id="search_value" value="<?= $escape($search_value) ?>" placeholder="시리즈/그룹 이름">
            </ul>
            <ul>
                <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" id="searchBtn">
                    <i class="fas fa-search"></i> 검색
                </button>
            </ul>
        </div>

        <?php /*
        <div class="table-wrap5 m-t-5" style="padding:12px; background:#f8fafc; border:1px solid #e5e7eb;">
            <form class="relation-group-form" id="relation_group_create_form">
                <input type="hidden" name="prg_idx" value="">
                <table class="table-st1" style="width:100%;">
                    <tbody>
                        <tr>
                            <th style="width:110px;">새 그룹 생성</th>
                            <td>
                                <select name="prg_mode">
                                    <option value="series">시리즈</option>
                                    <option value="custom_group">특정 그룹</option>
                                </select>
                                <select name="prg_brand_idx" required>
                                    <option value="">브랜드 선택</option>
                                    <?php foreach ($brandOptions as $brandOption) { ?>
                                        <option value="<?= (int)($brandOption['BD_IDX'] ?? 0) ?>"><?= $escape($brandOption['BD_NAME'] ?? '') ?></option>
                                    <?php } ?>
                                </select>
                                <input type="text" name="prg_name" maxlength="255" placeholder="시리즈/그룹 이름" style="width:260px;" required>
                                <input type="text" name="prg_memo" maxlength="1000" placeholder="관리 메모 (선택)" style="width:260px;">
                                <input type="hidden" name="prg_use_yn" value="Y">
                                <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm relation-group-save-btn">생성</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        */ ?>

        <div id="list_new_wrap">
            <div class="table-wrap5 m-t-5">
                <div class="scroll-wrap">
                    <table class="table-st1">
                        <thead>
                            <tr>
                                <th>고유번호</th>
                                <th>구분</th>
                                <th>브랜드</th>
                                <th>시리즈/그룹 이름</th>
                                <th>포함 상품</th>
                                <th>관리 메모</th>
                                <th>상태</th>
                                <th>등록자</th>
                                <th>등록일</th>
                                <th>관리</th>
                                <th>삭제</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($relationGroupList)) { ?>
                                <tr>
                                    <td colspan="11" class="text-center">등록된 시리즈/연관그룹이 없습니다.</td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($relationGroupList as $relationGroup) { ?>
                                    <tr class="relation-group-form">
                                        <td class="text-center">
                                            <input type="hidden" name="prg_idx" value="<?= (int)($relationGroup['prg_idx'] ?? 0) ?>">
                                            <?= (int)($relationGroup['prg_idx'] ?? 0) ?>
                                        </td>
                                        <td class="text-center">
                                            <select name="prg_mode">
                                                <option value="series" <?= ($relationGroup['prg_mode'] ?? '') === 'series' ? 'selected' : '' ?>>시리즈</option>
                                                <option value="custom_group" <?= ($relationGroup['prg_mode'] ?? '') === 'custom_group' ? 'selected' : '' ?>>특정 그룹</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="prg_brand_idx">
                                                <?php
                                                $currentBrandIdx = (int)($relationGroup['prg_brand_idx'] ?? 0);
                                                $currentBrandExists = false;
                                                foreach ($brandOptions as $brandOption) {
                                                    if ($currentBrandIdx === (int)($brandOption['BD_IDX'] ?? 0)) {
                                                        $currentBrandExists = true;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <?php if (!$currentBrandExists) { ?>
                                                    <option value="<?= $currentBrandIdx ?>" selected>삭제된 브랜드 #<?= $currentBrandIdx ?></option>
                                                <?php } ?>
                                                <?php foreach ($brandOptions as $brandOption) { ?>
                                                    <option value="<?= (int)($brandOption['BD_IDX'] ?? 0) ?>" <?= $currentBrandIdx === (int)($brandOption['BD_IDX'] ?? 0) ? 'selected' : '' ?>>
                                                        <?= $escape($brandOption['BD_NAME'] ?? '') ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="prg_name" value="<?= $escape($relationGroup['prg_name'] ?? '') ?>" maxlength="255" style="min-width:190px;"></td>
                                        <td class="text-center"><?= number_format((int)($relationGroup['member_count'] ?? 0)) ?>개</td>
                                        <td><input type="text" name="prg_memo" value="<?= $escape($relationGroup['prg_memo'] ?? '') ?>" maxlength="1000" style="min-width:200px;"></td>
                                        <td class="text-center">
                                            <select name="prg_use_yn">
                                                <option value="Y" <?= ($relationGroup['prg_use_yn'] ?? 'Y') === 'Y' ? 'selected' : '' ?>>사용</option>
                                                <option value="N" <?= ($relationGroup['prg_use_yn'] ?? '') === 'N' ? 'selected' : '' ?>>사용 중지</option>
                                            </select>
                                        </td>
                                        <td class="text-center"><?= $escape($relationGroup['prg_reg_admin_name'] ?? '-') ?></td>
                                        <td class="text-center"><?= $escape($relationGroup['prg_reg_at'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btnstyle1 btnstyle1-success btnstyle1-sm relation-group-save-btn">저장</button>
                                        </td>
                                        <td class="text-center">
                                            <?php if ((int)($relationGroup['member_count'] ?? 0) === 0) { ?>
                                                <button type="button" class="btn btnstyle1 btnstyle1-danger btnstyle1-sm relation-group-delete-btn">삭제</button>
                                            <?php } else { ?>
                                                <span title="포함 상품을 모두 제외한 뒤 삭제할 수 있습니다.">-</span>
                                            <?php } ?>
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
    <div class="pageing-wrap" id="pageing_ajax_show"><?= $paginationHtml ?></div>
</div>

<script>
function relationGroupSearch() {
    var params = {
        s_mode: $('#s_mode').val(),
        s_use_yn: $('#s_use_yn').val(),
        s_brand_idx: $('#s_brand_idx').val(),
        search_value: $.trim($('#search_value').val())
    };
    var query = Object.keys(params).filter(function (key) {
        return params[key] !== '';
    }).map(function (key) {
        return key + '=' + encodeURIComponent(params[key]);
    }).join('&');
    location.href = '/admin/product/relation_group_management' + (query ? '?' + query : '');
}

function requestRelationGroupManagement(actionMode, data, successMessage) {
    var payload = { action_mode: actionMode };
    if ($.isArray(data)) {
        $.each(data, function (_, field) {
            payload[field.name] = field.value;
        });
    } else {
        $.extend(payload, data || {});
    }

    ajaxRequest('/admin/product/action', payload)
        .done(function (res) {
            if (!(res && res.success)) {
                alert(res && res.message ? res.message : '처리에 실패했습니다.');
                return;
            }
            alert(res.message || successMessage);
            location.reload();
        })
        .fail(function (xhr) {
            alert(xhr && xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : '서버 통신 중 오류가 발생했습니다.');
        });
}

$(function () {
    $('#searchBtn').on('click', relationGroupSearch);
    $('#s_mode, #s_use_yn, #s_brand_idx').on('change', relationGroupSearch);
    $('#search_value').on('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            relationGroupSearch();
        }
    });

    $('.relation-group-save-btn').on('click', function () {
        var $form = $(this).closest('.relation-group-form');
        var groupName = $.trim($form.find('[name="prg_name"]').val());
        var brandIdx = Number($form.find('[name="prg_brand_idx"]').val() || 0);
        if (!groupName) {
            alert('시리즈/그룹 이름을 입력해주세요.');
            $form.find('[name="prg_name"]').focus();
            return;
        }
        if (brandIdx <= 0) {
            alert('브랜드를 선택해주세요.');
            $form.find('[name="prg_brand_idx"]').focus();
            return;
        }
        requestRelationGroupManagement('save_product_relation_group', $form.find(':input').serializeArray(), '저장했습니다.');
    });

    $('.relation-group-delete-btn').on('click', function () {
        var groupIdx = Number($(this).closest('.relation-group-form').find('[name="prg_idx"]').val() || 0);
        if (groupIdx <= 0 || !confirm('이 시리즈/연관그룹을 삭제할까요?')) {
            return;
        }
        requestRelationGroupManagement('delete_product_relation_group', { prg_idx: groupIdx }, '삭제했습니다.');
    });
});
</script>
