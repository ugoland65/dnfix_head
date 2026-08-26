# 인트라넷 Firebase 실시간 기능 적용 계획

작성일: 2026-08-25  
대상 환경: 카페24 웹호스팅, PHP 자체 개발 프레임워크, MySQL/MariaDB

## 1. 작업 목적

인트라넷 페이지를 열어 둔 상태에서 다음 기능을 제공한다.

- 새 알림 실시간 표시
- 특정 주문서나 업무 페이지를 보고 있는 직원 표시
- 같은 주문서를 동시에 수정하는 상황 안내
- 다른 직원이 먼저 저장한 데이터를 이전 화면의 값으로 덮어쓰는 문제 방지

Firebase Realtime Database는 실제 업무 데이터를 저장하는 용도가 아니라 **실시간 신호와 접속 상태 전달용**으로만 사용한다.

실제 주문, 결제, 상품, 알림 내역은 기존 카페24 MySQL에 저장한다.

## 2. 현재까지 완료된 작업

- [x] Firebase 프로젝트 생성
- [x] Firebase Realtime Database 생성
- [x] Singapore 리전 선택
- [x] 웹 앱 등록 및 `firebaseConfig` 발급
- [x] 익명 인증 활성화
- [x] 테스트 페이지 연결 확인
- [x] 두 브라우저 간 실시간 이벤트 송수신 확인
- [ ] 운영용 인증으로 교체
- [ ] 인트라넷 공통 알림 적용
- [ ] 페이지 접속자 표시 적용
- [ ] 주문서 편집 상태 적용
- [ ] MySQL 저장 충돌 방지 적용

## 3. 최종 구성

```text
인트라넷 로그인
→ PHP가 Firebase Custom Token 발급
→ 브라우저가 Firebase에 로그인
→ 실시간 신호 및 접속 상태 구독

업무 데이터 변경
→ 카페24 MySQL 저장
→ 저장 성공 후 Firebase에 작은 신호 기록
→ 대상 직원 브라우저에 즉시 전달
→ 브라우저가 필요한 데이터만 PHP API로 조회
```

### 역할 구분

| 구분 | 저장 위치 | 역할 |
| --- | --- | --- |
| 실제 주문·결제·상품 데이터 | MySQL | 최종 원본 데이터 |
| 알림 제목·내용·읽음 상태 | MySQL | 알림 이력 관리 |
| 새 알림 발생 신호 | Firebase | 브라우저에 실시간 전달 |
| 페이지 접속자 | Firebase | 현재 보고 있는 직원 표시 |
| 편집 중 상태 | Firebase | 동시 편집 안내 및 입력 제한 |
| 저장 충돌 방지 | MySQL `row_version` | 이전 데이터 덮어쓰기 차단 |

## 4. 운영 적용 순서

### 1단계: Firebase 운영 인증

- [ ] 익명 인증을 인트라넷 로그인 기반 Custom Token 인증으로 교체
- [ ] Firebase 서비스 계정 발급
- [ ] 서비스 계정 JSON을 웹 공개 경로 밖에 보관
- [ ] PHP 버전 확인
- [ ] Composer 사용 가능 여부 확인
- [ ] PHP용 Firebase 연결 라이브러리 설치
- [ ] `/api/firebase/token` API 구현
- [ ] 브라우저에서 `signInWithCustomToken()` 적용
- [ ] Firebase 운영 보안 규칙 적용
- [ ] 운영 전 익명 인증 비활성화

### 2단계: 실시간 알림

- [ ] 직원별 개인 신호 구독
- [ ] 전체 직원 공통 신호 구독
- [ ] 알림 생성 후 Firebase 신호 발송
- [ ] 신호 수신 시 해당 알림만 PHP API로 조회
- [ ] 인트라넷 상단 알림 UI와 연결
- [ ] Firebase 전송 실패 로그 저장

### 3단계: 페이지 접속자

- [ ] 주문서 페이지 진입 시 Firebase presence 등록
- [ ] `onDisconnect()` 자동 삭제 예약
- [ ] 같은 주문서를 보고 있는 직원 목록 표시
- [ ] 여러 브라우저 탭을 구분하는 connection ID 적용

### 4단계: 편집 상태

- [ ] 조회 중과 편집 중 상태 구분
- [ ] 편집 시작 시 `editing: true` 기록
- [ ] 다른 편집자가 있으면 조회 전용으로 전환
- [ ] 관리자의 편집권 강제 해제 기능 검토
- [ ] 브라우저 종료·인터넷 단절 시 자동 해제 확인

