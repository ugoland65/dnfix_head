//가격비교 퀵 창
function comparisonQuick(idx, vmode = "comparison") {
	window.open("/admin2/comparison/popup.comparison_modify.php?idx=" + idx + "&vmode=" + vmode, "comparison_quick_" + idx, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

//회원정보
function userModify(id, mode) {
	window.open("<?=_A_PATH_MEMBER_INFO_POPUP?>?id=" + id + "&mode=" + mode, "member_info_" + id, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

//상품 창 prd_provider_info
function prdProviderQuick(idx, vmode = "info") {
	window.open("/ad/ajax/prd_provider_info?prd_idx=" + idx + "&vmode=" + vmode, "prdProviderQuick_" + idx, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

//브랜드 수정
function brandModify(idx) {
	window.open("/admin/brand/detail/" + idx, "brand_view_" + idx, "width=1000,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

// 공급사 상품 매칭제외
function prdProviderMatchExcluded(db1_idx = null, db2_idx = null) {

	$.confirm({
		title: '매칭제외 처리',
		boxWidth: '420px',
		useBootstrap: false,
		content: ''
			+ '<div style="text-align:left;">'
			+ '  <div style="margin-bottom:8px; color:#666;">매칭제외 사유를 입력해주세요.</div>'
			+ '  <input type="text" id="matchExcludedReasonInput" class="form-control" placeholder="처리사유 입력" />'
			+ '  <div style="margin-top:10px; margin-bottom:5px; color:#666;">아래 버튼을 누르면 해당 사유로 바로 처리됩니다.</div>'
			+ '  <div style="margin-top:10px;">'
			+ '      <button type="button" id="quickReasonSupplierStop" class="btnstyle1 btnstyle1-gary btnstyle1-xs">공급사 판매중단</button>'
			+ '      <button type="button" id="quickReasonSupplierMatched" class="btnstyle1 btnstyle1-gary btnstyle1-xs">이미 타사 상품과 매칭하였음</button>'
			+ '  </div>'
			+ '</div>',
		onContentReady: function () {
			const self = this;
			this.$content.find('#quickReasonSupplierStop').on('click', function () {
				self.$content.find('#matchExcludedReasonInput').val('공급사 판매중단');
				self.$$submit.trigger('click');
			});
			this.$content.find('#quickReasonSupplierMatched').on('click', function () {
				self.$content.find('#matchExcludedReasonInput').val('이미 타사 상품과 매칭하였음');
				self.$$submit.trigger('click');
			});
		},
		buttons: {
			cancel: {
				text: '취소'
			},
			submit: {
				text: '처리',
				btnClass: 'btn-blue',
				action: function () {
					const reason = (this.$content.find('#matchExcludedReasonInput').val() || '').trim();
					if (!reason) {
						alert('처리사유를 입력해주세요.');
						return false;
					}

					$.ajax({
						url: '/admin/provider_product/action',
						type: 'POST',
						dataType: 'json',
						data: {
							action_mode: 'product_match_excluded',
							db1_idx: db1_idx,
							db2_idx: db2_idx,
							process_reason: reason
						}
					}).done(function (res) {
						if (res && res.status === 'success') {
							const hasDb1 = db1_idx !== null && db1_idx !== undefined && String(db1_idx).trim() !== '';
							const targetSelector = hasDb1
								? `#match_id_${db1_idx}`
								: `#trid_${db2_idx}`;
							const $targetRow = $(targetSelector);
							$targetRow.fadeOut(150, function () {
								$(this).remove();
							});
							showToast("매칭제외 처리 완료", new Date().toLocaleTimeString());
						} else {
							alert((res && res.message) ? res.message : '매칭제외 처리에 실패했습니다.');
						}
					}).fail(function () {
						alert('서버 통신에 실패했습니다.');
					});
				}
			}
		}
	});

}

// 상품 그룹핑 상품 순서 변경
function productGroupingProductOrderChange(idx) {

	var width = "1400px";

	$.alert({
		boxWidth: width,
		useBootstrap: false,
		title: "상품 추가/순서변경",
		backgroundDismiss: false,
		closeIcon: true,
		closeIconClass: 'fas fa-times',
		content: function () {
			var self = this;
			self._xhr = $.ajax({
				url: '/admin/product/grouping_product_order_change',
				data: { idx: idx },
				dataType: 'html',
				method: 'POST'
			}).done(function (response) {
				self.setContent(response);
			}).fail(function () {
				self.setContent('에러');
			});

			return self._xhr;
		},
		onClose: function () {
			if (this._xhr && this._xhr.readyState !== 4) this._xhr.abort();
		},
		buttons: {
			cancel: { text: '닫기' } // action 제거(추천)
		}
	});

}