<?php
$prdIdx = (int)($prd_idx ?? 0);
$spec = (isset($spec) && is_array($spec)) ? $spec : [];
$specItems = (isset($spec['psi_items']) && is_array($spec['psi_items'])) ? $spec['psi_items'] : [];
?>
<style>
.spec-info-wrap { display: flex; gap: 16px; align-items: flex-start; font-size: 12px; color: #374151; }
.spec-info-canvas-col { flex: 1; min-width: 0; }
.spec-info-side { width: 320px; flex-shrink: 0; }
.spec-info-stage {
    position: relative;
    min-height: 240px;
    border: 1px solid #e5e7eb;
    background: #f8fafc;
    overflow: auto;
}
.spec-info-stage canvas { display: block; cursor: crosshair; max-width: 100%; }
.spec-info-empty {
    padding: 60px 16px;
    text-align: center;
    color: #6b7280;
}
.spec-info-guide { margin: 8px 0 12px; color: #4b5563; line-height: 1.5; }
.spec-info-step { margin-bottom: 10px; padding: 10px; border: 1px solid #e5e7eb; background: #fff; }
.spec-info-step b { display: block; margin-bottom: 6px; color: #111827; }
.spec-info-step.active { border-color: #2563eb; background: #eff6ff; }
.spec-info-result { width: 100%; border-collapse: collapse; }
.spec-info-result th,
.spec-info-result td { padding: 6px 8px; border: 1px solid #e5e7eb; text-align: left; }
.spec-info-result th { background: #f9fafb; width: 110px; }
.spec-info-result .spec-info-value { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.spec-info-del { display: none; flex-shrink: 0; }
.spec-info-dot { display: inline-block; width: 10px; height: 10px; margin-right: 6px; vertical-align: middle; }
.spec-info-actions { margin-top: 10px; display: flex; gap: 6px; flex-wrap: wrap; }
.spec-info-hint { margin-top: 8px; color: #2563eb; font-weight: 600; min-height: 18px; }
.spec-info-url { width: 100%; box-sizing: border-box; margin: 8px 0 6px; }
.spec-info-rotate { display: none; margin-top: 8px; }
</style>

<div class="spec-info-wrap">
    <div class="spec-info-canvas-col">
        <div class="spec-info-guide">
            파일 또는 이미지 URL로 단면도를 올린 뒤, 가로로 시작점과 끝점을 클릭해 전체길이를 맞추고 실제 cm를 입력합니다.
            내부길이는 같은 비율로 계산됩니다. 통로가 더 있으면 내부길이 1, 2를 추가하세요.
        </div>
        <div class="spec-info-rotate spec-info-actions" id="specRotateBar">
            <button type="button" class="btnstyle1 btnstyle1-sm" id="specBtnRotateLeft">좌로 90°</button>
            <button type="button" class="btnstyle1 btnstyle1-sm" id="specBtnRotateRight">우로 90°</button>
        </div>  
        <div class="spec-info-stage" id="specInfoStage">
            <div class="spec-info-empty" id="specInfoEmpty">파일 또는 이미지 URL을 넣으면 여기에 표시됩니다.</div>
            <canvas id="specInfoCanvas" style="display:none;"></canvas>
        </div>

        <div class="spec-info-hint" id="specInfoHint"></div>
    </div>
    <div class="spec-info-side">
        <div class="spec-info-step" id="specStepUpload">
            <b>1. 단면도</b>
            <input type="file" id="specInfoFile" accept="image/jpeg,image/png,image/gif,image/webp">
            <input type="text" id="specInfoUrl" class="spec-info-url" placeholder="https:// 이미지 URL">
            <button type="button" class="btnstyle1 btnstyle1-sm" id="specBtnLoadUrl">URL 불러오기</button>
            <div class="admin-guide-text m-t-6">URL은 저장 전에 캔버스에서 바로 측정할 수 있습니다. 저장할 때 서버에 내려받습니다.</div>
        </div>
        <div class="spec-info-step" id="specStepTotal">
            <b>2. 전체길이</b>
            <button type="button" class="btnstyle1 btnstyle1-sm" id="specBtnTotal">시작/끝 지정</button>
            <div class="m-t-7">
                길이
                <input type="text" id="specTotalCm" style="width:80px;" value="<?= htmlspecialchars((string)($spec['psi_total_length_cm'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"> cm
            </div>
        </div>
        <div class="spec-info-step" id="specStepInner">
            <b>3. 내부길이</b>
            <div class="spec-info-actions">
                <button type="button" class="btnstyle1 btnstyle1-sm" id="specBtnInner" data-type="inner">내부길이 지정</button>
                <button type="button" class="btnstyle1 btnstyle1-sm" id="specBtnInner1" data-type="inner_1">내부길이 1</button>
                <button type="button" class="btnstyle1 btnstyle1-sm" id="specBtnInner2" data-type="inner_2">내부길이 2</button>
            </div>
        </div>
        <table class="spec-info-result m-t-10">
            <tr>
                <th><span class="spec-info-dot" style="background:#2563eb;"></span>전체길이</th>
                <td id="specResultTotal">-</td>
            </tr>
            <tr>
                <th><span class="spec-info-dot" style="background:#00E5FF;"></span>내부길이</th>
                <td>
                    <div class="spec-info-value">
                        <span id="specResultInner">-</span>
                        <button type="button" class="btnstyle1 btnstyle1-xs spec-info-del" data-type="inner" id="specDelInner">삭제</button>
                    </div>
                </td>
            </tr>
            <tr>
                <th><span class="spec-info-dot" style="background:#C6FF00;"></span>내부길이 1</th>
                <td>
                    <div class="spec-info-value">
                        <span id="specResultInner1">-</span>
                        <button type="button" class="btnstyle1 btnstyle1-xs spec-info-del" data-type="inner_1" id="specDelInner1">삭제</button>
                    </div>
                </td>
            </tr>
            <tr>
                <th><span class="spec-info-dot" style="background:#FFE600;"></span>내부길이 2</th>
                <td>
                    <div class="spec-info-value">
                        <span id="specResultInner2">-</span>
                        <button type="button" class="btnstyle1 btnstyle1-xs spec-info-del" data-type="inner_2" id="specDelInner2">삭제</button>
                    </div>
                </td>
            </tr>
        </table>
        <div class="spec-info-actions">
            <button type="button" class="btnstyle1 btnstyle1-success btnstyle1-lg" id="specBtnSave">측정값 저장</button>
        </div>
        <div class="admin-guide-text m-t-8">
            선은 가로로만 그어집니다. 지정한 선은 위아래로 옮길 수 있고, 좌우 끝점을 드래그하면 길이를 늘이거나 줄일 수 있습니다. 시작점을 잘못 찍으면 ESC로 취소한 뒤 다시 찍으세요. 이미지 회전·교체 시 측정값이 초기화됩니다.
        </div>
    </div>
</div>

<script>
(function() {
    var prdIdx = <?= $prdIdx ?>;
    var spec = <?= json_encode($spec, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var itemDefs = {
        total: { label: '전체길이', color: '#ffffff', resultId: 'specResultTotal' },
        inner: { label: '내부길이', color: '#00E5FF', resultId: 'specResultInner', deleteId: 'specDelInner' },
        inner_1: { label: '내부길이 1', color: '#C6FF00', resultId: 'specResultInner1', deleteId: 'specDelInner1' },
        inner_2: { label: '내부길이 2', color: '#FFE600', resultId: 'specResultInner2', deleteId: 'specDelInner2' }
    };

    var canvas = document.getElementById('specInfoCanvas');
    var emptyEl = document.getElementById('specInfoEmpty');
    var hintEl = document.getElementById('specInfoHint');
    var ctx = canvas.getContext('2d');
    var image = new Image();
    var items = Array.isArray(spec.psi_items) ? spec.psi_items.slice() : [];
    var imagePath = spec.psi_image_path || '';
    var pendingImageUrl = '';
    var hasImage = !!imagePath;
    var activeType = '';
    var pendingStart = null;
    var hoverX = null;
    var draggingItem = null;
    var draggingEnd = '';
    var dragMoved = false;
    var imageRotated = false;

    function setHint(text) {
        hintEl.textContent = text || '';
    }

    function findItem(type) {
        for (var i = 0; i < items.length; i++) {
            if (items[i].type === type) return items[i];
        }
        return null;
    }

    function upsertItem(item) {
        var next = [];
        var replaced = false;
        for (var i = 0; i < items.length; i++) {
            if (items[i].type === item.type) {
                next.push(item);
                replaced = true;
            } else {
                next.push(items[i]);
            }
        }
        if (!replaced) next.push(item);
        items = next;
    }

    function removeItem(type) {
        if (type === 'total') return;
        var def = itemDefs[type];
        if (!def || !findItem(type)) return;
        items = items.filter(function(item) {
            return item.type !== type;
        });
        if (activeType === type) {
            pendingStart = null;
            hoverX = null;
            activeType = '';
        }
        if (draggingItem && draggingItem.type === type) {
            draggingItem = null;
            draggingEnd = '';
            dragMoved = false;
        }
        recalc();
        redraw();
        setHint(def.label + '를 삭제했습니다. 저장해야 반영됩니다.');
    }

    function totalLengthCm() {
        var value = parseFloat(document.getElementById('specTotalCm').value);
        return value > 0 ? value : 0;
    }

    function recalc() {
        var total = findItem('total');
        var totalPx = total ? Math.abs(total.x2 - total.x1) : 0;
        var totalCm = totalLengthCm();
        for (var i = 0; i < items.length; i++) {
            var px = Math.abs(items[i].x2 - items[i].x1);
            items[i].px = Math.round(px * 100) / 100;
            if (items[i].type === 'total') {
                items[i].cm = totalCm || null;
            } else if (totalPx > 0 && totalCm > 0) {
                items[i].cm = Math.round((px / totalPx * totalCm) * 10) / 10;
            } else {
                items[i].cm = null;
            }
        }
        renderResults();
    }

    function formatCm(cm) {
        return cm === null || cm === undefined || cm === '' ? '-' : (Number(cm).toFixed(1) + ' cm');
    }

    function formatLineLabel(item) {
        var def = itemDefs[item.type] || itemDefs.inner;
        if (item.cm === null || item.cm === undefined || item.cm === '') {
            return def.label;
        }
        return def.label + ' - ' + Number(item.cm).toFixed(1) + 'cm';
    }

    function renderResults() {
        Object.keys(itemDefs).forEach(function(type) {
            var item = findItem(type);
            var el = document.getElementById(itemDefs[type].resultId);
            el.textContent = item ? formatCm(item.cm) : '-';
            var deleteBtn = itemDefs[type].deleteId ? document.getElementById(itemDefs[type].deleteId) : null;
            if (deleteBtn) {
                deleteBtn.style.display = item ? 'inline-block' : 'none';
            }
        });
        var total = findItem('total');
        if (total && total.cm) {
            document.getElementById('specTotalCm').value = total.cm;
        }
        document.getElementById('specStepUpload').classList.toggle('active', !hasImage);
        document.getElementById('specStepTotal').classList.toggle('active', !!hasImage && !findItem('total'));
        document.getElementById('specStepInner').classList.toggle('active', !!findItem('total') && totalLengthCm() > 0);
    }

    function canvasPoint(evt) {
        var rect = canvas.getBoundingClientRect();
        return {
            x: (evt.clientX - rect.left) * (canvas.width / rect.width),
            y: (evt.clientY - rect.top) * (canvas.height / rect.height)
        };
    }

    function hitPad() {
        return Math.max(14, canvas.width / 70);
    }

    function findLineAtPoint(point) {
        var pad = hitPad();
        var best = null;
        var bestDist = pad + 1;
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var minX = Math.min(item.x1, item.x2);
            var maxX = Math.max(item.x1, item.x2);
            if (point.x < minX - pad || point.x > maxX + pad) continue;
            var dist = Math.abs(point.y - item.y1);
            if (dist <= pad && dist < bestDist) {
                best = item;
                bestDist = dist;
            }
        }
        return best;
    }

    function findHandleAtPoint(point) {
        var pad = hitPad() * 1.3;
        var best = null;
        var bestDist = pad + 1;
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var ends = ['x1', 'x2'];
            for (var e = 0; e < ends.length; e++) {
                var end = ends[e];
                var dx = point.x - item[end];
                var dy = point.y - item.y1;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist <= pad && dist < bestDist) {
                    best = { item: item, end: end };
                    bestDist = dist;
                }
            }
        }
        return best;
    }

    function moveItemY(item, y) {
        if (!item) return;
        y = Math.max(0, Math.min(canvas.height, y));
        y = Math.round(y * 100) / 100;
        item.y1 = y;
        item.y2 = y;
        redraw();
    }

    function moveItemEnd(item, end, x) {
        if (!item || (end !== 'x1' && end !== 'x2')) return;
        x = Math.max(0, Math.min(canvas.width, x));
        var other = end === 'x1' ? item.x2 : item.x1;
        if (Math.abs(x - other) < 2) {
            x = other + (x >= other ? 2 : -2);
            x = Math.max(0, Math.min(canvas.width, x));
        }
        item[end] = Math.round(x * 100) / 100;
        item.y2 = item.y1;
        recalc();
        redraw();
    }

    function cancelPendingStart() {
        if (!pendingStart) return false;
        pendingStart = null;
        hoverX = null;
        redraw();
        if (activeType && itemDefs[activeType]) {
            setHint(itemDefs[activeType].label + ' 시작점을 다시 클릭하세요. 잘못 찍으면 ESC로 취소할 수 있습니다.');
        }
        return true;
    }

    function drawLine(item, dashed) {
        var def = itemDefs[item.type] || itemDefs.inner;
        var lineWidth = Math.max(3, canvas.width / 420);
        var cap = Math.max(8, canvas.width / 160);
        var label = formatLineLabel(item);
        var labelX = Math.min(item.x1, item.x2);
        var labelY = item.y1 - cap - 4;
        ctx.save();
        if (dashed) ctx.setLineDash([8, 6]);
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#111827';
        ctx.lineWidth = lineWidth + Math.max(3, lineWidth * 0.8);
        ctx.beginPath();
        ctx.moveTo(item.x1, item.y1);
        ctx.lineTo(item.x2, item.y1);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(item.x1, item.y1 - cap);
        ctx.lineTo(item.x1, item.y1 + cap);
        ctx.moveTo(item.x2, item.y1 - cap);
        ctx.lineTo(item.x2, item.y1 + cap);
        ctx.stroke();
        ctx.strokeStyle = def.color;
        ctx.lineWidth = lineWidth;
        ctx.beginPath();
        ctx.moveTo(item.x1, item.y1);
        ctx.lineTo(item.x2, item.y1);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(item.x1, item.y1 - cap);
        ctx.lineTo(item.x1, item.y1 + cap);
        ctx.moveTo(item.x2, item.y1 - cap);
        ctx.lineTo(item.x2, item.y1 + cap);
        ctx.stroke();
        ctx.setLineDash([]);
        ctx.font = 'bold ' + Math.max(14, Math.round(canvas.width / 62)) + 'px sans-serif';
        ctx.lineWidth = 4;
        ctx.strokeStyle = '#111827';
        ctx.strokeText(label, labelX, labelY);
        ctx.fillStyle = def.color;
        ctx.fillText(label, labelX, labelY);
        if (!dashed) {
            var handleR = Math.max(6, canvas.width / 140);
            ['x1', 'x2'].forEach(function(end) {
                ctx.beginPath();
                ctx.arc(item[end], item.y1, handleR + 2, 0, Math.PI * 2);
                ctx.fillStyle = '#111827';
                ctx.fill();
                ctx.beginPath();
                ctx.arc(item[end], item.y1, handleR, 0, Math.PI * 2);
                ctx.fillStyle = def.color;
                ctx.fill();
            });
        }
        ctx.restore();
    }

    function redraw() {
        if (!hasImage || !image.naturalWidth) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
        items.forEach(function(item) {
            drawLine(item, false);
        });
        if (pendingStart && hoverX !== null) {
            drawLine({
                type: activeType || 'inner',
                x1: pendingStart.x,
                y1: pendingStart.y,
                x2: hoverX,
                cm: null
            }, true);
        }
    }

    function showImage(src, onReady) {
        image.onload = function() {
            hasImage = true;
            canvas.width = image.naturalWidth;
            canvas.height = image.naturalHeight;
            canvas.style.display = 'block';
            emptyEl.style.display = 'none';
            document.getElementById('specRotateBar').style.display = 'flex';
            redraw();
            if (typeof onReady === 'function') onReady();
        };
        image.onerror = function() {
            hasImage = !!imagePath && !pendingImageUrl;
            showAlert('Error', '이미지를 불러오지 못했습니다. URL을 확인해주세요.', 'alert2');
            setHint('이미지를 다시 불러와 주세요.');
        };
        image.src = src;
    }

    function resetMeasureState() {
        items = [];
        pendingStart = null;
        hoverX = null;
        activeType = '';
        draggingItem = null;
        draggingEnd = '';
        dragMoved = false;
        document.getElementById('specTotalCm').value = '';
        renderResults();
    }

    function rotateWorkingImage(dir) {
        if (!hasImage || !image.naturalWidth) {
            setHint('먼저 단면도를 올려주세요.');
            return;
        }
        var srcW = image.naturalWidth;
        var srcH = image.naturalHeight;
        var off = document.createElement('canvas');
        off.width = srcH;
        off.height = srcW;
        var octx = off.getContext('2d');
        if (dir > 0) {
            octx.translate(srcH, 0);
        } else {
            octx.translate(0, srcW);
        }
        octx.rotate(dir * Math.PI / 2);
        octx.drawImage(image, 0, 0);
        imageRotated = true;
        pendingImageUrl = '';
        resetMeasureState();
        showImage(off.toDataURL('image/jpeg', 0.92), function() {
            setHint('이미지를 회전했습니다. 측정선을 다시 지정하세요.');
        });
    }

    function saveSpecInfo() {
        recalc();
        if (!findItem('total') || totalLengthCm() <= 0) {
            showAlert('Error', '전체길이를 지정하고 cm를 입력해주세요.', 'alert2');
            return;
        }
        if (!findItem('inner') && !findItem('inner_1') && !findItem('inner_2')) {
            showAlert('Error', '내부길이를 한 개 이상 지정해주세요.', 'alert2');
            return;
        }

        var formData = new FormData();
        formData.append('prd_idx', prdIdx);
        formData.append('psi_total_length_cm', totalLengthCm());
        formData.append('psi_items', JSON.stringify(items));
        if (pendingImageUrl && !imageRotated) {
            formData.append('image_url', pendingImageUrl);
        }

        function postSave() {
            $.ajax({
                url: '/admin/product/detail_spec_info/save',
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (!res || !res.success) {
                        showAlert('Error', (res && (res.message || res.msg)) || '저장에 실패했습니다.', 'alert2');
                        return;
                    }
                    spec = res.data || spec;
                    items = Array.isArray(spec.psi_items) ? spec.psi_items.slice() : items;
                    imagePath = spec.psi_image_path || imagePath;
                    pendingImageUrl = '';
                    imageRotated = false;
                    hasImage = !!imagePath || hasImage;
                    renderResults();
                    redraw();
                    if (typeof toast2 === 'function') {
                        toast2('success', '상품 스펙정보', '측정값을 저장했습니다.');
                    } else {
                        alert('측정값을 저장했습니다.');
                    }
                },
                error: function(request) {
                    var message = '저장에 실패했습니다.';
                    try {
                        var parsed = JSON.parse(request.responseText);
                        message = parsed.message || message;
                    } catch (e) {}
                    showAlert('Error', message, 'alert2');
                }
            });
        }

        if (!imageRotated) {
            postSave();
            return;
        }
        var off = document.createElement('canvas');
        off.width = image.naturalWidth;
        off.height = image.naturalHeight;
        off.getContext('2d').drawImage(image, 0, 0);
        off.toBlob(function(blob) {
            if (blob) {
                formData.append('rotated_image', blob, 'rotated.jpg');
            }
            postSave();
        }, 'image/jpeg', 0.92);
    }

    function previewFromUrl() {
        var url = (document.getElementById('specInfoUrl').value || '').trim();
        if (!/^https?:\/\//i.test(url)) {
            showAlert('Error', 'http/https 이미지 URL을 입력해주세요.', 'alert2');
            return;
        }
        pendingImageUrl = url;
        imagePath = '';
        imageRotated = false;
        resetMeasureState();
        setHint('이미지를 불러오는 중...');
        showImage('/admin/product/detail_spec_info/preview?url=' + encodeURIComponent(url), function() {
            setHint('전체길이 시작/끝을 지정하세요. 지금은 미리보기이며 저장할 때 서버에 등록됩니다.');
        });
    }

    function startPick(type) {
        if (!hasImage) {
            setHint('먼저 파일 또는 URL로 단면도를 올려주세요.');
            return;
        }
        if (type !== 'total' && (!findItem('total') || totalLengthCm() <= 0)) {
            setHint('전체길이를 지정하고 cm를 입력한 뒤 내부길이를 지정하세요.');
            return;
        }
        activeType = type;
        pendingStart = null;
        hoverX = null;
        draggingItem = null;
        draggingEnd = '';
        canvas.style.cursor = 'crosshair';
        setHint(itemDefs[type].label + ' 시작점을 클릭하세요. 잘못 찍으면 ESC로 시작점을 취소할 수 있습니다.');
    }

    function finishPick(endX) {
        var start = pendingStart;
        upsertItem({
            type: activeType,
            label: itemDefs[activeType].label,
            x1: Math.round(start.x * 100) / 100,
            y1: Math.round(start.y * 100) / 100,
            x2: Math.round(endX * 100) / 100,
            y2: Math.round(start.y * 100) / 100,
            px: Math.round(Math.abs(endX - start.x) * 100) / 100,
            cm: activeType === 'total' ? (totalLengthCm() || null) : null
        });
        pendingStart = null;
        hoverX = null;
        activeType = '';
        recalc();
        redraw();
        if (findItem('total') && !totalLengthCm()) {
            document.getElementById('specTotalCm').focus();
            setHint('전체길이 cm를 입력하세요.');
            return;
        }
        setHint('끝점을 드래그하면 길이를 조절할 수 있습니다. 선 중간은 위아래로 옮길 수 있습니다.');
    }

    canvas.addEventListener('mousedown', function(evt) {
        if (!hasImage || activeType || pendingStart) return;
        var point = canvasPoint(evt);
        var handle = findHandleAtPoint(point);
        if (handle) {
            draggingItem = handle.item;
            draggingEnd = handle.end;
            dragMoved = false;
            canvas.style.cursor = 'ew-resize';
            evt.preventDefault();
            return;
        }
        var hit = findLineAtPoint(point);
        if (!hit) return;
        draggingItem = hit;
        draggingEnd = '';
        dragMoved = false;
        canvas.style.cursor = 'ns-resize';
        evt.preventDefault();
    });

    canvas.addEventListener('click', function(evt) {
        if (dragMoved) {
            dragMoved = false;
            return;
        }
        if (!hasImage || !activeType) {
            if (hasImage && !activeType) {
                setHint(findItem('total') ? '끝점을 드래그해 길이를 조절하거나, 선을 위아래로 옮길 수 있습니다.' : '오른쪽에서 지정할 길이를 먼저 선택하세요.');
            }
            return;
        }
        var point = canvasPoint(evt);
        if (!pendingStart) {
            pendingStart = point;
            hoverX = point.x;
            setHint(itemDefs[activeType].label + ' 끝점을 클릭하세요. 시작점을 다시 찍으려면 ESC를 누르세요.');
            redraw();
            return;
        }
        finishPick(point.x);
    });

    canvas.addEventListener('mousemove', function(evt) {
        var point = canvasPoint(evt);
        if (draggingItem && draggingEnd) {
            dragMoved = true;
            moveItemEnd(draggingItem, draggingEnd, point.x);
            canvas.style.cursor = 'ew-resize';
            return;
        }
        if (draggingItem) {
            dragMoved = true;
            moveItemY(draggingItem, point.y);
            canvas.style.cursor = 'ns-resize';
            return;
        }
        if (pendingStart) {
            hoverX = point.x;
            redraw();
            return;
        }
        if (!activeType && findHandleAtPoint(point)) {
            canvas.style.cursor = 'ew-resize';
            return;
        }
        canvas.style.cursor = (!activeType && findLineAtPoint(point)) ? 'ns-resize' : 'crosshair';
    });

    window.addEventListener('mouseup', function() {
        if (!draggingItem) return;
        var movedItem = draggingItem;
        var movedEnd = draggingEnd;
        draggingItem = null;
        draggingEnd = '';
        if (dragMoved && movedItem) {
            var label = (itemDefs[movedItem.type] || {}).label || '선';
            if (movedEnd) {
                setHint(label + ' 길이를 조절했습니다. 저장해야 반영됩니다.');
            } else {
                setHint(label + ' 세로 위치를 옮겼습니다. 가로 길이는 그대로입니다.');
            }
        }
    });

    window.addEventListener('keydown', function(evt) {
        if (evt.key !== 'Escape' && evt.key !== 'Esc') return;
        if (cancelPendingStart()) {
            evt.preventDefault();
        }
    });

    document.getElementById('specBtnTotal').addEventListener('click', function() {
        startPick('total');
    });
    document.getElementById('specBtnInner').addEventListener('click', function() {
        startPick('inner');
    });
    document.getElementById('specBtnInner1').addEventListener('click', function() {
        startPick('inner_1');
    });
    document.getElementById('specBtnInner2').addEventListener('click', function() {
        startPick('inner_2');
    });
    document.querySelectorAll('.spec-info-del').forEach(function(btn) {
        btn.addEventListener('click', function() {
            removeItem(btn.getAttribute('data-type') || '');
        });
    });
    document.getElementById('specTotalCm').addEventListener('input', function() {
        recalc();
        redraw();
    });
    document.getElementById('specBtnRotateLeft').addEventListener('click', function() {
        rotateWorkingImage(-1);
    });
    document.getElementById('specBtnRotateRight').addEventListener('click', function() {
        rotateWorkingImage(1);
    });
    document.getElementById('specBtnLoadUrl').addEventListener('click', previewFromUrl);
    document.getElementById('specInfoUrl').addEventListener('keydown', function(evt) {
        if (evt.key === 'Enter') {
            evt.preventDefault();
            previewFromUrl();
        }
    });

    document.getElementById('specInfoFile').addEventListener('change', function() {
        var file = this.files && this.files[0];
        if (!file) return;
        var formData = new FormData();
        formData.append('prd_idx', prdIdx);
        formData.append('spec_image', file);
        $.ajax({
            url: '/admin/product/detail_spec_info/image',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (!res || !res.success) {
                    showAlert('Error', (res && (res.message || res.msg)) || '업로드에 실패했습니다.', 'alert2');
                    return;
                }
                spec = res.data || {};
                items = Array.isArray(spec.psi_items) ? spec.psi_items.slice() : [];
                imagePath = spec.psi_image_path || '';
                pendingImageUrl = '';
                imageRotated = false;
                document.getElementById('specInfoUrl').value = '';
                document.getElementById('specTotalCm').value = spec.psi_total_length_cm || '';
                pendingStart = null;
                activeType = '';
                hasImage = !!imagePath;
                renderResults();
                if (imagePath) {
                    showImage(imagePath);
                    setHint('전체길이 시작/끝을 지정하세요.');
                }
            },
            error: function(request) {
                var message = '업로드에 실패했습니다.';
                try {
                    var parsed = JSON.parse(request.responseText);
                    message = parsed.message || message;
                } catch (e) {}
                showAlert('Error', message, 'alert2');
            }
        });
        this.value = '';
    });

    document.getElementById('specBtnSave').addEventListener('click', saveSpecInfo);

    renderResults();
    if (imagePath) {
        showImage(imagePath);
        setHint('선을 다시 지정하거나 저장할 수 있습니다.');
    }
})();
</script>
