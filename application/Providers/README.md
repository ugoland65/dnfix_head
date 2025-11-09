# Service Providers

사이트별로 구분된 Service Provider 디렉토리입니다.

## 📁 폴더 구조

```
Providers/
├── Onadb/
│   └── ViewServiceProvider.php     # Onadb 사이트 전용 (모든 로직 포함)
├── Admin/
│   └── ViewServiceProvider.php     # Admin 사이트 전용 (모든 로직 포함)
└── README.md
```

## 💡 설계 철학

**Provider 중심 아키텍처**
- ViewServiceProvider에 모든 View Composer 로직 통합
- ViewComposers 폴더 없이 Provider에서 직접 관리
- 간결하고 명확한 구조

## 🎯 사용 방법

### Onadb 사이트
```php
// onadb/autoloader.php
use App\Providers\Onadb\ViewServiceProvider;

ViewServiceProvider::register();
```

### Admin 사이트
```php
// admin/autoloader.php
use App\Providers\Admin\ViewServiceProvider;

ViewServiceProvider::register();
```

## ✨ 각 사이트별로 독립적인 설정 관리

- **Onadb**: 오나디비 사이트 전용 View Composer 및 설정
- **Admin**: 관리자 페이지 전용 View Composer 및 설정
- **확장 가능**: 새로운 사이트 추가 시 새 폴더만 생성

## 🔧 ViewServiceProvider 역할

1. **View Composer 등록**: View와 데이터 바인딩
2. **사이트별 공통 데이터 주입**: 세션, 메타 등
3. **레이아웃 설정**: 사이드바, 테마 등
4. **메타 데이터 관리**: SEO 최적화
5. **DB 데이터 로드**: Service Layer를 통한 데이터 조회

## 📝 ViewServiceProvider 구조

```php
class ViewServiceProvider
{
    public static function register(): void
    {
        View::composer('onadb.*', function($view) {
            self::bindSessionData($view);      // 세션
            self::bindMetaData($view);         // 메타
            self::bindLayoutSettings($view);   // 레이아웃
            self::bindDatabaseData($view);     // DB
        });
    }
    
    private static function bindSessionData($view) { ... }
    private static function bindMetaData($view) { ... }
    private static function bindLayoutSettings($view) { ... }
    private static function bindDatabaseData($view) { ... }
    
    // Service 사용
    private static function getRecentComments() {
        $service = new ProductCommentService();
        return $service->getRecentComments();
    }
}
```

## 🆕 새 사이트 추가 방법

```bash
# 1. Provider 폴더 생성
mkdir -p application/Providers/NewSite

# 2. ViewServiceProvider.php 생성 (Onadb 복사 후 수정)
cp application/Providers/Onadb/ViewServiceProvider.php \
   application/Providers/NewSite/ViewServiceProvider.php

# 3. autoloader 생성
# newsite/autoloader.php
<?php
require_once __DIR__ . '/../application/Core/Autoloader.php';
use App\Providers\NewSite\ViewServiceProvider;

Autoloader::register();
require_once __DIR__ . '/../application/helpers.php';
ViewServiceProvider::register();
```