### 5단계: MySQL 저장 충돌 방지

- [ ] 주요 업무 테이블에 `row_version` 추가
- [ ] 데이터 조회 응답에 현재 `row_version` 포함
- [ ] 저장 시 `WHERE row_version = ?` 조건 적용
- [ ] 영향받은 행이 0건이면 충돌 안내
- [ ] 최신 데이터 다시 불러오기 기능 적용

## 5. 운영용 사용자 인증 구조

인트라넷 관리자 PK를 Firebase UID로 사용한다.

```text
인트라넷 admin idx: 12
Firebase uid: admin_12
```

브라우저 요청:

```text
GET /api/firebase/token
```

PHP 응답 예시:

```json
{
  "success": true,
  "token": "FIREBASE_CUSTOM_TOKEN",
  "uid": "admin_12"
}
```

Custom Token 추가 정보 예시:

```json
{
  "intranet": true,
  "admin_idx": 12,
  "admin_name": "김실장",
  "admin_role": "manager"
}
```

브라우저 로그인 코드:

```javascript
import {
    getAuth,
    signInWithCustomToken
} from 'https://www.gstatic.com/firebasejs/12.18.0/firebase-auth.js';

const response = await fetch('/api/firebase/token');
const result = await response.json();

if (!result.success) {
    throw new Error('Firebase 인증 실패');
}

const auth = getAuth();
await signInWithCustomToken(auth, result.token);
```

## 6. 서비스 계정 관리

Firebase 콘솔 경로:

```text
설정
→ 프로젝트 설정
→ 서비스 계정
→ 새 비공개 키 생성
```

권장 저장 위치 예시:

```text
/home/ACCOUNT/private/firebase-service-account.json
/home/ACCOUNT/public_html/
```

주의사항:

- 서비스 계정 JSON을 Git에 등록하지 않는다.
- JavaScript나 HTML에 포함하지 않는다.
- 브라우저에서 다운로드 가능한 경로에 두지 않는다.
- 가능하면 파일 권한을 `600`으로 설정한다.
- 설정 파일 경로는 서버 전용 설정에서 관리한다.

## 7. PHP 클래스 구성안

```text
app/
├─ Services/
│  └─ FirebaseRealtimeService.php
└─ Controllers/
   └─ FirebaseController.php

private/
└─ firebase-service-account.json
```

서비스 클래스 기본 역할:

```php
class FirebaseRealtimeService
{
    // 인트라넷 로그인 사용자의 Custom Token 발급
    public function createCustomToken(array $admin): string
    {
    }

    // 특정 직원에게 실시간 신호 전송
    public function publishToAdmin(
        int $adminIdx,
        string $eventType,
        array $data = []
    ): void {
    }

    // 전체 직원에게 실시간 신호 전송
    public function publishGlobal(
        string $eventType,
        array $data = []
    ): void {
    }
}
```

재개 시 먼저 확인할 항목:

```php
<?php
echo PHP_VERSION;
```

SSH 사용이 가능하면:

```bash
php -v
composer --version
```

PHP는 공식 Firebase Admin SDK 지원 대상이 아니므로 PHP 버전과 Composer 환경을 확인한 후 호환되는 `kreait/firebase-php` 버전을 선택한다.

## 8. Firebase 데이터 구조

Firebase에는 실제 알림 내용이나 주문 전체 정보를 저장하지 않는다.

```json
{
  "signals": {
    "global": {
      "event_id": "evt_20260825153000123",
      "event_type": "notice_created",
      "created_at": 1787643000000
    },
    "users": {
      "admin_12": {
        "event_id": "evt_20260825153100456",
        "event_type": "notification_created",
        "notification_idx": 971,
        "created_at": 1787643060000
      }
    }
  },
  "presence": {
    "orders": {
      "2608251234567890": {
        "admin_12": {
          "connections": {
            "connection_abc": {
              "admin_name": "김실장",
              "editing": true,
              "connected_at": 1787643000000
            }
          }
        }
      }
    }
  }
}
```

### 신호 데이터 원칙

- 직원별 마지막 신호를 덮어쓰는 방식으로 시작한다.
- 알림 이력은 MySQL에 저장한다.
- Firebase 신호에는 `notification_idx` 등 최소 정보만 넣는다.
- 브라우저는 신호를 받은 경우에만 PHP API를 한 번 호출한다.
- 인트라넷 최초 진입 시에는 MySQL에서 미확인 알림을 한 번 조회한다.

## 9. 운영용 Firebase 보안 규칙 초안

익명 인증을 제거하고 Custom Token 인증을 적용한 후 사용한다.

