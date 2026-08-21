ALTER TABLE `COMPARISON_DB`
    ADD COLUMN `cd_restock_alert_qty` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '재입고 알림 요청 수량' AFTER `cd_godo_code`,
    ADD COLUMN `cd_restock_alert_collected_at` DATETIME NULL COMMENT '재입고 알림 수집 시각' AFTER `cd_restock_alert_qty`;
