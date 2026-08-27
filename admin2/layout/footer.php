<?php
$footerFirebaseConfigPath = dirname(__DIR__, 2) . '/private/firebase-web-config.json';
$footerFirebaseConfigJson = is_readable($footerFirebaseConfigPath)
	? file_get_contents($footerFirebaseConfigPath)
	: false;
$footerFirebaseConfigData = $footerFirebaseConfigJson !== false
	? json_decode($footerFirebaseConfigJson, true)
	: null;
$footerFirebaseConfigKeys = [
	'apiKey',
	'authDomain',
	'databaseURL',
	'projectId',
	'storageBucket',
	'messagingSenderId',
	'appId',
];
$footerFirebaseConfig = is_array($footerFirebaseConfigData)
	? array_intersect_key(
		$footerFirebaseConfigData,
		array_fill_keys($footerFirebaseConfigKeys, true)
	)
	: null;

if (
	!is_array($footerFirebaseConfig)
	|| count($footerFirebaseConfig) !== count($footerFirebaseConfigKeys)
) {
	$footerFirebaseConfig = null;
}
?>
		</div>
	</div><!-- #wrap_table -->
</div><!-- #wrap -->

<div id="footer">
	Copyright ⓒ <b style="color:#247eff;"><?=defined('_A_GLOB_SITENAME') ? _A_GLOB_SITENAME : ''?></b> Corp. All Rights Reserved. <?=defined('_A_GLOB_COPYRIGHT') ? _A_GLOB_COPYRIGHT : ''?> (<?=defined('WS_VERSION_NUM') ? WS_VERSION_NUM : ''?>)
</div><!-- #footer  -->

<script src="/admin2/js/admin_footer.js?ver=<?=time()?>"></script>
<script src="/assets/js/common.js?ver=<?=time()?>"></script>