```json
{
  "rules": {
    ".read": false,
    ".write": false,

    "signals": {
      "global": {
        ".read": "auth != null && auth.token.intranet === true",
        ".write": false
      },
      "users": {
        "$uid": {
          ".read": "auth != null && auth.uid === $uid",
          ".write": false
        }
      }
    },

    "presence": {
      ".read": "auth != null && auth.token.intranet === true",
      "$resourceType": {
        "$resourceId": {
          "$uid": {
            ".write": "auth != null && auth.uid === $uid"
          }
        }
      }
    }
  }
}
```

보안 원칙:

- 신호 쓰기는 PHP 서버만 수행한다.
- 직원은 자신의 개인 신호만 읽는다.
- 접속 상태는 인증된 인트라넷 직원만 읽는다.
- 접속 상태는 본인의 UID 경로에만 쓸 수 있다.
- 운영 적용 전에 Firebase Rules Playground로 권한을 검수한다.

## 10. 브라우저 실시간 알림 수신

직원별 개인 신호 구독:

```javascript
import {
    getDatabase,
    ref,
    onValue
} from 'https://www.gstatic.com/firebasejs/12.18.0/firebase-database.js';

const database = getDatabase();
const adminUid = 'admin_12';

const personalSignalRef = ref(
    database,
    `signals/users/${adminUid}`
);

let initialized = false;

onValue(personalSignalRef, async snapshot => {
    const signal = snapshot.val();

    if (!signal) {
        return;
    }

    // 최초 연결 때 기존 신호를 새 알림으로 처리하지 않는다.
    if (!initialized) {
        initialized = true;
        return;
    }

    if (signal.event_type === 'notification_created') {
        await loadNotification(signal.notification_idx);
    }

    if (signal.event_type === 'order_updated') {
        handleOrderUpdated(signal);
    }
});
```

전체 신호 구독:

```javascript
const globalSignalRef = ref(database, 'signals/global');

onValue(globalSignalRef, snapshot => {
    const signal = snapshot.val();

    if (!signal) {
        return;
    }

    handleGlobalSignal(signal);
});
```

## 11. PHP 저장 후 Firebase 신호 발송

MySQL 저장이 먼저 성공해야 한다.

```php
$db->beginTransaction();

try {
    $notificationIdx = $notificationService->create([
        'admin_idx' => 12,
        'title' => '주문서가 수정되었습니다.',
        'message' => '주문번호 2608251234567890',
    ]);

    $db->commit();

    // DB 저장 완료 후 Firebase 신호 발송
    try {
        $firebaseRealtimeService->publishToAdmin(
            12,
            'notification_created',
            [
                'notification_idx' => $notificationIdx,
            ]
        );
    } catch (Throwable $firebaseException) {
        error_log('[Firebase] '.$firebaseException->getMessage());
    }
} catch (Throwable $e) {
    $db->rollBack();
    throw $e;
}
```

Firebase 발송 실패 때문에 이미 저장된 MySQL 업무 데이터를 롤백하지 않는다.

## 12. 주문서 접속자 표시

```javascript
import {
    ref,
    push,
    set,
    onValue,
    onDisconnect,
    serverTimestamp
} from 'https://www.gstatic.com/firebasejs/12.18.0/firebase-database.js';

const connectionsRef = ref(
    database,
    `presence/orders/${orderNo}/${adminUid}/connections`
);

const myConnectionRef = push(connectionsRef);

// 접속 정보를 쓰기 전에 서버에 자동 삭제를 먼저 예약한다.
await onDisconnect(myConnectionRef).remove();

await set(myConnectionRef, {
    admin_name: adminName,
    editing: false,
    connected_at: serverTimestamp()
});

onValue(
    ref(database, `presence/orders/${orderNo}`),
    snapshot => {
        const users = snapshot.val() || {};
        renderCurrentUsers(users);
    }
);
```

주의사항:

- `onDisconnect().remove()` 예약을 먼저 성공시킨 후 접속 상태를 기록한다.
- 한 직원이 여러 탭을 열 수 있으므로 connection ID를 별도로 생성한다.
- 직원 UID 하나만 저장하면 한 탭을 닫을 때 다른 탭 접속 정보까지 사라질 수 있다.

## 13. MySQL 저장 충돌 방지

예시 테이블이 `godo_orders`인 경우:

```sql
ALTER TABLE godo_orders
ADD COLUMN row_version INT UNSIGNED NOT NULL DEFAULT 1
COMMENT '동시 수정 확인 버전';
```

조회 시 현재 버전도 브라우저에 전달한다.

