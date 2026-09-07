<?php
$oopIdx = (int)($oopIdx ?? 0);
$products = is_array($products ?? null) ? $products : [];
$brandForSelect = is_array($brandForSelect ?? null) ? $brandForSelect : [];
$errorMessage = trim((string)($errorMessage ?? ''));
$productCount = count($products);
?>
<div class="prd-search-add-wrap">
	<ul class="left">
		<div>
			<?= $oopIdx ?>
		</div>

		<div>
			<ul>
				<select name="s_brand" id="formGroupPrdBrand" class="dn-select2">
					<option value="">브랜드</option>
					<?php foreach ($brandForSelect as $brand) { ?>
						<option value="<?= htmlspecialchars((string)($brand['BD_IDX'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)($brand['BD_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
					<?php } ?>
				</select>
			</ul>
			<ul>
				<input type="text" name="prdSearch" id="formGroupPrdSearch" value="" autocomplete="off" placeholder="검색어">
			</ul>
		</div>
		<div class="m-t-5 m-b-10 text-center">
			<button type="button" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="osFormGroupReg.prdSearch(this);">상품검색</button>
		</div>
		<div id="formGroupPrdSearchResult">
		</div>
	</ul>
	<ul class="right">
		<?php if ($errorMessage !== '') { ?>
			<div class="m-b-8" style="color:#c0392b;"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
		<?php } ?>
		<div>※ 추가된 상품은 저장을 눌러야 최종 적용됩니다. / 총상품 : <b><?= $productCount ?></b></div>
		<div class="prd-search-add-prd-list-wrap">
			<form id="formGroupPrd">
				<input type="hidden" name="idx" value="<?= $oopIdx ?>">
				<div id="formGroupPrdList" class="prd-search-add-prd-list-table">
					<?php foreach ($products as $product) {
						$prdIdx = htmlspecialchars((string)($product['prd_idx'] ?? ''), ENT_QUOTES, 'UTF-8');
						$psIdx = htmlspecialchars((string)($product['ps_idx'] ?? ''), ENT_QUOTES, 'UTF-8');
						$pname = htmlspecialchars((string)($product['pname'] ?? ''), ENT_QUOTES, 'UTF-8');
						$om = htmlspecialchars((string)($product['om'] ?? ''), ENT_QUOTES, 'UTF-8');
						$state = (string)($product['state'] ?? 'on');
						$code2 = htmlspecialchars((string)($product['code2'] ?? ''), ENT_QUOTES, 'UTF-8');
						$code3 = htmlspecialchars((string)($product['code3'] ?? ''), ENT_QUOTES, 'UTF-8');
						$imgPath = htmlspecialchars((string)($product['img_path'] ?? ''), ENT_QUOTES, 'UTF-8');
					?>
						<ul class="add-prd" data-prdidx="<?= $prdIdx ?>">
							<input type="hidden" name="prd_idx[]" value="<?= $prdIdx ?>">
							<input type="hidden" name="ps_idx[]" value="<?= $psIdx ?>">
							<li class="text-center" style="width:30px">
								<input type="checkbox" class="prd-select-chk">
							</li>
							<li class="text-center" style="width:40px">
								<p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p>
							</li>
							<li class="text-center" style="width:45px"><?= $prdIdx ?></li>
							<li class="text-center" style="width:55px"><img src="<?= $imgPath ?>" style="height:45px; border:1px solid #ddd;"></li>
							<li class="text-center" style="width:80px">
								<b><?= $code2 ?></b>
								<?php if ($code3 !== '') { ?><br><?= $code3 ?><?php } ?>
							</li>
							<li>
								<div>
									<?= $pname ?>
									<button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView('<?= $prdIdx ?>','info');">보기</button>
								</div>
								<div>
									<input type="text" name="ordermemo[]" value="<?= $om ?>" class="m-t-2" placeholder="주문 메모" style="font-size:12px;">
								</div>
							</li>
							<li class="text-center" style="width:75px">
								<select name="state[]">
									<option value="on" <?php if ($state === 'on') echo 'selected'; ?>>판매</option>
									<option value="out" <?php if ($state === 'out') echo 'selected'; ?>>단종</option>
									<option value="off" <?php if ($state === 'off') echo 'selected'; ?>>감춤</option>
								</select>
							</li>
							<li class="text-center" style="width:90px">
								<div class="m-b-3">
									<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveTop(this)"><i class="fas fa-angle-double-up"></i></button>
									<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveUp(this)"><i class="fas fa-angle-up"></i></button>
								</div>
								<div>
									<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveDown(this)"><i class="fas fa-angle-down"></i></button>
									<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveBottom(this)"><i class="fas fa-angle-double-down"></i></button>
								</div>
							</li>
							<li class="" style="width:50px"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="osFormGroupReg.prdListDel(this)"><i class="fas fa-trash-alt"></i></button></li>
						</ul>
					<?php } ?>
				</div>
			</form>
		</div>

		<div class="m-t-5 text-center">
			선택
			<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveTopSelected()"><i class="fas fa-angle-double-up"></i></button>
			<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveUpSelected()"><i class="fas fa-angle-up"></i></button>
			<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveDownSelected()"><i class="fas fa-angle-down"></i></button>
			<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveBottomSelected()"><i class="fas fa-angle-double-down"></i></button>
			<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-lg m-l-30" onclick="osFormGroupReg.prdSave(this, '<?= $oopIdx ?>');">그룹 상품 노출순서 저장</button>
		</div>
	</ul>
