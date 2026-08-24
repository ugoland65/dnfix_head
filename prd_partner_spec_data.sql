ALTER TABLE `prd_partner`
    ADD COLUMN `spec_data` JSON NULL COMMENT '공통 상품 상세스펙' AFTER `category_code`;
