
    <style>

        .status {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            background: #f1f1f1;
        }

        .connected {
            color: #16833c;
        }

        .disconnected {
            color: #c62828;
        }

        button {
            padding: 10px 18px;
            cursor: pointer;
        }

        #log {
            margin-top: 20px;
            padding: 15px;
            min-height: 200px;
            background: #1e1f21;
            color: #eee;
            white-space: pre-wrap;
        }
    </style>


<h1>Firebase 반응속도 테스트</h1>

<div id="connectionStatus" class="status">
    Firebase 연결 중...
</div>

<div class="status">
    사용자 UID:
    <span id="userUid">인증 중...</span>
</div>

<div>
    <input
        type="text"
        id="message"
        value="테스트 알림"
        style="width:300px; padding:10px;"
    >

    <button type="button" id="sendButton">
        이벤트 전송
    </button>

    <button type="button" id="clearButton">
        기록 초기화
    </button>
</div>

<div id="log"></div>

<script type="module">
    import {
        initializeApp
    } from 'https://www.gstatic.com/firebasejs/12.18.0/firebase-app.js';

    import {
        getAuth,
        signInAnonymously
    } from 'https://www.gstatic.com/firebasejs/12.18.0/firebase-auth.js';

    import {
        getDatabase,
        ref,
        push,
        set,
        remove,
        onValue,
        onChildAdded,
        query,
        limitToLast,
        serverTimestamp
    } from 'https://www.gstatic.com/firebasejs/12.18.0/firebase-database.js';

    // Firebase 콘솔에서 복사한 설정값으로 교체
    const firebaseConfig = {
        apiKey: 'AIzaSyDJRx5gJK2iNrOZtA72t-oBAU_jxw19W70',
        authDomain: 'dnfix-intranet-realtime.firebaseapp.com',
        databaseURL: 'https://dnfix-intranet-realtime-default-rtdb.asia-southeast1.firebasedatabase.app',
        projectId: 'dnfix-intranet-realtime',
        storageBucket: 'dnfix-intranet-realtime.firebasestorage.app',
        messagingSenderId: '870830587010',
        appId: '1:870830587010:web:9086edf957e79ba11e889b'
    };

    const pageStartedAt = Date.now();
    const performanceStartedAt = performance.now();
    const clientId = crypto.randomUUID();

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
        const time = new Date().toLocaleTimeString();

        logElement.textContent =
            `[${time}] ${message}\n` + logElement.textContent;
    }

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const database = getDatabase(app);

    const channelName = 'office-test-1';

    const eventsRef = ref(
        database,
        `speed_test/${channelName}/events`
    );

    const connectedRef = ref(
        database,
        '.info/connected'
    );

    // Firebase 연결상태 확인
    onValue(connectedRef, snapshot => {
        if (snapshot.val() === true) {
            const connectedTime =
                Math.round(performance.now() - performanceStartedAt);

            connectionStatus.textContent =
                `Firebase 연결됨 · 최초 연결 ${connectedTime}ms`;

            connectionStatus.className =
                'status connected';

            writeLog(
                `Firebase 서버 연결 완료: ${connectedTime}ms`
            );
        } else {
            connectionStatus.textContent =
                'Firebase 연결 끊김';

            connectionStatus.className =
                'status disconnected';
        }
    });

    // 익명 로그인
    const credential = await signInAnonymously(auth);

    userUid.textContent = credential.user.uid;

    writeLog('익명 인증 완료');

    // 최근 이벤트 수신
    const recentEventsQuery = query(
        eventsRef,
        limitToLast(30)
    );

    onChildAdded(recentEventsQuery, snapshot => {
        const event = snapshot.val();

        // 페이지를 열기 전에 발생한 기록은 속도 측정에서 제외
        if (!event.sent_at_client ||
            event.sent_at_client < pageStartedAt) {
            return;
        }

        // 자신이 전송한 이벤트는 상대방 수신속도 측정에서 제외
        if (event.client_id === clientId) {
            return;
        }

        const receivedDelay =
            Date.now() - event.sent_at_client;

        writeLog(
            `상대 이벤트 수신: ${receivedDelay}ms · ${event.message}`
        );
    });

    // 이벤트 전송
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

    // 테스트 기록 삭제
    clearButton.addEventListener('click', async () => {
        await remove(eventsRef);
        logElement.textContent = '';
        writeLog('테스트 기록을 초기화했습니다.');
    });
</script>
