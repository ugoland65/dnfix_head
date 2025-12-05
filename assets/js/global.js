// ---- v2 버전  --- 

    // 숫자 포맷 헬퍼
    window.number_format = (num) => Number(num || 0).toLocaleString();

	/**
	 * 공통 Ajax 요청 함수
	 * 
	 * @param {string} url - 요청 URL
	 * @param {object} data - 전송할 데이터
	 * @param {object} options - 추가 옵션 (method, dataType 등)
	 * @returns {Promise} - Ajax 요청 결과 Promise
	 */
	const ajaxRequest = (url, data = {}, options = {}) => {

		// 기본 옵션 설정
		const defaultOptions = {
			url,
			data,
			method: 'POST', // 기본 메서드
			dataType: 'json', // 기본 응답 형식
			cache: false, // jQuery 캐시 방지 플래그
		};

		// 기본 옵션과 사용자 옵션 병합
		const ajaxOptions = { ...defaultOptions, ...options };

		// Ajax 요청
		return $.ajax(ajaxOptions)
			.then((response) => {
				// 성공 처리
				//console.log('요청 성공:', response);
				return response; // 성공 결과 반환
			})
			.catch((jqXHR, textStatus, errorThrown) => {
				// 실패 처리
				console.error('Ajax 요청 실패:', textStatus, errorThrown);
				throw new Error(`Ajax 요청 실패: ${textStatus}`);
			});
	};


	/**
	 * 공통 Alert 생성 함수
	 * 
	 * @param {string|object} title - 알림 제목 또는 옵션 객체({title, content, type})
	 * @param {string} [content] - 알림 내용
	 * @param {string} [type=blue] - 알림 타입 (blue, red, green, yellow)
	 * @param {object} [extra=null] - 추가 옵션 객체
	 * @returns {JConfirm} - jQuery Confirm 인스턴스
	 */
	function dnAlert(title, content, type = 'blue', extra = null) {

		var DEFAULT_TITLE = 'Notice';
		var TYPES = { blue:1, red:1, green:1, yellow:1 };

		// ---- 확장 옵션/콜백 - 2025.10.20 Lion65 ----
		let onOk   = null; // 확인 클릭 시 실행
		let onClose= null; // 닫힘(OK/배경/닫기 아이콘 포함) 시 실행
		let redirect = null; // 확인/닫힘 후 이동할 URL
		let boxWidth = '400px';

		// extra 해석: 함수면 onOk, 객체면 옵션 병합
		if (typeof extra === 'function') {
			onOk = extra;
		} else if (extra && typeof extra === 'object') {
			onOk     = typeof extra.onOk   === 'function' ? extra.onOk   : null;
			onClose  = typeof extra.onClose=== 'function' ? extra.onClose: null;
			if (typeof extra.redirect === 'string' && extra.redirect.trim() !== '') redirect = extra.redirect;
			if (typeof extra.boxWidth === 'string' && extra.boxWidth.trim() !== '') boxWidth = extra.boxWidth;
		}


		// 1) dnAlert("메시지")
		if (arguments.length === 1 && typeof title !== 'object') {
			content = title;
			title = DEFAULT_TITLE;
			type = 'blue';
		}

		// 2) dnAlert("메시지", "red")  // 두 번째 인자를 type으로 인식
		else if (arguments.length === 2 && TYPES[String(content)]) {
			type = content;
			content = title;
			title = DEFAULT_TITLE;
		}

		else if (arguments.length >= 3 && (typeof arguments[3] === 'function' || typeof arguments[3] === 'object')) {
			// 위에서 extra 처리함
		}

		// 3) dnAlert({ title, content, type })  // 옵션 객체
		else if (typeof title === 'object' && title !== null) {
			var opt = title;
			title   = opt.title || DEFAULT_TITLE;
			content = opt.content || '';
			type = (opt.type && TYPES[opt.type]) ? opt.type : 'blue';

			// 객체 안에 확장 옵션이 들어온 경우 병합
			if (typeof opt.onOk === 'function')    onOk = opt.onOk;
			if (typeof opt.onClose === 'function') onClose = opt.onClose;
			if (typeof opt.redirect === 'string' && opt.redirect.trim() !== '') redirect = opt.redirect;
			if (typeof opt.boxWidth === 'string' && opt.boxWidth.trim() !== '') boxWidth = opt.boxWidth;
		
		} else {
			// 기본값 보정
			title = title || DEFAULT_TITLE;
			content = content || '';
			type = (type && TYPES[type]) ? type : 'blue';
		}
	
		const inst = $.alert({
			title,
			content,
			type,
			useBootstrap: false,
			boxWidth,
			backgroundDismiss: true,
			buttons: {
			  ok: {
				text: 'OK',
				btnClass: 'btn-blue',
				action: function () {
					try { if (onOk) onOk(); } catch (e) { console.error(e); }
					if (redirect) location.href = redirect;
				}
			  }
			},
			onClose: function () {
				try { if (onClose) onClose(); } catch (e) { console.error(e); }
				// onClose에서도 redirect할 경우
				// if (redirect) location.href = redirect;
			}
		});
	
		return inst;
	}
  
	/*
		1) 기본
		dnAlert('Saved', 'The item has been saved.', 'green');

		2) 확인 후 페이지 이동
		dnAlert('Saved', 'The item has been saved.', 'green', { redirect: '/admin/hotels' });

		3) 확인 후 콜백 실행 (그리고 이동)
		dnAlert('Saved', 'The item has been saved.', 'green', {
			onOk() { refreshGrid(); },
			redirect: '/admin/hotels'
		});

		4) 옵션 객체만으로
		dnAlert({
			title: 'Warning',
			content: 'Are you sure?',
			type: 'yellow',
			onOk() { submitDelete(); },
			onClose() { console.log('closed'); },
			boxWidth: '480px'
		});

		5) 간단 콜백(네 번째 인자에 함수)
		dnAlert('Completed', 'All done.', 'blue', () => refreshGrid());
	*/




	/**
	 * 공통 Confirm 생성 함수
	 * 
	 * @param {string} title - 팝업 제목
	 * @param {string} content - 팝업 내용
	 * @param {function} onConfirm - 확인 버튼 클릭 시 실행할 함수
	 * @param {string} icon - 팝업 아이콘
	 * @param {string} type - 팝업 타입
	 * @param {string} btnText - 확인 버튼 텍스트
	 * @param {string} btnClass - 확인 버튼 클래스
	 * @param {string} cancelText - 취소 버튼 텍스트
	 */
	const dnConfirm = (title, content, onConfirm, icon = null, type = "default", btnText = '확인', btnClass = 'btn-red', cancelText = '취소') => {
		$.confirm({
			boxWidth: '400px',
			useBootstrap: false,
			title: title,  // 명시적으로 key-value 쌍 작성
			content: content,
			type: type, // 기본 타입 추가
			backgroundDismiss: true,
			icon: icon, // 기본적으로 null, 필요 시 호출 시 전달
			closeAnimation: 'zoom', // 닫기 애니메이션 추가
			alignMiddle: true, // 팝업 중앙 정렬 추가
			buttons: {
				confirm: {
					text: btnText,
					btnClass: btnClass,
					action: onConfirm
				},
				cancel: {
					text: cancelText
				}
			}
		});
	};

	/**
	 * 공통 Dialog Popup 함수
	 * @param {string} url - 요청 URL
	 * @param {object} data - 전송할 데이터
	 * @param {string} title - 팝업 제목
	 * @param {string} width - 팝업 너비 (기본값: 800px)
	 * @returns {object} - $.alert의 반환값
	 */
	const openDialog = (url, data = {}, title = "Dialog", width = "800px", method = "GET", opts = {}) => {
		
		const onSuccess = typeof opts.onSuccess === 'function' ? opts.onSuccess : null;
  		const onClose   = typeof opts.onClose   === 'function' ? opts.onClose   : null;

		const inst =  $.alert({
			boxWidth: width,
			useBootstrap: false,
			title,
			backgroundDismiss: false,
			closeIcon: true,
			closeIconClass: 'fas fa-times',
			content: function () {
				const self = this;
				return ajaxRequest(url, data, { dataType: 'html', method: method }) // dataType은 html로 명시
					.then((response) => {
						self.setContent(response); // 성공 시 팝업 내용 설정
					})
					.catch((error) => {
						self.setContent(`<p>에러가 발생했습니다. 다시 시도해주세요.</p>`); // 에러 메시지
						console.error(error);
					});
			},
			onContentReady: function () {
				const self = this;
				const $c = self.$content;
		  
				/*
				$c.on('click', '.js-dialog-done', function () {
				  const payload = $(this).data() || {}; // ex) data-id="123"
				  if (onSuccess) onSuccess(payload);
				  self.close();
				});
				*/
		  
				// 커스텀 이벤트 패턴: 컨텐츠 스크립트에서 $root.trigger('dialog:done', payload)
				$c.on('dialog:done', function (e, payload) {
				  if (onSuccess) onSuccess(payload);
				  self.close();
				});

			},
			onClose: function () {
				// 이벤트 해제 (네임스페이스 기반이면 자동 정리되지만 한 번 더 안전장치)
				this.$content?.off();
				this.$content = undefined;
				console.log('onClose');
			},
			onDestroy: function () {
				// 노드 완전 제거로 DOM 잔재/데이터 캐시 제거
				this.$content?.remove();
				this.$content = undefined;
			},
			buttons: {
				cancel: {
					text: '닫기',
					action: function () {
						// 팝업 닫기 액션 (필요시 추가)
					}
				}
			}
		});

		return inst;
	};


	/**
	 * Toast 표시 함수
	 * @param {string} message - 토스트 메시지
	 * @param {string} time - 토스트 시간
	 * @returns {void}
	 */
    function showToast(message, title = 'Notification', time = 'just now') {
        // Toast 고유 ID 생성 (시간 기준으로 고유 ID를 보장)
        const toastId = `toast-${Date.now()}`;

        // Toast HTML 구조 동적으로 생성
        const toastHtml = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
                <div class="toast-header">
                    <strong class="me-auto">${title}</strong>
                    <small class="time">${time}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        // Toast를 컨테이너에 추가 (맨 위에 추가)
        const toastContainer = document.getElementById('toastContainer');
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        // Bootstrap Toast 객체 생성 및 표시
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);

        // Toast 표시
        toast.show();

        // 닫기 버튼 클릭 이벤트 핸들링
        const closeButton = toastElement.querySelector('.btn-close');
        closeButton.addEventListener('click', () => {
            console.log(`Toast ${toastId} closed manually.`);
        });

        // Toast가 사라진 후 DOM에서 제거
        toastElement.addEventListener('hidden.bs.toast', () => {
            //console.log(`Toast ${toastId} removed.`);
            toastElement.remove();
        });
    }