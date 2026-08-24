<?php
use App\Services\ProductSpecService;

$specCategoryCode = (string)($specCategoryCode ?? '');
$specData = (isset($specData) && is_array($specData)) ? $specData : [];
$specInputPrefix = (string)($specInputPrefix ?? 'spec');
$specService = new ProductSpecService();
$specType = $specService->getSpecType($specCategoryCode);
$schema = $specService->getSchema($specCategoryCode);
$schemas = $specService->getSchemas();
$vendor = (isset($specData['vendor_size']) && is_array($specData['vendor_size'])) ? $specData['vendor_size'] : [];
$measured = (isset($specData['measured_size']) && is_array($specData['measured_size'])) ? $specData['measured_size'] : [];
$options = (isset($specData['options']) && is_array($specData['options'])) ? $specData['options'] : [];
?>
<style>
    .torso-size-compare { max-width: 900px; margin-top: 18px; padding: 16px; border: 1px solid #dbe3ee; border-radius: 10px; background: #f8fafc; }
    .torso-size-compare h3 { margin: 0 0 4px; color: #1f2937; font-size: 15px; }
    .torso-size-compare__desc { margin: 0 0 12px; color: #64748b; font-size: 12px; }
    .torso-size-compare__stage { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .torso-size-compare__figure { min-width: 0; margin: 0; padding: 10px; border: 1px solid #e1e7ef; border-radius: 8px; background: #fff; }
    .torso-size-compare__figure figcaption { margin-bottom: 8px; color: #475569; font-size: 12px; font-weight: 700; text-align: center; }
    .torso-size-compare__figure img, .torso-size-compare__figure svg { display: block; width: 100%; height: auto; aspect-ratio: 43 / 70; border-radius: 4px; background: #f8fafc; }
    .torso-size-compare__figure img { object-fit: contain; }
    .torso-size-compare__svg .torso-shape { fill: rgba(73, 129, 190, .62); stroke: #346da9; stroke-width: 3; transition: d .2s ease; }
    .torso-size-compare__svg .torso-guide { stroke: #cbd5e1; stroke-width: 2; stroke-dasharray: 8 8; }
    .torso-size-compare__svg .torso-measure { stroke: #64748b; stroke-width: 2; }
    .torso-size-compare__svg text { font-family: Arial, sans-serif; }
    .torso-size-compare__svg .torso-measure-text { fill: #346da9; font-size: 20px; font-weight: 700; }
    .torso-size-compare__results { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 12px; }
    .torso-size-compare__results div { padding: 8px; border-radius: 6px; background: #fff; color: #64748b; font-size: 12px; }
    .torso-size-compare__results strong { display: block; margin-top: 3px; color: #346da9; font-size: 14px; }
    @media (max-width: 720px) { .torso-size-compare__stage { grid-template-columns: 1fr; } .torso-size-compare__results { grid-template-columns: 1fr; } }
</style>
<div class="product-spec-forms" data-spec-input-prefix="<?= htmlspecialchars($specInputPrefix, ENT_QUOTES, 'UTF-8') ?>">
<?php foreach ($schemas as $schemaCode => $schema) {
    $isActiveSchema = ($schemaCode === $specType);
    $schemaVendor = $isActiveSchema ? $vendor : [];
    $schemaMeasured = $isActiveSchema ? $measured : [];
    $schemaOptions = $isActiveSchema ? $options : [];
?>
<div class="product-spec-form" data-spec-category="<?= htmlspecialchars($schemaCode, ENT_QUOTES, 'UTF-8') ?>" style="<?= $isActiveSchema ? '' : 'display:none;' ?>">
        <table class="table-style border01">
            <colgroup><col width="180px"><col><col></colgroup>
            <tr><th>항목</th><th>업체제공 수치</th><th>실측 수치</th></tr>
            <?php foreach ($schema['options'] as $key => $option) { ?>
                <tr>
                    <th><?= htmlspecialchars($option[0], ENT_QUOTES, 'UTF-8') ?></th>
                    <td colspan="2">
                        <select name="<?= htmlspecialchars($specInputPrefix, ENT_QUOTES, 'UTF-8') ?>_option[<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>]" <?= $isActiveSchema ? '' : 'disabled' ?>>
                            <?php foreach ($option[1] as $value => $label) { ?>
                                <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" <?= ($schemaOptions[$key] ?? '') === $value ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            <?php } ?>
            <?php foreach ($schema['fields'] as $key => $field) { ?>
                <tr>
                    <th>
                        <?php if (($field[2] ?? false) === true) { ?><b class="point"><?php } ?>
                        <?= htmlspecialchars($field[0] . ($field[1] !== '' ? ' (' . $field[1] . ')' : ''), ENT_QUOTES, 'UTF-8') ?>
                        <?php if (($field[2] ?? false) === true) { ?></b><?php } ?>
                    </th>
                    <td>
                        <input type="text" name="<?= htmlspecialchars($specInputPrefix, ENT_QUOTES, 'UTF-8') ?>_vendor[<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars((string)($schemaVendor[$key] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:120px;" <?= $isActiveSchema ? '' : 'disabled' ?>>
                        <?php if (($field[3] ?? '') !== '') { ?>
                            <span class="admin-guide-text" style="margin-left:4px;"><?= htmlspecialchars($field[3], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php } ?>
                    </td>
                    <td><input type="text" name="<?= htmlspecialchars($specInputPrefix, ENT_QUOTES, 'UTF-8') ?>_measured[<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>]" value="<?= htmlspecialchars((string)($schemaMeasured[$key] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:120px;" <?= $isActiveSchema ? '' : 'disabled' ?>></td>
                </tr>
            <?php } ?>
        </table>
        <div class="admin-guide-text">- <?= htmlspecialchars($schema['label'], ENT_QUOTES, 'UTF-8') ?> 카테고리에서만 저장됩니다.</div>
        <?php if ($schemaCode === '02010000') { ?>
            <div class="torso-size-compare">
                <h3>한국 여성 평균과 토르소 크기 비교</h3>
                <p class="torso-size-compare__desc">왼쪽은 키 160cm 기준 여성, 오른쪽은 입력한 토르소 치수를 같은 180cm 기준 비율로 표시합니다.</p>
                <div class="torso-size-compare__stage">
                    <figure class="torso-size-compare__figure">
                        <figcaption>한국 여성 평균 · 160cm</figcaption>
                        <img src="/img/size_silhouette_m.jpg" alt="키 160cm 기준 여성 실루엣">
                    </figure>
                    <figure class="torso-size-compare__figure">
                        <figcaption id="torso-size-compare-summary" class="torso-size-compare-summary">입력 토르소</figcaption>
                        <svg id="torso-size-compare-svg" class="torso-size-compare-svg" viewBox="0 0 430 700" role="img" aria-label="입력 토르소 크기 비교 도식">
                            <line x1="24" y1="661" x2="406" y2="661" class="torso-guide"/>
                            <path id="torso-size-compare-path" class="torso-size-compare-path torso-shape" d=""/>
                            <line id="torso-size-compare-height-line" class="torso-height-line torso-measure"/><line id="torso-size-compare-height-top" class="torso-height-top torso-measure"/><line id="torso-size-compare-height-bottom" class="torso-height-bottom torso-measure"/>
                            <text id="torso-size-compare-height-text" class="torso-height-text torso-measure-text"></text>
                        </svg>
                    </figure>
                </div>
                <div class="torso-size-compare__results">
                    <div>가슴 차이<strong id="torso-size-compare-bust-diff" class="torso-bust-diff">-</strong></div>
                    <div>허리 차이<strong id="torso-size-compare-waist-diff" class="torso-waist-diff">-</strong></div>
                    <div>엉덩이 차이<strong id="torso-size-compare-hip-diff" class="torso-hip-diff">-</strong></div>
                </div>
            </div>
        <?php } ?>
</div>
<?php } ?>
</div>
<script>
$(function() {
    window.toggleSharedProductSpec = function(categoryCode, inputPrefix) {
        var specType = /^0201\d{4}$/.test(String(categoryCode || '')) ? '02010000' : String(categoryCode || '');
        var $forms = $('.product-spec-forms[data-spec-input-prefix="' + inputPrefix + '"]');
        $forms.find('.product-spec-form').each(function() {
            var $section = $(this);
            var active = String($section.data('spec-category')) === specType;
            $section.toggle(active);
            $section.find('input, select, textarea').prop('disabled', !active);
        });
    };

    window.toggleSharedProductSpec(
        <?= json_encode($specCategoryCode, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        <?= json_encode($specInputPrefix, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
    );

    var $form = $('.product-spec-form[data-spec-category="02010000"]');
    var $compare = $form.find('.torso-size-compare');
    var prefix = <?= json_encode($specInputPrefix, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var read = function(key, fallback) {
        var measured = parseFloat(String($form.find('input[name="' + prefix + '_measured[' + key + ']"]').val() || '').replace(/,/g, ''));
        var vendor = parseFloat(String($form.find('input[name="' + prefix + '_vendor[' + key + ']"]').val() || '').replace(/,/g, ''));
        if (Number.isFinite(measured) && measured > 0) return measured;
        if (Number.isFinite(vendor) && vendor > 0) return vendor;
        return fallback;
    };
    var hasMeasurement = function(key) {
        var measured = parseFloat(String($form.find('input[name="' + prefix + '_measured[' + key + ']"]').val() || '').replace(/,/g, ''));
        var vendor = parseFloat(String($form.find('input[name="' + prefix + '_vendor[' + key + ']"]').val() || '').replace(/,/g, ''));
        return (Number.isFinite(measured) && measured > 0) || (Number.isFinite(vendor) && vendor > 0);
    };
    var render = function() {
        var clamp = function(value, min, max) { return Math.min(max, Math.max(min, value)); };
        var bust = clamp(read('chest_circumference', 90), 20, 200);
        var shoulderMax = (!hasMeasurement('shoulder_width') && hasMeasurement('chest_circumference')) ? bust : 100;
        var values = {
            height: clamp(read('body_height', 61), 20, 160),
            shoulder: clamp(read('shoulder_width', 30), 10, shoulderMax),
            bust: bust,
            waist: clamp(read('waist_circumference', 68), 20, 200),
            hip: clamp(read('hip_circumference', 92), 20, 220)
        };
        var px = 700 / 180, cx = 215, bottom = 661, heightPx = values.height * px, top = bottom - heightPx;
        var shoulder = values.shoulder * px / 2, bust = values.bust * .38 * px / 2, waist = values.waist * .38 * px / 2, hip = values.hip * .38 * px / 2;
        var shoulderY = top + heightPx * .12, bustY = top + heightPx * .3, waistY = top + heightPx * .56, hipY = top + heightPx * .76, crotchY = top + heightPx * .84, gap = Math.max(9, hip * .12), neck = Math.min(22, shoulder * .28);
        var d = `M ${cx - neck} ${top}
            L ${cx - neck} ${top + 12}
            C ${cx - neck - 4} ${shoulderY - 8}, ${cx - shoulder + 16} ${shoulderY - 12}, ${cx - shoulder} ${shoulderY}
            C ${cx - shoulder + 4} ${bustY - 20}, ${cx - bust} ${bustY - 12}, ${cx - bust} ${bustY}
            C ${cx - bust} ${bustY + 24}, ${cx - waist} ${waistY - 24}, ${cx - waist} ${waistY}
            C ${cx - waist} ${waistY + 28}, ${cx - hip} ${hipY - 30}, ${cx - hip} ${hipY}
            C ${cx - hip} ${crotchY + 12}, ${cx - hip * 0.82} ${bottom - 12}, ${cx - hip * 0.72} ${bottom}
            L ${cx - gap} ${bottom} L ${cx - gap} ${crotchY}
            L ${cx + gap} ${crotchY} L ${cx + gap} ${bottom}
            L ${cx + hip * 0.72} ${bottom}
            C ${cx + hip * 0.82} ${bottom - 12}, ${cx + hip} ${crotchY + 12}, ${cx + hip} ${hipY}
            C ${cx + hip} ${hipY - 30}, ${cx + waist} ${waistY + 28}, ${cx + waist} ${waistY}
            C ${cx + waist} ${waistY - 24}, ${cx + bust} ${bustY + 24}, ${cx + bust} ${bustY}
            C ${cx + bust} ${bustY - 12}, ${cx + shoulder - 4} ${bustY - 20}, ${cx + shoulder} ${shoulderY}
            C ${cx + shoulder - 16} ${shoulderY - 12}, ${cx + neck + 4} ${shoulderY - 8}, ${cx + neck} ${top + 12}
            L ${cx + neck} ${top} Z`;
        $compare.find('.torso-size-compare-path').attr('d', d);
        var lineX = 375;
        $compare.find('.torso-height-line').attr({x1:lineX, x2:lineX, y1:top, y2:bottom});
        $compare.find('.torso-height-top').attr({x1:lineX - 9, x2:lineX + 9, y1:top, y2:top});
        $compare.find('.torso-height-bottom').attr({x1:lineX - 9, x2:lineX + 9, y1:bottom, y2:bottom});
        $compare.find('.torso-height-text').attr({x:lineX + 12, y:(top + bottom) / 2}).text(values.height + 'cm');
        $compare.find('.torso-size-compare-summary').text('입력 토르소 · ' + values.height + 'cm · ' + values.bust + ' / ' + values.waist + ' / ' + values.hip);
        var signed = function(value) { return (value >= 0 ? '+' : '') + value.toFixed(1).replace(/\.0$/, '') + 'cm'; };
        $compare.find('.torso-bust-diff').text(signed(values.bust - 85));
        $compare.find('.torso-waist-diff').text(signed(values.waist - 72));
        $compare.find('.torso-hip-diff').text(signed(values.hip - 92));
    };
    $form.on('input', 'input[name^="' + prefix + '_vendor["], input[name^="' + prefix + '_measured["]', render);
    render();
});
</script>
