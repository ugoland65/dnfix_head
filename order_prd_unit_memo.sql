-- 주문상품 단위별 입고 검수 메모 컬럼
-- MySQL / MariaDB 기준
ALTER TABLE `order_prd_unit`
    ADD COLUMN `stock_inspection_memo` TEXT NULL
    COMMENT '입고 수량검수 메모'
    AFTER `stock_inspection_data`;
