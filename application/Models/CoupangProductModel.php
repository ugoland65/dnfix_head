<?php
namespace App\Models;

use App\Core\BaseModel;

class CoupangProductModel extends BaseModel 
{
    protected $table = 'coupang_products';

    protected $fillable = [
        // ===== 기본 식별 =====
        'product_id',                 // BIGINT | 쿠팡 productId
        'seller_product_id',          // BIGINT | 쿠팡 sellerProductId (상세조회 키)

        // ===== 기본 정보 =====
        'name',                       // VARCHAR(255) | 상품명
        'brand',                      // VARCHAR(255) | 브랜드

        // ===== 카테고리 =====
        'category_id',                // INT | 내부 카테고리 ID
        'display_category_code',      // INT | 노출 카테고리 코드

        // ===== 상태 =====
        'status',                     // VARCHAR(50) | 상품 상태 (승인완료 등)
        'requested',                  // VARCHAR(10) | 승인 요청 여부 (Y/N)
        'status_updated_at',          // DATETIME | 상태 변경 시간

        // ===== 판매 기간 =====
        'sale_started_at',            // DATETIME | 판매 시작일
        'sale_ended_at',              // DATETIME | 판매 종료일

        // ===== 쿠팡 생성 정보 =====
        'coupang_created_at',         // DATETIME | 쿠팡 등록일

        // ===== 판매자 정보 =====
        'vendor_id',                  // VARCHAR(50) | 판매자 ID
        'md_id',                      // VARCHAR(100) | MD ID
        'md_name',                    // VARCHAR(100) | MD 이름

        // ===== 상품 확장 정보 =====
        'display_product_name',       // VARCHAR(255) | 노출 상품명
        'general_product_name',       // VARCHAR(255) | 옵션 제외 상품명

        'manufacturer',               // VARCHAR(255) | 제조사
        'product_group',              // VARCHAR(100) | 상품 그룹

        // ===== 배송 정보 =====
        'delivery_method',            // VARCHAR(50) | 배송 방식
        'delivery_company_code',      // VARCHAR(50) | 택배사 코드
        'delivery_charge_type',       // VARCHAR(50) | 배송비 유형
        'delivery_charge',            // INT | 배송비
        'free_ship_over_amount',      // INT | 무료배송 조건 금액

        'delivery_charge_on_return',  // INT | 초기 배송비
        'return_charge',              // INT | 반품 배송비
        'remote_area_deliverable',    // VARCHAR(10) | 도서산간 배송 여부 (Y/N)

        'outbound_shipping_place_code', // VARCHAR(50) | 출고지 코드

        // ===== 반품 정보 =====
        'return_center_code',         // VARCHAR(50) | 반품센터 코드
        'return_charge_name',         // VARCHAR(255) | 반품지 이름
        'company_contact_number',     // VARCHAR(50) | 반품 연락처
        'return_zip_code',            // VARCHAR(20) | 반품지 우편번호

        // ===== 상품 속성 =====
        'adult_only',                 // VARCHAR(10) | 성인상품 여부 (Y/N)
        'tax_type',                   // VARCHAR(50) | 과세 유형
        'parallel_imported',          // VARCHAR(10) | 병행수입 여부 (Y/N)
        'overseas_purchased',         // VARCHAR(10) | 해외구매 여부 (Y/N)
        'pcc_needed',                 // VARCHAR(10) | 개인통관부호 필요 여부 (Y/N)

        // ===== 상품 상태 추가 =====
        'offer_condition',            // VARCHAR(50) | 상품 상태 (NEW/USED 등)
        'offer_description',          // TEXT | 상품 상태 설명

        // ===== 검색/메타 =====
        'search_tags',                // JSON | 검색 태그
        'extra_properties',           // JSON | 추가 속성 key-value

        // ===== 인증 / 기타 =====
        'certifications_json',        // JSON | 인증 정보
        'bundle_info',                // JSON | 묶음 상품 정보

        // ===== 상세 콘텐츠 =====
        'content_json',               // JSON | 상세 콘텐츠 (HTML)
        'contents_type',              // VARCHAR(50) | 콘텐츠 타입
        'images_json',                // JSON | 이미지 정보
        'notices_json',               // JSON | 상품 고시 정보
        'attributes_json',            // JSON | 속성 정보
        'items_json',                 // JSON | 옵션 원본 데이터

        // ===== 옵션 핵심 =====
        'main_vendor_item_id',        // BIGINT | 대표 옵션 vendorItemId
        'vendor_item_id',             // BIGINT | 일반 상품 vendorItemId
        'rocket_vendor_item_id',      // BIGINT | 로켓 상품 vendorItemId
        'is_rocket',                  // CHAR(1) | 로켓 여부 (Y/N)

        // ===== 원본 데이터 =====
        'raw_json',                  // JSON | API 전체 원본

        // ===== 동기화 =====
        'synced_at',                 // DATETIME | 리스트 동기화 시간
        'detail_loaded_at',          // DATETIME | 상세 API 수집 시간

        // ===== 동기화 관리 =====
        'api_created_at',          // DATETIME | 최초 수집 시간
        'last_synced_at',          // DATETIME | 마지막 동기화 시간
        'last_synced_by',          // INT | 관리자 PK
        'last_synced_by_info',     // JSON | 관리자 정보 스냅샷

        // ===== 이미지 =====
        'thumbnail', // VARCHAR(500) | 대표 이미지 URL

        // ===== 재고 JSON =====
        'stock_json',           // JSON | 일반 상품 재고/가격 원본
        'rocket_stock_json',    // JSON | 로켓 상품 재고/가격 원본
        'stock_synced_at',      // DATETIME | 재고 동기화 시간

        'ps_idx', // INT | prd_stock.idx

    ];
}