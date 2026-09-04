<?php
$relationGroupData = (isset($relation_group_data) && is_array($relation_group_data)) ? $relation_group_data : [];
$product = (isset($relationGroupData['product']) && is_array($relationGroupData['product'])) ? $relationGroupData['product'] : [];
$brandOptions = (isset($relationGroupData['brand_options']) && is_array($relationGroupData['brand_options'])) ? $relationGroupData['brand_options'] : [];
$availableGroups = (isset($relationGroupData['available_groups']) && is_array($relationGroupData['available_groups'])) ? $relationGroupData['available_groups'] : [];
$attachedGroups = (isset($relationGroupData['attached_groups']) && is_array($relationGroupData['attached_groups'])) ? $relationGroupData['attached_groups'] : [];
$groupMembersByGroupIdx = (isset($relationGroupData['group_members_by_group_idx']) && is_array($relationGroupData['group_members_by_group_idx'])) ? $relationGroupData['group_members_by_group_idx'] : [];
$attachedGroupIdxs = [];
foreach ($attachedGroups as $attachedGroup) {
    $attachedGroupIdxs[(int)($attachedGroup['prgp_group_idx'] ?? 0)] = true;
}
$modeLabels = [
    'series' => '시리즈',
    'custom_group' => '특정 그룹',
];
?>

