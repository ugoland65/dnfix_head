<?php

return [
    'launch_url' => rtrim((string)(getenv('B_COMPETITOR_DETAIL_LAUNCH_URL') ?: 'https://dnetc01.mycafe24.com/api/CompetitorProductDetail/launch'), '?'),
    'audience' => (string)(getenv('B_TICKET_AUDIENCE') ?: 'competitor-detail-popup'),
    'issuer' => (string)(getenv('A_TICKET_ISSUER') ?: 'a1-intranet'),
    'private_key' => (string)getenv('B_TICKET_PRIVATE_KEY'),
    'private_key_path' => (string)(getenv('B_TICKET_PRIVATE_KEY_PATH') ?: '/dnfixhead/secure/a-to-b-popup-private.pem'),
    'algorithm' => (string)(getenv('B_TICKET_ALGORITHM') ?: 'RS256'),
    'ttl_seconds' => min(60, max(1, (int)(getenv('B_TICKET_TTL_SECONDS') ?: 60))),
];
