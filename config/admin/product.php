<?php

$prd_kind_name = [
    'ONAHOLE' => "오나홀",
    'TORSO' => "토르소",
    'BREAST' => "가슴장난감",
    'VIBRATOR' => "바이브레이터",
    'DILDO' => "딜도",
    'ANAL' => "애널",
    'MAN' => "남성보조",
    'WOMAN' => "여성용품",
    'GEL' => "윤활젤",
    'CONDOM' => "콘돔",
    'NIPPLE' => "니플(유두)",
    'PERFUME' => "향수",
    'PILLOW' => "필로우",
    'AIRDOLL' => "에어돌",
    'UNDERWEAR' => "속옷",
    'COSTUME' => "코스튬",
    'BDSM' => "BDSM",
    'SIDE' => "보조용품",
    'SET' => "세트상품",
    'ONLYORDER' => "주문전용상품",
    'REALDOLL' => "리얼돌",
];

$categories = [
    [
        'code' => '01000000',
        'key' => 'ONAHOLE',
        'name' => '오나홀',
        'children' => []
    ],
    [
        'code' => '02000000',
        'key' => 'TORSO',
        'name' => '리얼/토르소',
        'children' => [
            [
                'code' => '02010000',
                'key' => 'TORSO',
                'name' => '토르소형 ',
                'children' => []
            ],
            [
                'code' => '02020000',
                'key' => 'BREAST',
                'name' => '가슴장난감',
                'children' => []
            ],
            [
                'code' => '02030000',
                'key' => 'BUTT',
                'name' => '엉덩이형',
                'children' => []
            ],
            [
                'code' => '02060000',
                'key' => 'LEG',
                'name' => '하반신형',
                'children' => []
            ],
            [
                'code' => '02040000',
                'key' => 'BODY_PART',
                'name' => '신체부위',
                'children' => []
            ],
            [
                'code' => '02050000',
                'key' => 'REALDOLL',
                'name' => '리얼돌',
                'children' => []
            ],
        ]
    ],
    [
        'code' => '03000000',
        'key' => 'VIBRATOR',
        'name' => '바이브레이터',
        'children' => []
    ],
    [
        'code' => '04000000',
        'key' => 'DILDO',
        'name' => '딜도',
        'children' => []
    ],
    [
        'code' => '05000000',
        'key' => 'ANAL',
        'name' => '애널',
        'children' => [
            [
                'code' => '05010000',
                'key' => 'ANAL_PLUG_MANUAL',
                'name' => '애널플러그 (수동)',
                'children' => []
            ],
            [
                'code' => '05020000',
                'key' => 'ANAL_PLUG_VIBE',
                'name' => '애널플러그 (진동)',
                'children' => []
            ],
            [
                'code' => '05030000',
                'key' => 'ANAL_BEADS_MANUAL',
                'name' => '애널비즈 (수동)',
                'children' => []
            ],
            [
                'code' => '05040000',
                'key' => 'ANAL_BEADS_VIBE',
                'name' => '애널비즈 (진동)',
                'children' => []
            ],
            [
                'code' => '05050000',
                'key' => 'ANAL_VIBE',
                'name' => '애널 바이브',
                'children' => []
            ],
            [
                'code' => '05060000',
                'key' => 'ANAL_DILDO',
                'name' => '애널 딜도',
                'children' => []
            ],
            [
                'code' => '05070000',
                'key' => 'ANAL_PURE_CRYSTAL',
                'name' => '퓨어 크리스탈',
                'children' => []
            ],
            [
                'code' => '05080000',
                'key' => 'ANAL_PROSTATE_ANEROS',
                'name' => '전립선/아네로스',
                'children' => []
            ],
            [
                'code' => '05090000',
                'key' => 'ANAL_EXPAND_PUMP',
                'name' => '애널 확장/펌프',
                'children' => []
            ],
            [
                'code' => '05100000',
                'key' => 'ANAL_CARE_SUPPORT',
                'name' => '애널 관리/보조',
                'children' => []
            ],
            [
                'code' => '05110000',
                'key' => 'ANAL_TAIL_ACCESSORY',
                'name' => '애널 테일/액세서리',
                'children' => []
            ],
        ]
    ],
    [
        'code' => '06000000',
        'key' => 'MAN',
        'name' => '남성보조',
        'children' => [
            [
                'code' => '06010000',
                'key' => 'MAN_COCKRING_BASIC',
                'name' => '콕링/보조링',
                'children' => []
            ],
            [
                'code' => '06020000',
                'key' => 'MAN_COCKRING_PREMIUM',
                'name' => '콕링 고급형',
                'children' => []
            ],
            [
                'code' => '06030000',
                'key' => 'MAN_VIBE_RING',
                'name' => '바이브/진동 링',
                'children' => []
            ],
            [
                'code' => '06040000',
                'key' => 'MAN_ENHANCE_EXTEND',
                'name' => '남성 강화/확장',
                'children' => []
            ],
            [
                'code' => '06050000',
                'key' => 'MAN_COVER_SLEEVE',
                'name' => '커버/슬리브',
                'children' => []
            ],
        ]
    ],
    [
        'code' => '07000000',
        'key' => 'WOMAN',
        'name' => '여성용품',
        'children' => []
    ],
    [
        'code' => '08000000',
        'key' => 'GEL',
        'name' => '윤활젤',
        'children' => []
    ],
    [
        'code' => '09000000',
        'key' => 'CONDOM',
        'name' => '콘돔',
        'children' => []
    ],
    [
        'code' => '10000000',
        'key' => 'NIPPLE',
        'name' => '니플(유두)',
        'children' => []
    ],
    [
        'code' => '11000000',
        'key' => 'PERFUME',
        'name' => '향수',
        'children' => []
    ],
    [
        'code' => '12000000',
        'key' => 'PILLOW',
        'name' => '필로우',
        'children' => []
    ],
    [
        'code' => '13000000',
        'key' => 'AIRDOLL',
        'name' => '에어돌',
        'children' => []
    ],
    [
        'code' => '14000000',
        'key' => 'UNDERWEAR',
        'name' => '속옷',
        'children' => []
    ],
    [
        'code' => '15000000',
        'key' => 'COSTUME',
        'name' => '코스튬',
        'children' => []
    ],
    [
        'code' => '16000000',
        'key' => 'BDSM',
        'name' => 'BDSM',
        'children' => []
    ],
    [
        'code' => '17000000',
        'key' => 'SIDE',
        'name' => '보조용품',
        'children' => []
    ],
    [
        'code' => '18000000',
        'key' => 'SET',
        'name' => '세트상품',
        'children' => []
    ],
    [
        'code' => '19000000',
        'key' => 'ONLYORDER',
        'name' => '주문전용상품',
        'children' => []
    ],
];


$importing_country = [
    'jp' => "일본",
    'cn' => "중국",
    'kr' => "한국",
    'dollar' => "그외 달러 국가",
];

$data = [
    'prd_kind_name' => $prd_kind_name,
    'categories' => $categories,
    'importing_country' => $importing_country
];

return $data;