<div style="padding:15px; max-width:1000px;">
    <h3 style="margin:0 0 8px;">시리즈/연관그룹 관리</h3>
    <div style="margin-bottom:15px; color:#6b7280;">
        <?= htmlspecialchars((string)($product['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?> 상품과 같은 브랜드의 그룹만 표시됩니다.
    </div>

    <div style="margin-bottom:20px; padding:12px; border:1px solid #dbe3ea; background:#f8fafc;">
        <div style="font-weight:700; margin-bottom:10px;">새 시리즈/연관그룹 만들기</div>
        <?php if (empty($brandOptions)) { ?>
            <div style="color:#dc2626;">상품에 연결된 브랜드가 없어 새 그룹을 만들 수 없습니다.</div>
        <?php } else { ?>
            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <select id="relation_group_mode" style="height:30px;">
                    <option value="series">시리즈</option>
                    <option value="custom_group">특정 그룹</option>
                </select>
                <select id="relation_group_brand_idx" style="height:30px;">
                    <?php foreach ($brandOptions as $brandOption) { ?>
                        <option value="<?= (int)($brandOption['idx'] ?? 0) ?>"><?= htmlspecialchars((string)($brandOption['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                    <?php } ?>
                </select>
                <input type="text" id="relation_group_name" maxlength="255" placeholder="시리즈/그룹 이름" style="height:30px; min-width:240px;">
                <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="relation_group_create_btn">생성 후 포함</button>
            </div>
            <div style="margin-top:8px;">
                <input type="text" id="relation_group_memo" maxlength="1000" placeholder="관리 메모 (선택)" style="height:30px; width:min(100%, 550px);">
            </div>
        <?php } ?>
    </div>

    <div style="margin-bottom:20px;">
        <div style="font-weight:700; margin-bottom:8px;">현재 포함된 시리즈/연관그룹 (<?= count($attachedGroups) ?>)</div>
        <?php if (empty($attachedGroups)) { ?>
            <div style="color:#6b7280;">포함된 시리즈/연관그룹이 없습니다.</div>
        <?php } else { ?>
            <table class="table-st1" style="width:100%;">
                <thead>
                    <tr>
                        <th>구분</th>
                        <th>브랜드</th>
                        <th>이름</th>
                        <th>메모</th>
                        <th>순서</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attachedGroups as $attachedGroup) { ?>
                        <?php $groupMode = (string)($attachedGroup['prg_mode'] ?? ''); ?>
                        <tr>
                            <td class="text-center"><?= htmlspecialchars($modeLabels[$groupMode] ?? $groupMode, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-center"><?= htmlspecialchars((string)($attachedGroup['brand_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)($attachedGroup['prg_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)($attachedGroup['prg_memo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-center"><?= (int)($attachedGroup['prgp_sort_no'] ?? 0) ?></td>
                            <td class="text-center">
                                <button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs relation-group-remove-btn" data-group-idx="<?= (int)($attachedGroup['prgp_group_idx'] ?? 0) ?>">제외</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>

    <div>
        <div style="font-weight:700; margin-bottom:8px;">기존 시리즈/연관그룹에 포함</div>
        <?php
        $canAddGroups = array_filter($availableGroups, function ($group) use ($attachedGroupIdxs) {
            return !isset($attachedGroupIdxs[(int)($group['prg_idx'] ?? 0)]);
        });
        ?>
        <?php if (empty($canAddGroups)) { ?>
            <div style="color:#6b7280;">추가할 수 있는 같은 브랜드의 기존 그룹이 없습니다.</div>
        <?php } else { ?>
            <div style="display:flex; gap:8px; align-items:center;">
                <select id="existing_relation_group_idx" style="height:30px; max-width:500px;">
                    <?php foreach ($canAddGroups as $group) { ?>
                        <?php $groupMode = (string)($group['prg_mode'] ?? ''); ?>
                        <option value="<?= (int)($group['prg_idx'] ?? 0) ?>">
                            [<?= htmlspecialchars($modeLabels[$groupMode] ?? $groupMode, ENT_QUOTES, 'UTF-8') ?>]
                            <?= htmlspecialchars((string)($group['brand_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            - <?= htmlspecialchars((string)($group['prg_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php } ?>
                </select>
                <button type="button" class="btnstyle1 btnstyle1-info btnstyle1-sm" id="relation_group_add_btn">선택 그룹에 포함</button>
            </div>
        <?php } ?>
    </div>
</div>

<div style="padding:0 15px 15px; max-width:1000px;">
    <div style="font-weight:700; margin-bottom:8px;">시리즈/연관그룹 포함 상품</div>
    <?php if (empty($attachedGroups)) { ?>
        <div style="color:#6b7280;">포함 상품을 표시할 시리즈/연관그룹이 없습니다.</div>
    <?php } else { ?>
        <?php foreach ($attachedGroups as $attachedGroup) { ?>
            <?php
            $attachedGroupIdx = (int)($attachedGroup['prgp_group_idx'] ?? 0);
            $groupMembers = $groupMembersByGroupIdx[$attachedGroupIdx] ?? [];
            $groupMode = (string)($attachedGroup['prg_mode'] ?? '');
            ?>
            <div style="margin-bottom:12px; padding:12px; border:1px solid #dbe3ea; background:#fff;">
                <div style="margin-bottom:10px;">
                    <b><?= htmlspecialchars($modeLabels[$groupMode] ?? $groupMode, ENT_QUOTES, 'UTF-8') ?></b>
                    · <b><?= htmlspecialchars((string)($attachedGroup['prg_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></b>
                    <span style="color:#6b7280;">(<?= htmlspecialchars((string)($attachedGroup['brand_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)</span>
                </div>
                <?php if (empty($groupMembers)) { ?>
                    <span style="color:#6b7280;">다른 포함 상품이 없습니다.</span>
                <?php } else { ?>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <?php foreach ($groupMembers as $groupMember) { ?>
                            <?php
                            $memberImage = trim((string)($groupMember['CD_IMG'] ?? ''));
                            $memberImageUrl = '';
                            if ($memberImage !== '') {
                                $memberImageUrl = (($groupMember['img_mode'] ?? '') === 'out')
                                    ? $memberImage
                                    : '/data/comparion/' . $memberImage;
                            }
                            ?>
                            <a
                                href="javascript:onlyAD.prdView('<?= (int)($groupMember['prgp_prd_idx'] ?? 0) ?>','info');"
                                style="display:block; width:120px; color:#111827; text-decoration:none;"
                            >
                                <div style="height:100px; border:1px solid #e5e7eb; background:#f8fafc; display:flex; align-items:center; justify-content:center;">
                                    <?php if ($memberImageUrl !== '') { ?>
                                        <img src="<?= htmlspecialchars($memberImageUrl, ENT_QUOTES, 'UTF-8') ?>" style="max-width:100%; max-height:100%; object-fit:contain;" alt="">
                                    <?php } else { ?>
                                        <span style="color:#9ca3af; font-size:11px;">이미지 없음</span>
                                    <?php } ?>
                                </div>
                                <div style="margin-top:5px; font-size:12px; line-height:1.4; word-break:break-all;">
                                    <?= htmlspecialchars((string)($groupMember['CD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php if (isset($groupMember['ps_stock']) && $groupMember['ps_stock'] !== null) { ?>
                                    <div style="font-size:11px; color:#6b7280;">재고 <?= number_format((int)$groupMember['ps_stock']) ?></div>
                                <?php } ?>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<script>
(function () {
    var prdIdx = <?= (int)($prd_idx ?? 0) ?>;

    function reloadRelationGroupPanel() {
        if (typeof prdInfo !== 'undefined' && prdInfo && typeof prdInfo.mode === 'function') {
            prdInfo.mode('', 'relation_group');
        }
    }

    function requestRelationGroupAction(actionMode, data, successMessage) {
        var payload = Object.assign({
            action_mode: actionMode,
            prd_idx: prdIdx
        }, data || {});

        ajaxRequest('/admin/product/action', payload)
            .done(function (res) {
                if (!(res && res.success)) {
                    alert(res && res.message ? res.message : '처리에 실패했습니다.');
                    return;
                }
                if (successMessage) {
                    alert(res.message || successMessage);
                }
                if (typeof prdInfo !== 'undefined' && prdInfo && typeof prdInfo.updateSeriesLabel === 'function') {
                    prdInfo.updateSeriesLabel((res.data && res.data.series_names) ? res.data.series_names : []);
                }
                reloadRelationGroupPanel();
            })
            .fail(function (xhr) {
                var message = xhr && xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : '서버 통신 중 오류가 발생했습니다.';
                alert(message);
            });
    }

    $('#relation_group_create_btn').off('click.relationGroup').on('click.relationGroup', function () {
        var groupName = String($('#relation_group_name').val() || '').trim();
        if (!groupName) {
            alert('시리즈/연관그룹 이름을 입력해주세요.');
            $('#relation_group_name').focus();
            return;
        }
        requestRelationGroupAction('create_product_relation_group', {
            prg_mode: String($('#relation_group_mode').val() || 'series'),
            prg_brand_idx: Number($('#relation_group_brand_idx').val() || 0),
            prg_name: groupName,
            prg_memo: String($('#relation_group_memo').val() || '').trim()
        }, '새 시리즈/연관그룹을 만들었습니다.');
    });

    $('#relation_group_add_btn').off('click.relationGroup').on('click.relationGroup', function () {
        var groupIdx = Number($('#existing_relation_group_idx').val() || 0);
        if (groupIdx <= 0) {
            alert('추가할 그룹을 선택해주세요.');
            return;
        }
        requestRelationGroupAction('add_product_to_relation_group', {
            prg_idx: groupIdx
        }, '상품을 그룹에 포함했습니다.');
    });

    $('.relation-group-remove-btn').off('click.relationGroup').on('click.relationGroup', function () {
        var groupIdx = Number($(this).data('group-idx') || 0);
        if (groupIdx <= 0 || !confirm('이 상품을 해당 시리즈/연관그룹에서 제외할까요?')) {
            return;
        }
        requestRelationGroupAction('remove_product_from_relation_group', {
            prg_idx: groupIdx
        }, '상품을 그룹에서 제외했습니다.');
    });
})();
</script>
