-- MariaDB: JSON 타입 변경 전 비정상/빈 JSON 데이터를 안전한 빈 객체로 정리합니다.
UPDATE `partners`
SET `info` = JSON_OBJECT()
WHERE `info` IS NULL
   OR TRIM(`info`) = ''
   OR JSON_VALID(`info`) = 0;

UPDATE `partners`
SET `reg` = JSON_OBJECT()
WHERE `reg` IS NULL
   OR TRIM(`reg`) = ''
   OR JSON_VALID(`reg`) = 0;

ALTER TABLE `partners`
    MODIFY COLUMN `info` JSON NULL COMMENT '거래처 상세 정보',
    MODIFY COLUMN `reg` JSON NULL COMMENT '생성·수정 이력',
    ADD COLUMN `bank_name` VARCHAR(100) NULL COMMENT '은행 이름' AFTER `category`,
    ADD COLUMN `bank_account` VARCHAR(255) NULL COMMENT '은행 계좌번호' AFTER `bank_name`,
    ADD COLUMN `bank_account_name` VARCHAR(100) NULL COMMENT '계좌 예금주명' AFTER `bank_account`,
    ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성일' AFTER `reg`,
    ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일' AFTER `created_at`;
