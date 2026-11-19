<?php
$prdIdx = (int)($prd_idx ?? 0);
$imageStoragePath = trim((string)($image_storage_path ?? ''));
$imageStorageUrl = trim((string)($image_storage_url ?? ''));
$images = is_array($images ?? null) ? $images : [];
$listError = trim((string)($list_error ?? ''));
$h = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<style>
.provider-content { background: #fff; padding: 16px; }
.provider-content-head { margin-bottom: 12px; }
.provider-content-head h1 { margin: 0; font-size: 20px; font-weight: 600; color: #111827; }
.provider-content-tabs { display: flex; gap: 0; margin: 0 0 14px; border-bottom: 1px solid #999; }
.provider-content-tab { width: 200px; margin: 0 0 -1px; padding: 8px 25px; border: 1px solid #bbb; border-bottom: 0; background: none; color: #333; font-size: 14px; font-weight: 600; line-height: 1.4; cursor: pointer; }
.provider-content-tab.is-active { color: #111827; background: #fff; border-color: #999; border-bottom: 1px solid #fff; }
.provider-content-panel[hidden] { display: none !important; }
.provider-content-wip { margin: 0; padding: 48px 16px; color: #6b7280; font-size: 15px; font-weight: 700; text-align: center; }
.section-title-name { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 5px; }
.prd-image-manage { max-width: 980px; }
.prd-image-manage-location { margin: 0 0 12px; padding: 12px 14px; border: 1px solid #dbe3f0; border-radius: 8px; background: #f8fafc; }
.prd-image-manage-location p { display: flex; gap: 8px; margin: 0; color: #111827; font-size: 13px; line-height: 1.5; word-break: break-all; }
.prd-image-manage-location p + p { margin-top: 4px; }
.prd-image-manage-location span { flex: 0 0 36px; color: #64748b; font-weight: 700; }
.prd-image-manage-location a { color: #1d4ed8; }
.prd-image-manage-notice { margin: 0 0 12px; padding: 9px 11px; border-radius: 5px; color: #92400e; background: #fef3c7; font-size: 13px; }
.prd-image-manage-notice[hidden],
.prd-image-manage-work[hidden] { display: none; }
.prd-image-manage-drop { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; min-height: 160px; margin-top: 8px; padding: 22px 16px; border: 2px dashed #93c5fd; border-radius: 12px; background: #f8fbff; color: #1e3a8a; text-align: center; cursor: pointer; }
.prd-image-manage-drop.is-over { border-color: #2563eb; background: #dbeafe; }
.prd-image-manage-drop strong { font-size: 15px; }
.prd-image-manage-drop span { color: #64748b; font-size: 12px; }
.prd-image-manage-queue { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 8px; margin-top: 12px; }
.prd-image-manage-queue[hidden] { display: none; }
.prd-image-manage-queue-item { position: relative; overflow: hidden; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; }
.prd-image-manage-queue-item img { display: block; width: 100%; aspect-ratio: 1; object-fit: contain; background: #f8fafc; }
.prd-image-manage-queue-item span { display: block; padding: 6px 28px 6px 8px; overflow: hidden; color: #374151; font-size: 11px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
.prd-image-manage-queue-remove { position: absolute; top: 4px; right: 4px; width: 22px; height: 22px; padding: 0; border: 0; border-radius: 999px; background: rgba(17, 24, 39, .72); color: #fff; font-size: 14px; line-height: 22px; cursor: pointer; }
.prd-image-manage-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
.prd-image-manage-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; margin-top: 10px; }
.prd-image-manage-grid[hidden] { display: none; }
.prd-image-manage-html-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 10px; }
.prd-image-manage-select-all { display: inline-flex; align-items: center; gap: 4px; color: #374151; font-size: 12px; }
.prd-image-manage-html { box-sizing: border-box; width: 100%; min-height: 88px; margin: 8px 0 0; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; background: #f8fafc; color: #111827; font-family: Consolas, Monaco, monospace; font-size: 12px; line-height: 1.5; resize: vertical; }
.prd-image-manage-order-help { margin: 8px 0 0; color: #6b7280; font-size: 12px; }
.prd-image-manage-card { display: block; overflow: hidden; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; color: inherit; text-decoration: none; cursor: grab; }
.prd-image-manage-card.is-dragging { opacity: .45; cursor: grabbing; }
.prd-image-manage-card.is-drop-target { outline: 2px dashed #2563eb; outline-offset: -2px; }
.prd-image-manage-card.is-checked { border-color: #2563eb; box-shadow: inset 0 0 0 1px #2563eb; }
.prd-image-manage-check { display: flex; align-items: center; gap: 4px; padding: 6px 8px 0; color: #374151; font-size: 12px; cursor: pointer; }
.prd-image-manage-card > a { display: block; color: inherit; text-decoration: none; }
.prd-image-manage-card img { display: block; width: 100%; aspect-ratio: 1; object-fit: contain; background: #f8fafc; }
.prd-image-manage-card strong,
.prd-image-manage-card em { display: block; padding: 0 8px; overflow: hidden; font-style: normal; text-overflow: ellipsis; white-space: nowrap; }
.prd-image-manage-card strong { padding-top: 6px; color: #111827; font-size: 12px; }
.prd-image-manage-card em { padding-bottom: 8px; color: #6b7280; font-size: 11px; }
.prd-content-library-empty { margin: 18px 0 8px; color: #9ca3af; text-align: center; font-size: 12px; line-height: 1.5; }
.prd-content-library-empty[hidden] { display: none; }
</style>

<div class="provider-content">
    <div class="provider-content-head">
        <h1>상품 컨텐츠 관리</h1>
    </div>
    <div class="provider-content-tabs" role="tablist">
        <button type="button" class="provider-content-tab is-active" data-provider-content-tab="top" role="tab" aria-selected="true">상단</button>
        <button type="button" class="provider-content-tab" data-provider-content-tab="bottom" role="tab" aria-selected="false">하단</button>
        <button type="button" class="provider-content-tab" data-provider-content-tab="images" role="tab" aria-selected="false">이미지 관리</button>
    </div>

    <div class="provider-content-panel" data-provider-content-panel="top" role="tabpanel">
        <p class="provider-content-wip">기능 작업중</p>
    </div>
    <div class="provider-content-panel" data-provider-content-panel="bottom" role="tabpanel" hidden>
        <p class="provider-content-wip">기능 작업중</p>
    </div>
    <div class="provider-content-panel" data-provider-content-panel="images" role="tabpanel" hidden>
        <div class="prd-image-manage">
            <div class="prd-image-manage-location">
                <p>
                    <span>URL</span>
                    <?php if ($imageStorageUrl !== '') { ?>
                        <a href="<?= $h($imageStorageUrl) ?>" target="_blank" rel="noopener"><?= $h($imageStorageUrl) ?></a>
                    <?php } else { ?>
                        <b>미설정</b>
                    <?php } ?>
                </p>
                <p>
                    <span>경로</span>
                    <?php if ($imageStoragePath !== '') { ?>
                        <code><?= $h($imageStoragePath) ?></code>
                    <?php } else { ?>
                        <b>미설정</b>
                    <?php } ?>
                </p>
            </div>
            <?php if ($listError !== '') { ?>
                <p class="prd-image-manage-notice"><?= $h($listError) ?></p>
            <?php } ?>
            <p class="prd-image-manage-notice" id="prd_image_manage_unset" <?= $imageStoragePath === '' ? '' : 'hidden' ?>>이미지 저장소가 없습니다. 상단 이미지 저장소에서 먼저 등록해 주세요.</p>
            <section id="prd_image_manage_work" class="prd-image-manage-work" <?= $imageStoragePath === '' ? 'hidden' : '' ?>>
                <h2 class="section-title-name">이미지 담기</h2>
                <div id="prd_image_manage_drop" class="prd-image-manage-drop" tabindex="0" role="button" aria-label="이미지를 끌어다 놓거나 클릭해서 선택">
                    <strong>PC에서 이미지를 이 영역으로 끌어다 놓으세요.</strong>
                    <span>클릭해서 파일을 고를 수도 있습니다. jpg, png, gif, webp</span>
                </div>
                <input type="file" id="prd_image_manage_input" accept="image/jpeg,image/png,image/gif,image/webp" multiple hidden>
                <div id="prd_image_manage_queue" class="prd-image-manage-queue" hidden></div>
                <div class="prd-image-manage-actions">
                    <button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm" id="prd_image_manage_upload" disabled>저장소에 업로드</button>
                    <button type="button" class="btnstyle1 btnstyle1-sm" id="prd_image_manage_clear" hidden>담은 이미지 비우기</button>
                    <button type="button" class="btnstyle1 btnstyle1-sm" id="prd_image_manage_refresh">저장소 목록 갱신</button>
                </div>
                <h2 class="section-title-name" style="margin-top:18px;">저장소 이미지 <span id="prd_image_manage_count"><?= count($images) ?></span></h2>
                <div class="prd-image-manage-html-bar">
                    <label class="prd-image-manage-select-all"><input type="checkbox" id="prd_image_manage_select_all"> 전체선택</label>
                    <button type="button" class="btnstyle1 btnstyle1-sm" id="prd_image_manage_html_copy">HTML 복사</button>
                </div>
                <textarea id="prd_image_manage_html" class="prd-image-manage-html" readonly placeholder="이미지를 선택하면 img 태그가 여기에 만들어집니다."></textarea>
                <p class="prd-image-manage-order-help">카드를 끌어 순서를 바꾸면 선택한 이미지의 HTML 순서도 함께 바뀝니다.</p>
                <div id="prd_image_manage_list" class="prd-image-manage-grid" <?= $images === [] ? 'hidden' : '' ?>></div>
                <p id="prd_image_manage_empty" class="prd-content-library-empty" <?= $images === [] ? '' : 'hidden' ?>>저장소에 올린 이미지가 없습니다.</p>
            </section>
        </div>
    </div>
</div>

<script>
(function() {
    var prdIdx = <?= json_encode($prdIdx) ?>;
    var imageStoragePath = <?= json_encode($imageStoragePath, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> || '';
    var imageLibraryItems = <?= json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> || [];
    var imageManageQueue = [];
    var draggedImageManageCard = null;

    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    function requestErrorMessage(err, fallback) {
        if (err && err.responseJSON && err.responseJSON.message) {
            return err.responseJSON.message;
        }
        if (err && err.message) {
            return err.message;
        }
        return fallback;
    }

    function activateProviderContentTab(name) {
        var tabName = (name === 'bottom' || name === 'images') ? name : 'top';
        $('[data-provider-content-tab]').removeClass('is-active').attr('aria-selected', 'false');
        $('[data-provider-content-tab="' + tabName + '"]').addClass('is-active').attr('aria-selected', 'true');
        $('[data-provider-content-panel]').attr('hidden', true);
        $('[data-provider-content-panel="' + tabName + '"]').removeAttr('hidden');
    }

    function renderImageManageList() {
        var $list = $('#prd_image_manage_list');
        var $empty = $('#prd_image_manage_empty');
        var $count = $('#prd_image_manage_count');
        if (!$list.length) {
            return;
        }
        var selectedUrls = {};
        $list.find('.prd-image-manage-select:checked').each(function() {
            selectedUrls[$(this).attr('data-hosting-url') || ''] = true;
        });
        var html = '';
        var count = 0;
        imageLibraryItems.forEach(function(item) {
            var url = String((item && item.hosting_url) || '');
            var name = String((item && item.filename) || '');
            var meta = String((item && item.meta_label) || '');
            if (!url) {
                return;
            }
            if (!name) {
                name = url.split('/').pop() || url;
            }
            var checked = !!selectedUrls[url];
            html += '<div class="prd-image-manage-card' + (checked ? ' is-checked' : '') + '" draggable="true">'
                + '<label class="prd-image-manage-check"><input type="checkbox" class="prd-image-manage-select" data-hosting-url="' + escapeHtml(url) + '"' + (checked ? ' checked' : '') + '> 선택</label>'
                + '<a href="' + escapeHtml(url) + '" target="_blank" rel="noopener" title="' + escapeHtml(name) + '">'
                + '<img src="' + escapeHtml(url) + '" alt="' + escapeHtml(name) + '" draggable="false">'
                + '<strong>' + escapeHtml(name) + '</strong>'
                + '<em>' + escapeHtml(meta || '저장소 이미지') + '</em>'
                + '</a>'
                + '</div>';
            count += 1;
        });
        $list.html(html);
        $count.text(count);
        if (html) {
            $list.removeAttr('hidden');
            $empty.attr('hidden', true);
        } else {
            $list.attr('hidden', true);
            $empty.removeAttr('hidden');
        }
        syncImageManageSelectAll();
        buildImageManageHtml();
    }

    function selectedImageManageUrls() {
        var urls = [];
        $('#prd_image_manage_list .prd-image-manage-select:checked').each(function() {
            var url = $(this).attr('data-hosting-url') || '';
            if (url) {
                urls.push(url);
            }
        });
        return urls;
    }

    function buildImageManageHtml() {
        var html = selectedImageManageUrls().map(function(url) {
            return '<img src="' + url + '"><br>';
        }).join('\n');
        $('#prd_image_manage_html').val(html);
    }

    function syncImageManageOrder() {
        var order = [];
        $('#prd_image_manage_list .prd-image-manage-select').each(function() {
            var url = $(this).attr('data-hosting-url') || '';
            if (url) {
                order.push(url);
            }
        });
        var byUrl = {};
        imageLibraryItems.forEach(function(item) {
            var url = String((item && item.hosting_url) || '');
            if (url && !byUrl[url]) {
                byUrl[url] = item;
            }
        });
        var nextItems = [];
        order.forEach(function(url) {
            if (!byUrl[url]) {
                return;
            }
            nextItems.push(byUrl[url]);
            delete byUrl[url];
        });
        Object.keys(byUrl).forEach(function(url) {
            nextItems.push(byUrl[url]);
        });
        imageLibraryItems = nextItems;
        buildImageManageHtml();
    }

    function syncImageManageSelectAll() {
        var $boxes = $('#prd_image_manage_list .prd-image-manage-select');
        var checkedCount = $boxes.filter(':checked').length;
        var $all = $('#prd_image_manage_select_all');
        $all.prop('checked', $boxes.length > 0 && checkedCount === $boxes.length);
        $all.prop('indeterminate', checkedCount > 0 && checkedCount < $boxes.length);
    }

    function revokeImageManageQueue() {
        imageManageQueue.forEach(function(item) {
            if (item.previewUrl) {
                URL.revokeObjectURL(item.previewUrl);
            }
        });
        imageManageQueue = [];
    }

    function renderImageManageQueue() {
        var $queue = $('#prd_image_manage_queue');
        var html = '';
        imageManageQueue.forEach(function(item, index) {
            html += '<div class="prd-image-manage-queue-item">'
                + '<img src="' + escapeHtml(item.previewUrl) + '" alt="">'
                + '<span>' + escapeHtml(item.file.name) + '</span>'
                + '<button type="button" class="prd-image-manage-queue-remove" data-provider-image-queue-remove="' + index + '" aria-label="빼기">×</button>'
                + '</div>';
        });
        $queue.html(html);
        if (html) {
            $queue.removeAttr('hidden');
            $('#prd_image_manage_clear').removeAttr('hidden');
        } else {
            $queue.attr('hidden', true);
            $('#prd_image_manage_clear').attr('hidden', true);
        }
        var $upload = $('#prd_image_manage_upload');
        $upload.prop('disabled', imageManageQueue.length === 0 || imageStoragePath === '');
        $upload.text(imageManageQueue.length > 0 ? imageManageQueue.length + '장 저장소에 업로드' : '저장소에 업로드');
    }

    function addImageManageFiles(fileList) {
        if (!imageStoragePath) {
            showAlert('이미지 관리', '이미지 저장소 설정을 먼저 완료해 주세요.', 'alert2');
            return;
        }
        var allowed = { 'image/jpeg': true, 'image/png': true, 'image/gif': true, 'image/webp': true };
        Array.prototype.forEach.call(fileList || [], function(file) {
            if (!file || !allowed[file.type]) {
                return;
            }
            var exists = imageManageQueue.some(function(item) {
                return item.file.name === file.name && item.file.size === file.size && item.file.lastModified === file.lastModified;
            });
            if (exists) {
                return;
            }
            imageManageQueue.push({
                file: file,
                previewUrl: URL.createObjectURL(file)
            });
        });
        renderImageManageQueue();
    }

    $(document).off('click.providerContentTab').on('click.providerContentTab', '[data-provider-content-tab]', function() {
        activateProviderContentTab($(this).attr('data-provider-content-tab'));
    });

    $('#prd_image_manage_list').on('dragstart', '.prd-image-manage-card', function(event) {
        draggedImageManageCard = this;
        this.classList.add('is-dragging');
        var transfer = event.originalEvent && event.originalEvent.dataTransfer;
        if (transfer) {
            transfer.effectAllowed = 'move';
            transfer.setData('text/plain', $(this).find('.prd-image-manage-select').attr('data-hosting-url') || '');
        }
    }).on('dragend', '.prd-image-manage-card', function() {
        this.classList.remove('is-dragging');
        $('#prd_image_manage_list .prd-image-manage-card').removeClass('is-drop-target');
        draggedImageManageCard = null;
    }).on('dragover', '.prd-image-manage-card', function(event) {
        if (!draggedImageManageCard || draggedImageManageCard === this) {
            return;
        }
        event.preventDefault();
        $(this).addClass('is-drop-target');
        var transfer = event.originalEvent && event.originalEvent.dataTransfer;
        if (transfer) {
            transfer.dropEffect = 'move';
        }
    }).on('dragleave', '.prd-image-manage-card', function() {
        this.classList.remove('is-drop-target');
    }).on('drop', '.prd-image-manage-card', function(event) {
        event.preventDefault();
        this.classList.remove('is-drop-target');
        if (!draggedImageManageCard || draggedImageManageCard === this) {
            return;
        }
        var pointer = event.originalEvent || event;
        var rect = this.getBoundingClientRect();
        var centerY = rect.top + (rect.height / 2);
        var sameRow = Math.abs(pointer.clientY - centerY) < (rect.height / 2);
        var insertAfter = sameRow
            ? pointer.clientX > rect.left + (rect.width / 2)
            : pointer.clientY > centerY;
        this.parentNode.insertBefore(draggedImageManageCard, insertAfter ? this.nextSibling : this);
        syncImageManageOrder();
    });

    $('#prd_image_manage_list').on('change', '.prd-image-manage-select', function() {
        $(this).closest('.prd-image-manage-card').toggleClass('is-checked', this.checked);
        syncImageManageSelectAll();
        buildImageManageHtml();
    });

    $('#prd_image_manage_select_all').on('change', function() {
        var checked = this.checked;
        $('#prd_image_manage_list .prd-image-manage-select').prop('checked', checked);
        $('#prd_image_manage_list .prd-image-manage-card').toggleClass('is-checked', checked);
        buildImageManageHtml();
    });

    $('#prd_image_manage_html_copy').on('click', function() {
        var $button = $(this);
        var text = $('#prd_image_manage_html').val();
        if (!text) {
            showAlert('이미지 관리', '복사할 이미지를 선택해 주세요.', 'alert2');
            return;
        }
        var originalLabel = $button.text();
        var complete = function() {
            $button.text('복사 완료');
            setTimeout(function() {
                $button.text(originalLabel);
            }, 1500);
        };
        var textarea = document.getElementById('prd_image_manage_html');
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(complete);
            return;
        }
        textarea.focus();
        textarea.select();
        document.execCommand('copy');
        complete();
    });

    $('#prd_image_manage_drop').on('click', function() {
        if (!imageStoragePath) {
            return;
        }
        $('#prd_image_manage_input').val('').trigger('click');
    }).on('dragenter dragover', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).addClass('is-over');
    }).on('dragleave drop', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).removeClass('is-over');
    }).on('drop', function(event) {
        var files = event.originalEvent && event.originalEvent.dataTransfer ? event.originalEvent.dataTransfer.files : null;
        addImageManageFiles(files);
    });

    $('#prd_image_manage_input').on('change', function() {
        addImageManageFiles(this.files);
    });

    $(document).off('click.providerImageQueue').on('click.providerImageQueue', '[data-provider-image-queue-remove]', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var index = parseInt($(this).attr('data-provider-image-queue-remove'), 10);
        if (isNaN(index) || !imageManageQueue[index]) {
            return;
        }
        if (imageManageQueue[index].previewUrl) {
            URL.revokeObjectURL(imageManageQueue[index].previewUrl);
        }
        imageManageQueue.splice(index, 1);
        renderImageManageQueue();
    });

    $('#prd_image_manage_clear').on('click', function() {
        revokeImageManageQueue();
        renderImageManageQueue();
    });

    $('#prd_image_manage_upload').on('click', function() {
        var $btn = $(this);
        if ($btn.prop('disabled') || imageManageQueue.length === 0) {
            return;
        }
        var formData = new FormData();
        formData.append('prd_idx', prdIdx);
        imageManageQueue.forEach(function(item) {
            formData.append('images[]', item.file);
        });
        $btn.prop('disabled', true);
        ajaxRequest('/admin/provider_product/image_storage/upload', formData, {
            processData: false,
            contentType: false
        }).done(function(res) {
            if (res && res.success) {
                imageLibraryItems = (res.data && res.data.items) ? res.data.items : imageLibraryItems;
                renderImageManageList();
                revokeImageManageQueue();
                renderImageManageQueue();
                toast2('success', '이미지 관리', res.message || '이미지를 저장소에 업로드했습니다.');
            } else {
                showAlert('이미지 관리', (res && (res.message || res.msg)) || '이미지를 업로드하지 못했습니다.', 'alert2');
                $btn.prop('disabled', false);
            }
        }).fail(function(err) {
            showAlert('이미지 관리', requestErrorMessage(err, '이미지를 업로드하지 못했습니다.'), 'alert2');
            $btn.prop('disabled', false);
        });
    });

    $('#prd_image_manage_refresh').on('click', function() {
        var $btn = $(this);
        if ($btn.prop('disabled') || !imageStoragePath) {
            return;
        }
        $btn.prop('disabled', true);
        ajaxRequest('/admin/provider_product/image_storage/refresh', {
            prd_idx: prdIdx
        }).done(function(res) {
            if (res && res.success) {
                imageLibraryItems = (res.data && res.data.items) ? res.data.items : [];
                renderImageManageList();
                toast2('success', '이미지 관리', res.message || '저장소 목록을 갱신했습니다.');
            } else {
                showAlert('이미지 관리', (res && (res.message || res.msg)) || '저장소 목록을 불러오지 못했습니다.', 'alert2');
            }
        }).fail(function(err) {
            showAlert('이미지 관리', requestErrorMessage(err, '저장소 목록을 불러오지 못했습니다.'), 'alert2');
        }).always(function() {
            $btn.prop('disabled', false);
        });
    });

    if (window.__providerContentOpenTab === 'images' || window.__providerContentOpenTab === 'bottom' || window.__providerContentOpenTab === 'top') {
        activateProviderContentTab(window.__providerContentOpenTab);
        window.__providerContentOpenTab = '';
    }
    renderImageManageList();
})();
</script>
