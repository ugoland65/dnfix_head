CREATE TABLE `competitor_popup_permissions` (
  `idx` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_user_idx` INT UNSIGNED NOT NULL,
  `site_code` VARCHAR(64) NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0은 해당 사이트 전체 상품',
  `can_view` TINYINT(1) NOT NULL DEFAULT 0,
  `can_memo_write` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uq_competitor_popup_permission` (`admin_user_idx`, `site_code`, `product_id`),
  KEY `idx_competitor_popup_permission_lookup` (`admin_user_idx`, `site_code`, `product_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 예시: 관리자 123에게 특정 사이트의 모든 경쟁사 상품 조회 및 메모 권한 부여
-- INSERT INTO competitor_popup_permissions
-- (admin_user_idx, site_code, product_id, can_view, can_memo_write, is_active)
-- VALUES (123, 'example_site', 0, 1, 1, 1);
