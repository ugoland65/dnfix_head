ALTER TABLE `product_collection_item`
    ADD COLUMN `translated_accessories` TEXT NULL COMMENT '수집 부속품 번역본' AFTER `error_message`,
    ADD COLUMN `translated_maker_comment` MEDIUMTEXT NULL COMMENT '수집 메이커 코멘트 번역본' AFTER `translated_accessories`,
    ADD COLUMN `translation_updated_at` DATETIME NULL COMMENT '번역 최종 수정 시각' AFTER `translated_maker_comment`;