```json
{
  "idx": 971,
  "order_no": "2608251234567890",
  "row_version": 7
}
```

저장 쿼리:

```sql
UPDATE godo_orders
SET
    order_status = ?,
    row_version = row_version + 1,
    updated_at = NOW()
WHERE idx = ?
  AND row_version = ?;
```

영향받은 행이 0건이면 다른 사용자가 먼저 저장한 것이다.

```php
if ($affectedRows === 0) {
    return [
        'success' => false,
        'error_code' => 'VERSION_CONFLICT',
        'message' => '다른 사용자가 먼저 수정했습니다. 최신 내용을 다시 불러와 주세요.',
    ];
}
```

Firebase 편집 상태는 사용자 편의를 위한 소프트 잠금이고, `row_version`은 실제 데이터 보호 장치다. 둘을 함께 사용한다.

## 14. 테스트 계획

### 인증

- [ ] 로그인한 직원만 Firebase 연결 가능
- [ ] 로그아웃 상태에서는 토큰 API 호출 거부
- [ ] 직원 A가 직원 B의 개인 신호를 읽을 수 없음
- [ ] 익명 사용자가 운영 데이터에 접근할 수 없음

### 알림

- [ ] 개인 알림이 해당 직원에게만 표시됨
- [ ] 전체 알림이 모든 접속 직원에게 표시됨
- [ ] Firebase 연결이 끊겼다가 복구되어도 페이지가 정상 동작함
- [ ] Firebase 발송 실패 시 MySQL 데이터는 정상 저장됨
- [ ] 최초 접속 시 과거 신호가 새 알림처럼 중복 표시되지 않음

### 접속자

- [ ] 주문서 진입 즉시 접속자 표시
- [ ] 정상적으로 페이지를 닫으면 접속자 제거
- [ ] 브라우저 강제 종료 후 접속자 제거
- [ ] 인터넷 단절 후 접속자 제거
- [ ] 같은 직원의 여러 탭이 각각 정상 관리됨

### 동시 수정

- [ ] 직원 A가 편집 중이면 직원 B는 조회 전용
- [ ] 편집 상태가 사라지면 직원 B가 편집 가능
- [ ] 소켓 상태가 잘못되어도 `row_version`으로 덮어쓰기 차단
- [ ] 충돌 발생 시 최신 데이터 다시 불러오기 가능

## 15. 운영 전 최종 보안 체크

- [ ] 익명 인증 비활성화
- [ ] `/speed_test` 데이터와 테스트 규칙 삭제
- [ ] 서비스 계정 JSON이 공개 경로 밖에 있음
- [ ] 서비스 계정 JSON이 Git에서 제외됨
- [ ] Firebase Rules에서 기본 읽기·쓰기 모두 차단
- [ ] 직원별 UID 권한 검증
- [ ] Custom Token API에서 인트라넷 세션 검증
- [ ] Firebase 오류 로그에 토큰이나 서비스 계정 키를 출력하지 않음
- [ ] Firebase 사용량 및 결제 알림 설정

## 16. 작업 재개 시 첫 확인사항

다음 작업을 시작하기 전에 아래 정보를 확인한다.

```text
1. 카페24 PHP 버전
2. Composer 사용 가능 여부
3. 자체 프레임워크의 로그인 세션 관리자 정보 구조
4. 관리자 PK 컬럼명
5. 공통 레이아웃 JavaScript 파일 위치
6. 알림 테이블 존재 여부와 PK
7. 첫 적용 대상 주문 테이블명과 PK
```

확인 결과를 바탕으로 다음 파일부터 구현한다.

```text
1. FirebaseRealtimeService.php
2. Firebase token API Controller
3. 공통 firebase-realtime.js
4. 운영용 Firebase Rules
5. 개인 알림 테스트 기능
6. 주문서 presence 기능
7. row_version 충돌 방지 기능
```

## 17. 핵심 결정사항 요약

- Firebase Realtime Database를 먼저 사용해 반응속도를 확인한다.
- Firebase는 실시간 신호와 presence만 담당한다.
- 실제 업무 데이터와 알림 이력은 MySQL에 유지한다.
- AJAX 주기 조회는 사용하지 않는다.
- 신호가 발생한 경우에만 필요한 PHP API를 호출한다.
- 익명 인증은 테스트 단계에서만 사용한다.
- 운영에서는 인트라넷 로그인 기반 Custom Token을 사용한다.
- 동시 수정 표시는 Firebase로 처리한다.
- 최종 저장 충돌은 MySQL `row_version`으로 차단한다.