<script type="module">
		import {
			initializeApp
		} from 'https://www.gstatic.com/firebasejs/12.18.0/firebase-app.js';

		import {
			getAuth,
			signInWithCustomToken
		} from 'https://www.gstatic.com/firebasejs/12.18.0/firebase-auth.js';

		import {
			getDatabase,
			ref,
			push,
			set,
			update,
			remove,
			onValue,
			onDisconnect,
			onChildAdded,
			query,
			limitToLast,
			serverTimestamp
		} from 'https://www.gstatic.com/firebasejs/12.18.0/firebase-database.js';

		const firebaseConfig = <?=json_encode(
			$footerFirebaseConfig,
			JSON_UNESCAPED_SLASHES
			| JSON_HEX_TAG
			| JSON_HEX_AMP
			| JSON_HEX_APOS
			| JSON_HEX_QUOT
		)?>;

		const pageStartedAt = Date.now();
		const performanceStartedAt = performance.now();
		const clientId = crypto.randomUUID();
		const orderRealtimeState = {
			container: null,
			orderId: '',
			currentUid: '',
			myConnectionRef: null,
			myConnectionId: '',
			activeOopIdx: '',
			editingOopIdx: '',
			latestPresenceData: {},
			lastEditingWriteAt: 0,
			editingClearTimer: null,
			handledSaveEventIds: new Set()
		};

		function normalizePositiveId(value) {
			const normalized = String(value || '').trim();
			return /^[1-9]\d*$/.test(normalized) ? normalized : '';
		}

		function clearEditingTimer() {
			if (orderRealtimeState.editingClearTimer) {
				clearTimeout(orderRealtimeState.editingClearTimer);
				orderRealtimeState.editingClearTimer = null;
			}
		}

		function clearOrderQuantityEditing() {
			clearEditingTimer();
			orderRealtimeState.editingOopIdx = '';

			if (!orderRealtimeState.myConnectionRef) {
				return Promise.resolve();
			}

			return update(orderRealtimeState.myConnectionRef, {
				editing: false,
				editing_oop_idx: '',
				editing_at: serverTimestamp()
			}).catch(error => {
				console.error('Firebase editing state clear failed.', error);
			});
		}

		function markOrderQuantityEditing(orderId, oopIdx) {
			const normalizedOrderId = normalizePositiveId(orderId);
			const normalizedOopIdx = normalizePositiveId(oopIdx);
			if (
				normalizedOrderId === ''
				|| normalizedOopIdx === ''
				|| normalizedOrderId !== orderRealtimeState.orderId
			) {
				return;
			}

			orderRealtimeState.activeOopIdx = normalizedOopIdx;
			orderRealtimeState.editingOopIdx = normalizedOopIdx;
			clearEditingTimer();
			orderRealtimeState.editingClearTimer = setTimeout(
				clearOrderQuantityEditing,
				12000
			);

			if (!orderRealtimeState.myConnectionRef) {
				return;
			}

			const now = Date.now();
			if (now - orderRealtimeState.lastEditingWriteAt < 2000) {
				return;
			}
			orderRealtimeState.lastEditingWriteAt = now;

			update(orderRealtimeState.myConnectionRef, {
				editing: true,
				editing_oop_idx: normalizedOopIdx,
				editing_at: serverTimestamp()
			}).catch(error => {
				console.error('Firebase editing state update failed.', error);
			});
		}

		function setActiveOrderFormGroup(orderId, oopIdx) {
			const normalizedOrderId = normalizePositiveId(orderId);
			const normalizedOopIdx = normalizePositiveId(oopIdx);
			if (
				normalizedOrderId === ''
				|| normalizedOrderId !== orderRealtimeState.orderId
			) {
				return;
			}

			const previousOopIdx = orderRealtimeState.activeOopIdx;
			orderRealtimeState.activeOopIdx = normalizedOopIdx;
			if (
				orderRealtimeState.editingOopIdx !== ''
				&& previousOopIdx !== normalizedOopIdx
			) {
				clearOrderQuantityEditing();
			}
			refreshOrderRealtimeUi();
		}

		function publishOrderFormGroupSaved(orderId, oopIdx) {
			const normalizedOrderId = normalizePositiveId(orderId);
			const normalizedOopIdx = normalizePositiveId(oopIdx);
			if (
				normalizedOrderId === ''
				|| normalizedOopIdx === ''
				|| normalizedOrderId !== orderRealtimeState.orderId
				|| !orderRealtimeState.myConnectionRef
			) {
				return Promise.resolve(false);
			}

			clearEditingTimer();
			orderRealtimeState.editingOopIdx = '';
			const eventId = `${clientId}-${Date.now()}`;

			return update(orderRealtimeState.myConnectionRef, {
				editing: false,
				editing_oop_idx: '',
				editing_at: serverTimestamp(),
				saved_oop_idx: normalizedOopIdx,
				saved_event_id: eventId,
				saved_at: serverTimestamp(),
				saved_at_client: Date.now()
			}).then(() => true);
		}

		window.orderSheetRealtime = {
			setActiveFormGroup: setActiveOrderFormGroup,
			markQuantityEditing: markOrderQuantityEditing,
			clearQuantityEditing: clearOrderQuantityEditing,
			publishFormGroupSaved: publishOrderFormGroupSaved
		};

		const connectionStatus =
			document.getElementById('connectionStatus');

		const userUid =
			document.getElementById('userUid');

		const logElement =
			document.getElementById('log');

		const sendButton =
			document.getElementById('sendButton');

		const clearButton =
			document.getElementById('clearButton');

		const messageInput =
			document.getElementById('message');

		function writeLog(message) {
			if (!logElement) {
				console.info(`[Firebase] ${message}`);
				return;
			}

			const time = new Date().toLocaleTimeString();

			logElement.textContent =
				`[${time}] ${message}\n` + logElement.textContent;
		}

		function renderOrderPresence(
			container,
			presenceData,
			currentUid,
			currentConnectionId,
			activeOopIdx
		) {
			const statusElement =
				container.querySelector('[data-presence-status]');
			const usersElement =
				container.querySelector('[data-presence-users]');
			const noticeElement =
				container.querySelector('[data-presence-notice]');

			if (!statusElement || !usersElement) {
				return;
			}

			const activeUsers = Object.entries(presenceData || {})
				.map(([uid, userData]) => {
					const connections = Object.values(
						userData?.connections || {}
					).filter(connection => connection && typeof connection === 'object');

					if (connections.length === 0) {
						return null;
					}

					const namedConnection = connections.find(
						connection => connection.admin_name
					);

					return {
						uid,
						name: namedConnection?.admin_name || uid,
						tabCount: connections.length
					};
				})
				.filter(Boolean)
				.sort((left, right) => {
					if (left.uid === currentUid) {
						return -1;
					}

					if (right.uid === currentUid) {
						return 1;
					}

					return left.name.localeCompare(right.name, 'ko');
				});

			usersElement.replaceChildren();
			statusElement.textContent = activeUsers.length > 0
				? `${activeUsers.length}명`
				: '접속자 없음';

			activeUsers.forEach(user => {
				const userElement = document.createElement('span');
				userElement.className = 'order-sheet-presence__user';

				if (user.uid === currentUid) {
					userElement.classList.add(
						'order-sheet-presence__user--self'
					);
				}

				userElement.append(
					document.createTextNode(
						user.name + (user.uid === currentUid ? ' (나)' : '')
					)
				);

				if (user.tabCount > 1) {
					const tabsElement = document.createElement('span');
					tabsElement.className = 'order-sheet-presence__tabs';
					tabsElement.textContent = `${user.tabCount}탭`;
					userElement.append(tabsElement);
				}

				usersElement.append(userElement);
			});

			if (noticeElement) {
				const hasOtherEditor = activeOopIdx !== ''
					&& Object.entries(presenceData || {}).some(([uid, userData]) => {
						return Object.entries(userData?.connections || {}).some(
							([connectionId, connection]) => {
								if (
									uid === currentUid
									&& connectionId === currentConnectionId
								) {
									return false;
								}

								return connection?.editing === true
									&& String(connection.editing_oop_idx || '') === activeOopIdx;
							}
						);
					});

				noticeElement.textContent = hasOtherEditor
					? '현재 다른사람이 이폼의 수량을 변경중입니다.'
					: '';
				noticeElement.hidden = !hasOtherEditor;
			}
		}

		function getActiveOrderFormGroup() {
			if (orderRealtimeState.activeOopIdx !== '') {
				return orderRealtimeState.activeOopIdx;
			}

			const activeGroup = document.querySelector(
				'.order_sheet_detail .ost-big.active[id^="group_side_"]'
			);
			if (!activeGroup) {
				return '';
			}

			return normalizePositiveId(
				String(activeGroup.id || '').replace(/^group_side_/, '')
			);
		}

		function handleOrderFormGroupSaveEvents(presenceData) {
			Object.entries(presenceData || {}).forEach(([uid, userData]) => {
				Object.entries(userData?.connections || {}).forEach(
					([connectionId, connection]) => {
						const eventId = String(connection?.saved_event_id || '');
						if (
							eventId === ''
							|| orderRealtimeState.handledSaveEventIds.has(eventId)
						) {
							return;
						}
						orderRealtimeState.handledSaveEventIds.add(eventId);

						const savedAt = Number(
							connection?.saved_at_client
							|| connection?.saved_at
							|| 0
						);
						if (
							savedAt < pageStartedAt
							|| (
								uid === orderRealtimeState.currentUid
								&& connectionId === orderRealtimeState.myConnectionId
							)
						) {
							return;
						}

						const savedOopIdx = normalizePositiveId(
							connection?.saved_oop_idx
						);
						if (savedOopIdx === '') {
							return;
						}

						if (typeof window.showToast === 'function') {
							window.showToast(
								'다른 사용자가 폼그룹 상품을 저장했습니다.',
								new Date().toLocaleTimeString()
							);
						}

						if (
							savedOopIdx === getActiveOrderFormGroup()
							&& window.orderSheetDetail
							&& typeof window.orderSheetDetail.prdListShow === 'function'
						) {
							clearOrderQuantityEditing();
							window.orderSheetDetail.prdListShow(
								orderRealtimeState.orderId,
								savedOopIdx
							);
						}
					}
				);
			});
		}

		function refreshOrderRealtimeUi() {
			if (!orderRealtimeState.container) {
				return;
			}

			const activeOopIdx = getActiveOrderFormGroup();
			renderOrderPresence(
				orderRealtimeState.container,
				orderRealtimeState.latestPresenceData,
				orderRealtimeState.currentUid,
				orderRealtimeState.myConnectionId,
				activeOopIdx
			);
		}

		async function startOrderPresence(database, credential) {
			const container =
				document.getElementById('firebaseOrderPresence');

			if (!container) {
				return;
			}

			const statusElement =
				container.querySelector('[data-presence-status]');
			const usersElement =
				container.querySelector('[data-presence-users]');
			const orderId = String(container.dataset.orderId || '');

			if (!/^[1-9]\d*$/.test(orderId)) {
				if (statusElement) {
					statusElement.textContent = '주문서 번호 없음';
				}
				return;
			}

			try {
				const idTokenResult =
					await credential.user.getIdTokenResult();
				const adminName =
					String(
						idTokenResult.claims.admin_name
						|| credential.user.uid
					);
				const orderPresenceRef = ref(
					database,
					`presence/orders/${orderId}`
				);
				const myConnectionsRef = ref(
					database,
					`presence/orders/${orderId}/${credential.user.uid}/connections`
				);
				const myConnectionRef = push(myConnectionsRef);

				orderRealtimeState.container = container;
				orderRealtimeState.orderId = orderId;
				orderRealtimeState.currentUid = credential.user.uid;
				orderRealtimeState.myConnectionRef = myConnectionRef;
				orderRealtimeState.myConnectionId = String(myConnectionRef.key || '');
				orderRealtimeState.activeOopIdx = getActiveOrderFormGroup();

				await onDisconnect(myConnectionRef).remove();
				await set(myConnectionRef, {
					admin_name: adminName,
					editing: false,
					editing_oop_idx: '',
					editing_at: serverTimestamp(),
					saved_oop_idx: '',
					saved_event_id: '',
					saved_at: 0,
					saved_at_client: 0,
					connected_at: serverTimestamp()
				});

				onValue(
					orderPresenceRef,
					snapshot => {
						orderRealtimeState.latestPresenceData =
							snapshot.val() || {};
						refreshOrderRealtimeUi();
						handleOrderFormGroupSaveEvents(
							orderRealtimeState.latestPresenceData
						);
					},
					error => {
						if (statusElement) {
							statusElement.textContent = '접속자 확인 실패';
						}
						if (usersElement) {
							usersElement.textContent =
								'Firebase 권한을 확인해 주세요.';
						}
						console.error(
							'Firebase presence subscription failed.',
							error
						);
					}
				);
			} catch (error) {
				if (statusElement) {
					statusElement.textContent = '접속 등록 실패';
				}
				if (usersElement) {
					usersElement.textContent =
						'Firebase 권한을 확인해 주세요.';
				}
				console.error(
					'Firebase presence registration failed.',
					error
				);
			}
		}

		async function startFirebase() {
			if (!firebaseConfig) {
				throw new Error('Firebase Web 설정 파일을 읽을 수 없습니다.');
			}

			const app = initializeApp(firebaseConfig);
			const auth = getAuth(app);
			const database = getDatabase(app);

			const tokenResponse = await fetch('/api/firebase/token', {
				method: 'GET',
				credentials: 'same-origin',
				cache: 'no-store',
				headers: {
					'Accept': 'application/json'
				}
			});
			const tokenResult = await tokenResponse.json();

			if (!tokenResponse.ok ||
				!tokenResult.success ||
				!tokenResult.token) {
				throw new Error(
					tokenResult.message || 'Firebase 인증 토큰 발급에 실패했습니다.'
				);
			}

			const credential = await signInWithCustomToken(
				auth,
				tokenResult.token
			);

			if (credential.user.uid !== tokenResult.uid) {
				throw new Error('Firebase 관리자 UID가 일치하지 않습니다.');
			}

			if (userUid) {
				userUid.textContent = credential.user.uid;
			}

			writeLog(`관리자 인증 완료: ${credential.user.uid}`);

			await startOrderPresence(database, credential);

			const isSpeedTestPage = Boolean(
				connectionStatus &&
				userUid &&
				logElement &&
				sendButton &&
				clearButton &&
				messageInput
			);

			if (!isSpeedTestPage) {
				return;
			}

			const channelName = 'office-test-1';
			const eventsRef = ref(
				database,
				`speed_test/${channelName}/events`
			);
			const connectedRef = ref(database, '.info/connected');

			onValue(connectedRef, snapshot => {
				if (snapshot.val() === true) {
					const connectedTime =
						Math.round(performance.now() - performanceStartedAt);

					connectionStatus.textContent =
						`Firebase 연결됨 · 최초 연결 ${connectedTime}ms`;
					connectionStatus.className = 'status connected';
					writeLog(
						`Firebase 서버 연결 완료: ${connectedTime}ms`
					);
				} else {
					connectionStatus.textContent = 'Firebase 연결 끊김';
					connectionStatus.className = 'status disconnected';
				}
			});

			const recentEventsQuery = query(
				eventsRef,
				limitToLast(30)
			);

			onChildAdded(recentEventsQuery, snapshot => {
				const event = snapshot.val();

				if (!event.sent_at_client ||
					event.sent_at_client < pageStartedAt) {
					return;
				}

				if (event.client_id === clientId) {
					return;
				}

				const receivedDelay =
					Date.now() - event.sent_at_client;

				writeLog(
					`상대 이벤트 수신: ${receivedDelay}ms · ${event.message}`
				);
			});

			sendButton.addEventListener('click', async () => {
				const startedAt = performance.now();
				const eventRef = push(eventsRef);

				await set(eventRef, {
					client_id: clientId,
					sender_uid: auth.currentUser.uid,
					message: messageInput.value,
					sent_at_client: Date.now(),
					sent_at_server: serverTimestamp()
				});

				const writeRoundTrip =
					Math.round(performance.now() - startedAt);

				writeLog(
					`Firebase 저장 응답: ${writeRoundTrip}ms`
				);
			});

			clearButton.addEventListener('click', async () => {
				await remove(eventsRef);
				logElement.textContent = '';
				writeLog('테스트 기록을 초기화했습니다.');
			});
		}

		startFirebase().catch(error => {
			if (connectionStatus) {
				connectionStatus.textContent = 'Firebase 인증 실패';
				connectionStatus.className = 'status disconnected';
			}

			writeLog(error.message || 'Firebase 초기화에 실패했습니다.');
			console.error('Firebase initialization failed.', error);
		});
	</script>

</body>
</html>