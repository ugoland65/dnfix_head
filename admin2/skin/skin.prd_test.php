
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


