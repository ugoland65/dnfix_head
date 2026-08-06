-- 검수 등록 이력별 메모 컬럼
-- MySQL / MariaDB 기준
ALTER TABLE `order_prd_unit_inspection`
    ADD COLUMN `inspection_memo` TEXT NULL
    COMMENT '개별 검수 등록 이력 메모'
    AFTER `inspector_admin_name`;
