-- godo_order_goods <-> purchase_orders 연결 컬럼 추가
-- MySQL 8.0+ / MariaDB 기준

ALTER TABLE `godo_order_goods`
  ADD COLUMN IF NOT EXISTS `purchase_order_idx` BIGINT UNSIGNED NULL COMMENT 'purchase_orders.idx 연결키' AFTER `purchase_status`;

ALTER TABLE `godo_order_goods`
  ADD INDEX IF NOT EXISTS `idx_purchase_order_idx` (`purchase_order_idx`);

-- 이미 생성된 발주 데이터가 있으면 order_goods_sno 기준으로 역매핑
UPDATE `godo_order_goods` GOG
INNER JOIN `purchase_order_items` POI
  ON POI.`order_goods_sno` = GOG.`order_goods_sno`
SET
  GOG.`purchase_order_idx` = POI.`purchase_order_idx`,
  GOG.`purchase_status` = '발주서생성'
WHERE GOG.`purchase_order_idx` IS NULL;

-- FK 연결 (제약명 충돌 시 이름 변경)
ALTER TABLE `godo_order_goods`
  ADD CONSTRAINT `fk_godo_order_goods_purchase_order`
  FOREIGN KEY (`purchase_order_idx`) REFERENCES `purchase_orders` (`idx`)
  ON UPDATE CASCADE
  ON DELETE SET NULL;
