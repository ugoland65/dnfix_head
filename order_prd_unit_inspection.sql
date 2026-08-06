-- 주문상품 단위별 입고 수량검수 이력
-- MySQL / MariaDB 기준
-- 동시 최초 등록 시 부모 행이 중복 생성되지 않도록 먼저 적용하세요.
-- ALTER TABLE `order_prd_unit`
--     ADD UNIQUE KEY `uniq_order_bidx_pidx` (`order_idx`, `bidx`, `pidx`);

CREATE TABLE `order_prd_unit_inspection` (
    `idx` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
    `order_prd_unit_idx` BIGINT UNSIGNED NOT NULL COMMENT 'order_prd_unit.idx',
    `checked_qty` INT UNSIGNED NOT NULL COMMENT '이번에 검수한 수량',
    `inspector_admin_idx` BIGINT UNSIGNED NOT NULL COMMENT '검수자 관리자 PK',
    `inspector_admin_id` VARCHAR(80) NOT NULL COMMENT '검수자 관리자 ID 스냅샷',
    `inspector_admin_name` VARCHAR(80) NOT NULL COMMENT '검수자 이름 스냅샷',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
    PRIMARY KEY (`idx`),
    KEY `idx_order_prd_unit_idx` (`order_prd_unit_idx`),
    KEY `idx_inspector_admin_idx` (`inspector_admin_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='주문상품 단위 입고 수량검수 이력';
