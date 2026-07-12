-- 구매대행 발주 전용 테이블 (header + items)
-- MySQL / MariaDB 기준

CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `idx` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_name` VARCHAR(120) NOT NULL COMMENT '발주서명',
  `po_code` VARCHAR(60) NOT NULL COMMENT 'P/O 코드',
  `supplier_no` INT DEFAULT NULL COMMENT '공급사번호',
  `supplier_name` VARCHAR(120) NOT NULL COMMENT '공급사명',
  `item_count` INT NOT NULL DEFAULT 0 COMMENT '라인 수',
  `total_quantity` INT NOT NULL DEFAULT 0 COMMENT '총 수량',
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT '총 금액',
  `status` VARCHAR(20) NOT NULL DEFAULT 'created' COMMENT 'created/downloaded/closed',
  `memo` TEXT NULL,
  `created_by` INT DEFAULT NULL,
  `created_name` VARCHAR(80) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uniq_po_code` (`po_code`),
  KEY `idx_supplier_no` (`supplier_no`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `idx` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_order_idx` BIGINT UNSIGNED NOT NULL COMMENT 'purchase_orders.idx',
  `godo_order_goods_id` BIGINT UNSIGNED NOT NULL COMMENT 'godo_order_goods.idx',
  `order_goods_sno` BIGINT NOT NULL COMMENT '고도 orderGoodsSno',
  `order_no` VARCHAR(40) NOT NULL COMMENT '고도 주문번호',
  `goods_no` BIGINT DEFAULT NULL COMMENT '고도 goodsNo',
  `goods_name` VARCHAR(255) NOT NULL,
  `option_info` TEXT NULL COMMENT '옵션 JSON 스냅샷',
  `scm_no` INT DEFAULT NULL,
  `scm_name` VARCHAR(120) DEFAULT NULL,
  `goods_count` INT NOT NULL DEFAULT 1,
  `goods_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `goods_total_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `receiver_name` VARCHAR(80) DEFAULT NULL,
  `receiver_phone` VARCHAR(40) DEFAULT NULL,
  `receiver_cell_phone` VARCHAR(40) DEFAULT NULL,
  `receiver_zonecode` VARCHAR(20) DEFAULT NULL,
  `receiver_address` VARCHAR(255) DEFAULT NULL,
  `receiver_address_sub` VARCHAR(255) DEFAULT NULL,
  `order_memo` TEXT NULL,
  `created_by` INT DEFAULT NULL,
  `created_name` VARCHAR(80) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `uniq_order_goods_sno` (`order_goods_sno`),
  KEY `idx_purchase_order_idx` (`purchase_order_idx`),
  KEY `idx_order_no` (`order_no`),
  KEY `idx_scm_no` (`scm_no`),
  KEY `idx_godo_order_goods_id` (`godo_order_goods_id`),
  CONSTRAINT `fk_purchase_order_items_purchase_order` FOREIGN KEY (`purchase_order_idx`) REFERENCES `purchase_orders` (`idx`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

