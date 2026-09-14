<?php

return [
    // 고도몰 → 인트라넷 상품 컨텐츠 조회 API 키
    // 헤더 X-Api-Key 또는 쿼리 api_key
    'inbound_api_key' => (string)(getenv('GODO_INBOUND_API_KEY') ?: 'sdm_pdc_a1_7c3e9f2b4d81a6e0c5b2f8d1a4e7c093'),
];
