// ---- v2 버전  --- 

	/**
	 * 공통 Ajax 요청 함수
	 * @param {string} url - 요청 URL
	 * @param {object} data - 전송할 데이터
	 * @param {object} options - 추가 옵션 (method, dataType 등)
	 * @returns {Promise} - Ajax 요청 결과 Promise
	 */
	const ajaxRequest = (url, data = {}, options = {}) => {
		// 기본 옵션 설정
        const method = (options.method || 'POST').toUpperCase();
		const defaultOptions = {
			url,
			data,
			method,
			dataType: 'json', // 기본 응답 형식
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



	// 공통 Alert 생성 함수
	const dnAlert = (title, content, type = 'blue') => {
		$.alert({
			title: title,  // 명시적으로 key-value 쌍 작성
			content: content,
			type: type,
			useBootstrap: false,
			boxWidth: '400px',
			backgroundDismiss: true
		});
	};

	// 공통 Confirm 생성 함수
	const dnConfirm = (title, content, onConfirm, icon = null, type = "default", btnText = '확인', btnClass = 'btn-red', cancelText = '취소') => {
		$.confirm({
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
	 */
	const openDialog = (url, data = {}, title = "Dialog", width = "800px") => {
		$.alert({
			boxWidth: width,
			useBootstrap: false,
			title,
			backgroundDismiss: false,
			closeIcon: true,
			closeIconClass: 'fas fa-times',
			content: function () {
				const self = this;
				return ajaxRequest(url, data, { dataType: 'html' }) // dataType은 html로 명시
					.then((response) => {
						self.setContent(response); // 성공 시 팝업 내용 설정
					})
					.catch((error) => {
						self.setContent(`<p>에러가 발생했습니다. 다시 시도해주세요.</p>`); // 에러 메시지
						console.error(error);
					});
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
	};