</div>

<style>
	.add-prd.selected {
		background: #fff3cd !important;
	}
</style>

<script>
	var osFormGroupReg = function() {
		function escapeHtml(value) {
			return String(value == null ? '' : value)
				.replace(/&/g, '&amp;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#39;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;');
		}

		return {
			prdSave: function(obj, oop_idx) {
				var formData = $("#formGroupPrd").serializeArray();

				$.ajax({
					url: "/admin/order/group/form_group_product/save",
					data: formData,
					type: "POST",
					dataType: "json",
					success: function(res) {
						if (res.success == true) {
							toast2("success", "그룹 상품리스트", "설정이 저장되었습니다.");
							if (window.orderSheetForm && typeof orderSheetForm.groupViewReset === 'function') {
								orderSheetForm.groupViewReset(oop_idx);
							}
							if (window.orderSheetDetail && typeof orderSheetDetail.PrdListReload === 'function') {
								orderSheetDetail.PrdListReload();
							}
						} else {
							showAlert("Error", res.msg || res.message || "저장에 실패했습니다.", "alert2");
							return false;
						}
					},
					error: function(request, status, error) {
						console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
						var responseMessage = request && request.responseJSON ? (request.responseJSON.msg || request.responseJSON.message) : "";
						showAlert("Error", responseMessage || "에러", "alert2");
						return false;
					}
				});
			},

			prdSearch: function() {
				var keyword = $("#formGroupPrdSearch").val();
				var s_brand = $("#formGroupPrdBrand").val();

				$.ajax({
					url: "/admin/order/group/form_group_product/search",
					data: {
						"keyword": keyword,
						"s_brand": s_brand
					},
					type: "POST",
					dataType: "json",
					success: function(res) {
						if (res.success != true) {
							showAlert("Error", res.msg || res.message || "검색에 실패했습니다.", "alert2");
							return false;
						}

						if (!res.count) {
							showAlert("Error", "검색 결과가 없습니다.", "alert2");
							return false;
						}

						var shtml = '<div class="m-t-10"><b>' + escapeHtml(keyword) + '</b> 검색결과 : (<b>' + res.count + '</b>)건</div>' +
							'<div class="m-t-5" id="formGroupPrdSearchResultList">' +
							'<table class="table-style border01 width-full">';

						for (var i = 0; i < res.prd_data.length; i++) {
							var row = res.prd_data[i];
							shtml += '<tr>' +
								'<td class="text-center" style="width:30px"><input type="checkbox" class="prd-search-result-checkbox" value="' + escapeHtml(row.idx) + '" ' +
								' data-psidx="' + escapeHtml(row.ps_idx) + '" ' +
								' data-img="' + escapeHtml(row.img) + '" ' +
								' data-prdname="' + escapeHtml(row.name) + '"></td>' +
								'<td class="text-center" style="width:50px"><img src="/data/comparion/' + escapeHtml(row.img) + '" style="width:40px; "></td>' +
								'<td>' +
								'<span class="prd-code">' + escapeHtml(row.idx) + ' | ' + escapeHtml(row.jancode) + '</span><br>' +
								'<span class="prd-name">' + escapeHtml(row.name) + '</span>' +
								' <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView(\'' + escapeHtml(row.idx) + '\',\'info\');">보기</button>' +
								'</td>' +
								'</tr>';
						}

						shtml += '</table></div>' +
							'<div class="m-t-5 m-b-10 text-center">' +
							'<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-sm prd-search-add-btn" onclick="osFormGroupReg.prdSearchAdd(this);">선택상품 추가</button>' +
							'</div>';

						$("#formGroupPrdSearchResult").html(shtml);
						$("#formGroupPrdSearch").val("");
					},
					error: function(request, status, error) {
						console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
						var responseMessage = request && request.responseJSON ? (request.responseJSON.msg || request.responseJSON.message) : "";
						showAlert("Error", responseMessage || "에러", "alert2");
						return false;
					}
				});
			},

			prdSearchAdd: function() {
				if ($(".prd-search-result-checkbox:checked").length == 0) {
					showAlert("Error", "선택된 상품이 없습니다.", "alert2");
					return false;
				}

				var shtml = '';
				var checkedCount = 0;
				var overlappingCkCount = 0;

				$(".prd-search-result-checkbox:checked").each(function() {
					var _prd_idx = String($(this).val() || '');
					var _prd_ps_idx = String($(this).data("psidx") || '');
					var _prd_img = String($(this).data("img") || '');
					var _prd_name = String($(this).data("prdname") || '');
					var overlappingCk = "on";
					checkedCount++;

					$("#formGroupPrdList ul").each(function() {
						if (_prd_idx == $(this).data("prdidx")) {
							overlappingCk = "off";
							overlappingCkCount++;
						}
					});

					if (overlappingCk == "on") {
						shtml += '<ul class="add-prd" data-prdidx="' + escapeHtml(_prd_idx) + '">' +
							'<input type="hidden" name="prd_idx[]" value="' + escapeHtml(_prd_idx) + '">' +
							'<input type="hidden" name="ps_idx[]" value="' + escapeHtml(_prd_ps_idx) + '">' +
							'<li class="text-center" style="width:30px"><input type="checkbox" class="prd-select-chk"></li>' +
							'<li class="text-center" style="width:40px"><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></li>' +
							'<li class="text-center" style="width:45px">' + escapeHtml(_prd_idx) + '</li>' +
							'<li class="text-center" style="width:55px"><img src="/data/comparion/' + escapeHtml(_prd_img) + '" style="height:45px; border:1px solid #ddd;"></li>' +
							'<li class="text-center" style="width:80px"></li>' +
							'<li>' +
							'<div>' +
							escapeHtml(_prd_name) +
							' <button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-xs" onclick="onlyAD.prdView(\'' + escapeHtml(_prd_idx) + '\',\'info\');">보기</button>' +
							'</div>' +
							'<div>' +
							'<input type="text" name="ordermemo[]" value="" class="m-t-2" placeholder="주문 메모" style="font-size:12px;">' +
							'</div>' +
							'</li>' +
							'<li class="text-center" style="width:75px">' +
							'<select name="state[]">' +
							'<option value="on">판매</option>' +
							'<option value="out">단종</option>' +
							'<option value="off">감춤</option>' +
							'</select>' +
							'</li>' +
							'<li class="text-center" style="width:90px">' +
							'<div class="m-b-3">' +
							'<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveTop(this)"><i class="fas fa-angle-double-up"></i></button>' +
							'<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveUp(this)"><i class="fas fa-angle-up"></i></button>' +
							'</div>' +
							'<div>' +
							'<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveDown(this)"><i class="fas fa-angle-down"></i></button>' +
							'<button type="button" class="btnstyle1 btnstyle1-xs" onclick="osFormGroupReg.moveBottom(this)"><i class="fas fa-angle-double-down"></i></button>' +
							'</div>' +
							'</li>' +
							'<li class="" style="width:50px"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="osFormGroupReg.prdListDel(this)"><i class="fas fa-trash-alt"></i></button></li>' +
							'</ul>';
					}
				});

				$("#formGroupPrdList").prepend(shtml);

				if (overlappingCkCount > 0) {
					showAlert("Good", "총 선택된 상품 (" + checkedCount + ")개중<br>중복(" + overlappingCkCount + ")을 제외한 (" + (checkedCount - overlappingCkCount) + ")상품이 추가되었습니다.<br>추가된 상품은 저장을 눌러야 최종 적용됩니다.", "alert2", "good");
					return false;
				}
			},

			prdListDel: function(obj) {
				$(obj).closest('ul').remove();
			},

			moveUp: function(btn) {
				var $item = $(btn).closest('ul');
				var $prev = $item.prev('ul');
				if ($prev.length) {
					$item.insertBefore($prev);
				}
			},

			moveDown: function(btn) {
				var $item = $(btn).closest('ul');
				var $next = $item.next('ul');
				if ($next.length) {
					$item.insertAfter($next);
				}
			},

			moveTop: function(btn) {
				var $item = $(btn).closest('ul');
				$item.parent().prepend($item);
			},

			moveBottom: function(btn) {
				var $item = $(btn).closest('ul');
				$item.parent().append($item);
			},

			moveUpSelected: function() {
				$("#formGroupPrdList ul.add-prd.selected").each(function() {
					var $item = $(this);
					var $prev = $item.prev('ul');
					if ($prev.length) {
						$item.insertBefore($prev);
					}
				});
			},

			moveDownSelected: function() {
				$($("#formGroupPrdList ul.add-prd.selected").get().reverse()).each(function() {
					var $item = $(this);
					var $next = $item.next('ul');
					if ($next.length) {
						$item.insertAfter($next);
					}
				});
			},

			moveTopSelected: function() {
				var $selected = $("#formGroupPrdList ul.add-prd.selected");
				if ($selected.length) {
					$selected.first().parent().prepend($selected);
				}
			},

			moveBottomSelected: function() {
				var $selected = $("#formGroupPrdList ul.add-prd.selected");
				if ($selected.length) {
					$selected.first().parent().append($selected);
				}
			}
		};
	}();

	$(function() {
		$("#formGroupPrdList").sortable({
			axis: "y",
			cursor: "move"
		});

		$(document).off('change.osFormGroupPrd', '#formGroupPrdList .prd-select-chk')
			.on('change.osFormGroupPrd', '#formGroupPrdList .prd-select-chk', function() {
				$(this).closest('ul.add-prd').toggleClass('selected', this.checked);
			});

		$(document).off('click.osFormGroupPrd', '#formGroupPrdList ul.add-prd')
			.on('click.osFormGroupPrd', '#formGroupPrdList ul.add-prd', function(e) {
				var tag = e.target.tagName.toLowerCase();
				if (['input', 'textarea', 'select', 'button', 'i', 'option'].indexOf(tag) !== -1) {
					return;
				}
				var $chk = $(this).find('.prd-select-chk');
				$chk.prop('checked', !$chk.prop('checked')).trigger('change');
			});

		$("#formGroupPrdSearch").off('keydown.osFormGroupPrd').on('keydown.osFormGroupPrd', function(e) {
			if (e.which == 13) {
				e.preventDefault();
				osFormGroupReg.prdSearch();
			}
		});
	});
</script>
