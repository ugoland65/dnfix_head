<?php

// prdDB 카테고리 대분류 (상품당 1개만, 중복 불가)
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
    'SIDE' => "관리/보조",
    'SET' => "세트상품",
    'ONLYORDER' => "주문전용상품",
    'REALDOLL' => "리얼돌",
];

// prdDB 상세 카테고리
$categories = [
    [
        'code' => '01000000',
        'key' => 'ONAHOLE',
        'name' => '오나홀',
        'children' => [
            [
                'code' => '01010000',
                'key' => 'ONAHOLE_COMPACT',
                'name' => '컴팩트',
                'children' => []
            ],
            [
                'code' => '01020000',
                'key' => 'ONAHOLE_HANDY_S',
                'name' => '핸디형 S',
                'children' => []
            ],
            [
                'code' => '01030000',
                'key' => 'ONAHOLE_HANDY_M',
                'name' => '핸디형 M',
                'children' => []
            ],
            [
                'code' => '01040000',
                'key' => 'ONAHOLE_HANDY_L',
                'name' => '핸디형 L',
                'children' => []
            ],
            [
                'code' => '01050000',
                'key' => 'ONAHOLE_MEDIUM_LARGE',
                'name' => '중 / 대형',
                'children' => []
            ],
            [
                'code' => '01060000',
                'key' => 'ONAHOLE_ELECTRIC_AUTO',
                'name' => '전동 / 자동형',
                'children' => []
            ],
            [
                'code' => '01070000',
                'key' => 'ONAHOLE_FERA',
                'name' => '페라형',
                'children' => []
            ],
            [
                'code' => '01080000',
                'key' => 'ONAHOLE_CUPHOLDER',
                'name' => '컵홀형',
                'children' => []
            ],
            [
                'code' => '01090000',
                'key' => 'ONAHOLE_BOTTOM',
                'name' => '바닥오나',
                'children' => []
            ],
            [
                'code' => '01100000',
                'key' => 'ONAHOLE_BODY_FETISH',
                'name' => '신체 / 페티쉬',
                'children' => []
            ],
            [
                'code' => '01110000',
                'key' => 'ONAHOLE_COTTON_TYPE',
                'name' => '면타입',
                'children' => []
            ],
        ]
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
                'children' => [
                    ['code' => '02010100', 'key' => 'TORSO_MINI', 'name' => '미니 토르소', 'children' => []],
                    ['code' => '02010200', 'key' => 'TORSO_STANDARD', 'name' => '라이트 토르소', 'children' => []],
                    ['code' => '02010300', 'key' => 'TORSO_LARGE', 'name' => '리얼 토르소', 'children' => []],
                ],
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
                'name' => '리얼돌/전신형',
                'children' => []
            ],
            [
                'code' => '02070000',
                'key' => 'HEAD',
                'name' => '헤드',
                'children' => []
            ],
            [
                'code' => '02080000',
                'key' => 'FURRY',
                'name' => '퍼리/피규어',
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
        'children' => [
            [
                'code' => '08010000',
                'key' => 'GEL_ONAHOLE',
                'name' => '오나 윤활제',
                'children' => []
            ],
            [
                'code' => '08020000',
                'key' => 'GEL_BASIC',
                'name' => '기본 윤활제',
                'children' => []
            ],
            [
                'code' => '08030000',
                'key' => 'GEL_WARMING',
                'name' => '발열 윤활제',
                'children' => []
            ],
            [
                'code' => '08040000',
                'key' => 'GEL_MENTHOL',
                'name' => '멘톨 윤활제',
                'children' => []
            ],
            [
                'code' => '08050000',
                'key' => 'GEL_SALIVA',
                'name' => '타액 윤활제',
                'children' => []
            ],
            [
                'code' => '08060000',
                'key' => 'GEL_SCENTED',
                'name' => '향기 윤활제',
                'children' => []
            ],
            [
                'code' => '08070000',
                'key' => 'GEL_ANAL',
                'name' => '애널 윤활제',
                'children' => []
            ],
            [
                'code' => '08080000',
                'key' => 'GEL_CREAMY',
                'name' => '백탁 윤활제',
                'children' => []
            ],
            [
                'code' => '08090000',
                'key' => 'GEL_FUNCTIONAL_LOTION',
                'name' => '기능성 로션',
                'children' => []
            ],
        ]
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
        'children' => [
            [
                'code' => '15010000',
                'key' => 'COSTUME_MENS_UNDERWEAR',
                'name' => '남성 언더웨어',
                'children' => [
                    ['code' => '15010100', 'key' => 'COSTUME_MENS_UNDERWEAR_SEXY_PANTY', 'name' => '섹시/팬티', 'children' => []],
                    ['code' => '15010200', 'key' => 'COSTUME_MENS_UNDERWEAR_DRAWERS', 'name' => '드로즈', 'children' => []],
                    ['code' => '15010300', 'key' => 'COSTUME_MENS_UNDERWEAR_BRIEFS', 'name' => '삼각', 'children' => []],
                    ['code' => '15010400', 'key' => 'COSTUME_MENS_UNDERWEAR_OTHER', 'name' => '기타', 'children' => []],
                ],
            ],
            [
                'code' => '15020000',
                'key' => 'COSTUME_COSPLAY',
                'name' => '코스프레',
                'children' => [
                    ['code' => '15020100', 'key' => 'COSTUME_COSPLAY_NURSE', 'name' => '간호사', 'children' => []],
                    ['code' => '15020200', 'key' => 'COSTUME_COSPLAY_CHEONGSAM', 'name' => '치파오', 'children' => []],
                    ['code' => '15020300', 'key' => 'COSTUME_COSPLAY_BUNNY_CATGIRL', 'name' => '바니걸 & 캣걸', 'children' => []],
                    ['code' => '15020400', 'key' => 'COSTUME_COSPLAY_SAILOR', 'name' => '세라복', 'children' => []],
                    ['code' => '15020500', 'key' => 'COSTUME_COSPLAY_SCHOOLMIZ', 'name' => '스쿨미즈', 'children' => []],
                    ['code' => '15020600', 'key' => 'COSTUME_COSPLAY_UNIFORM_SECRETARY', 'name' => '제복/비서', 'children' => []],
                    ['code' => '15020700', 'key' => 'COSTUME_COSPLAY_EVENT', 'name' => '이벤트', 'children' => []],
                    ['code' => '15020800', 'key' => 'COSTUME_COSPLAY_MAID', 'name' => '메이드', 'children' => []],
                    ['code' => '15020900', 'key' => 'COSTUME_COSPLAY_KIMONO', 'name' => '기모노', 'children' => []],
                    ['code' => '15021000', 'key' => 'COSTUME_COSPLAY_ANIME_GAME', 'name' => '애니/게임', 'children' => []],
                    ['code' => '15021100', 'key' => 'COSTUME_COSPLAY_CHRISTMAS', 'name' => '크리스마스', 'children' => []],
                    ['code' => '15021200', 'key' => 'COSTUME_COSPLAY_SEXY_KNIT', 'name' => '섹시 니트', 'children' => []],
                ],
            ],
            [
                'code' => '15030000',
                'key' => 'COSTUME_SEXY_SLIP',
                'name' => '섹시 슬립',
                'children' => [
                    ['code' => '15030100', 'key' => 'COSTUME_SEXY_SLIP_BASIC', 'name' => '섹시 슬립', 'children' => []],
                    ['code' => '15030200', 'key' => 'COSTUME_SEXY_SLIP_GOWN', 'name' => '가운 슬립', 'children' => []],
                    ['code' => '15030300', 'key' => 'COSTUME_SEXY_SLIP_LONG', 'name' => '롱 슬립', 'children' => []],
                    ['code' => '15030400', 'key' => 'COSTUME_SEXY_SLIP_CORSET', 'name' => '코르셋', 'children' => []],
                    ['code' => '15030500', 'key' => 'COSTUME_SEXY_SLIP_SLEEPWEAR', 'name' => '섹시 실내복', 'children' => []],
                    ['code' => '15030600', 'key' => 'COSTUME_SEXY_SLIP_ALL_IN_ONE', 'name' => '섹시 홀인원', 'children' => []],
                    ['code' => '15030700', 'key' => 'COSTUME_SEXY_SLIP_GARTER', 'name' => '가터 슬립', 'children' => []],
                ],
            ],
            [
                'code' => '15040000',
                'key' => 'COSTUME_SEXY_PANTY',
                'name' => '섹시 팬티',
                'children' => [
                    ['code' => '15040100', 'key' => 'COSTUME_SEXY_PANTY_DAILY', 'name' => '데일리 팬티', 'children' => []],
                    ['code' => '15040200', 'key' => 'COSTUME_SEXY_PANTY_MESH', 'name' => '망사 팬티', 'children' => []],
                    ['code' => '15040300', 'key' => 'COSTUME_SEXY_PANTY_CROTCHLESS', 'name' => '갈라 & 밑트임 팬티', 'children' => []],
                    ['code' => '15040400', 'key' => 'COSTUME_SEXY_PANTY_LACE_RIBBON', 'name' => '레이스 & 리본 팬티', 'children' => []],
                    ['code' => '15040500', 'key' => 'COSTUME_SEXY_PANTY_G_STRING', 'name' => 'G끈 팬티', 'children' => []],
                    ['code' => '15040600', 'key' => 'COSTUME_SEXY_PANTY_C', 'name' => 'C 팬티', 'children' => []],
                    ['code' => '15040700', 'key' => 'COSTUME_SEXY_PANTY_T', 'name' => 'T 팬티', 'children' => []],
                    ['code' => '15040800', 'key' => 'COSTUME_SEXY_PANTY_STRING', 'name' => '끈 팬티', 'children' => []],
                    ['code' => '15040900', 'key' => 'COSTUME_SEXY_PANTY_CUTE', 'name' => '큐티 팬티', 'children' => []],
                ],
            ],
            [
                'code' => '15050000',
                'key' => 'COSTUME_SEXY_BRA',
                'name' => '섹시 브라',
                'children' => [
                    ['code' => '15050100', 'key' => 'COSTUME_SEXY_BRA_SET', 'name' => '브라 set', 'children' => []],
                    ['code' => '15050200', 'key' => 'COSTUME_SEXY_BRA_PAD', 'name' => '뽕 패드', 'children' => []],
                    ['code' => '15050300', 'key' => 'COSTUME_SEXY_BRA_OTHER', 'name' => '기타 용품', 'children' => []],
                    ['code' => '15050400', 'key' => 'COSTUME_SEXY_BRA_NUDE', 'name' => '누드 브라', 'children' => []],
                ],
            ],
            [
                'code' => '15060000',
                'key' => 'COSTUME_SEXY_DRESS',
                'name' => '섹시 드레스',
                'children' => [
                    ['code' => '15060100', 'key' => 'COSTUME_SEXY_DRESS_OFF_SHOULDER', 'name' => '오프숄더', 'children' => []],
                    ['code' => '15060200', 'key' => 'COSTUME_SEXY_DRESS_SHORT_SLEEVE', 'name' => '반/민소매', 'children' => []],
                    ['code' => '15060300', 'key' => 'COSTUME_SEXY_DRESS_LONG_SLEEVE', 'name' => '긴 소매', 'children' => []],
                ],
            ],
            [
                'code' => '15070000',
                'key' => 'COSTUME_STOCKING',
                'name' => '스타킹',
                'children' => [
                    ['code' => '15070100', 'key' => 'COSTUME_STOCKING_BODY', 'name' => '바디 스타킹', 'children' => []],
                    ['code' => '15070200', 'key' => 'COSTUME_STOCKING_PANTY', 'name' => '팬티 스타킹', 'children' => []],
                    ['code' => '15070300', 'key' => 'COSTUME_STOCKING_BAND', 'name' => '밴드 스타킹', 'children' => []],
                    ['code' => '15070400', 'key' => 'COSTUME_STOCKING_BODY_SKIRT', 'name' => '바디 스커트 스타킹', 'children' => []],
                    ['code' => '15070500', 'key' => 'COSTUME_STOCKING_GARTER_BELT', 'name' => '가터벨트 스타킹', 'children' => []],
                    ['code' => '15070600', 'key' => 'COSTUME_STOCKING_OVER_KNEE', 'name' => '오버니삭스', 'children' => []],
                    ['code' => '15070700', 'key' => 'COSTUME_STOCKING_TWO_PIECE', 'name' => '투피스 스타킹', 'children' => []],
                    ['code' => '15070800', 'key' => 'COSTUME_STOCKING_CROTCHLESS', 'name' => '밑트임 스타킹', 'children' => []],
                ],
            ],
            [
                'code' => '15080000',
                'key' => 'COSTUME_GARTER_BELT',
                'name' => '가터벨트',
                'children' => [
                    ['code' => '15080100', 'key' => 'COSTUME_GARTER_BELT_SET', 'name' => '가터벨트 set', 'children' => []],
                    ['code' => '15080200', 'key' => 'COSTUME_GARTER_BELT_BASIC', 'name' => '가터벨트', 'children' => []],
                ],
            ],
            [
                'code' => '15090000',
                'key' => 'COSTUME_ACCESSORY',
                'name' => '악세서리',
                'children' => [
                    ['code' => '15090100', 'key' => 'COSTUME_ACCESSORY_MASK_BLINDFOLD', 'name' => '가면 & 안대', 'children' => []],
                    ['code' => '15090200', 'key' => 'COSTUME_ACCESSORY_HEADBAND', 'name' => '머리띠', 'children' => []],
                    ['code' => '15090300', 'key' => 'COSTUME_ACCESSORY_OTHER', 'name' => '기타', 'children' => []],
                    ['code' => '15090400', 'key' => 'COSTUME_ACCESSORY_CHOKER_BAND', 'name' => '초커 & 밴드', 'children' => []],
                ],
            ],
        ]
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
        'name' => '관리/보조',
        'children' => [
            [
                'code' => '17010000',
                'key' => 'SIDE_CLEAN',
                'name' => '세척/세정',
                'children' => []
            ],
            [
                'code' => '17020000',
                'key' => 'SIDE_POWDER',
                'name' => '관리/파우더',
                'children' => []
            ],
            [
                'code' => '17030000',
                'key' => 'SIDE_WARMER',
                'name' => '워머/히팅',
                'children' => []
            ],
            [
                'code' => '17040000',
                'key' => 'SIDE_DRY',
                'name' => '드라이/건조',
                'children' => []
            ],
            [
                'code' => '17050000',
                'key' => 'SIDE_POUCH',
                'name' => '보관/파우치',
                'children' => []
            ],
            [
                'code' => '17060000',
                'key' => 'SIDE_ONAHOLE_ACCESSORY',
                'name' => '오나홀 보조',
                'children' => []
            ],
            [
                'code' => '17070000',
                'key' => 'SIDE_OTHER',
                'name' => '기타용품',
                'children' => []
            ],
        ]
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

/**
 * 1차 카테고리별 서브 카테고리 (중복 선택 가능)
 * - 메인은 $prd_kind_name 중 1개만 가진다.
 * - 서브는 1차 카테고리 안에서 그룹(특징별 등) > 항목 구조이며, 상품마다 여러 개 선택할 수 있다.
 * - 저장은 추가 구분과 같이 product_category_mappings 를 사용한다.
 *   category_type = 'sub', category_code = 항목 코드(ONAHOLE_FEATURE_VORTEX 등).
 *   그룹 코드(ONAHOLE_FEATURE)는 UI 묶음용이며 매핑에는 넣지 않는다.
 * - 다른 1차 카테고리도 같은 형태로 그룹을 추가하면 된다. (예: TORSO => 특징별)
 */
$subCategoriesByKind = [
    'ONAHOLE' => [
        [
            'code' => 'ONAHOLE_FEATURE',
            'name' => '특징별',
            'children' => [
                [
                    'code' => 'ONAHOLE_FEATURE_VORTEX',
                    'name' => '소용돌이 구조',
                    'description' => '주름형 구조가 나선으로 되어 사용시 휘감는 효과가 있는 경우',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_MULTI_MATERIAL',
                    'name' => '다중소재 구조',
                    'description' => '소재가 1개 이상 사용된 구조',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_CLEAR',
                    'name' => '클리어/투명',
                    'description' => '투명한 재질이 사용된 구조',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_MINI_BODY',
                    'name' => '미니바디 조형',
                    'description' => '토르소 형태로 조형된 구조, 가슴 조형 등이 있는 경우',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_ZERO_DIMENSION',
                    'name' => '무차원 가공',
                    'description' => '공간 없이 헤집고 가야 하는 경우',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_TWO_HOLE',
                    'name' => '2구멍',
                    'description' => '1개 이상 구멍이 있는 경우',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_THROUGH',
                    'name' => '관통 유형',
                    'description' => '앞뒤가 뚫린 유형',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_WOMB',
                    'name' => '자궁홀',
                    'description' => '자궁 기믹, 포르치오 기믹이 있는 경우',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_ANAL',
                    'name' => '애널형',
                    'description' => '애널 기믹, 컨셉이 애널이라고 표현한 경우',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_FOLD',
                    'name' => '주름형(히다계)',
                    'description' => '일정한 간격으로 주름 기믹이 있는 경우',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_NUB',
                    'name' => '돌기형(이보계)',
                    'description' => '돌기가 있는 경우',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_HERRING_ROE',
                    'name' => '청어알형',
                    'description' => '둥근 돌기들이 청어알처럼 모여 있는 경우',
                    'image' => '',
                ],
                [
                    'code' => 'ONAHOLE_FEATURE_ELECTRIC',
                    'name' => '전자기믹',
                    'description' => '배터리, 전원 연결해서 진동·전자 효과가 있는 경우',
                    'image' => '',
                ],
            ],
        ],
    ],
];

// 취향/태그
$preferenceTags = [
    'FUTANARI' => [
        'code' => 'FUTANARI',
        'name' => '후타나리',

        // 고객에게 표시되는 설명
        'description' => '여성의 신체적 특징과 남성 성기가 함께 표현된 판타지 장르를 뜻합니다.',

        // 운영자용 상품 분류 기준
        'admin_description' => '홀과 딜도 기능이 함께 있는 제품',

        // 연결되는 그룹 코드
        'group_codes' => [],

        // 쇼핑몰 카테고리 연결
        'operation_category_code' => null,
        'godo_category_code' => '061001',

        // 사용 여부
        'is_active' => true,
    ],
    'AV_ACTRESS' => [
        'code' => 'AV_ACTRESS',
        'name' => 'AV여배우',
        'description' => '실존 또는 가상 AV 여배우를 모티브로 한 제품입니다.',
        'admin_description' => 'AV 여배우 이름·컨셉이 상품명이나 조형에 명시된 제품',
        'group_codes' => [],
        'operation_category_code' => null,
        'godo_category_code' => '061002',
        'is_active' => true,
    ],
    'GYARU' => [
        'code' => 'GYARU',
        'name' => '갸루',
        'description' => '태닝한 피부, 밝은 머리, 화려한 메이크업 등 갸루 스타일을 모티브로 한 제품입니다.',
        'admin_description' => '갸루(ギャル) 컨셉·조형이 명시된 제품',
        'group_codes' => [],
        'operation_category_code' => null,
        'godo_category_code' => '061003',
        'is_active' => true,
    ],
];


$importing_country = [
    'jp' => "일본",
    'cn' => "중국",
    'kr' => "한국",
    'dollar' => "그외 달러 국가",
];

$purchase_type_options = [
    [
        'code' => 'direct_purchase',
        'value' => '사입',
        'label' => '사입',
    ],
    [
        'code' => 'purchase_agency',
        'value' => '구매대행',
        'label' => '구매대행',
    ],
    [
        'code' => 'consignment',
        'value' => '위탁',
        'label' => '위탁',
    ],
    [
        'code' => 'oem',
        'value' => 'OEM',
        'label' => 'OEM',
    ],
    [
        'code' => 'odm',
        'value' => 'ODM',
        'label' => 'ODM',
    ],
    [
        'code' => 'direct_import',
        'value' => '직수입',
        'label' => '직수입',
    ],
    [
        'code' => 'preorder_purchase',
        'value' => '예약구매',
        'label' => '예약구매',
    ],
    [
        'code' => 'domestic_wholesale',
        'value' => '국내도매',
        'label' => '국내도매',
    ],
    [
        'code' => 'overseas_wholesale',
        'value' => '해외도매',
        'label' => '해외도매(1688)',
    ],
];

$sale_status_options = [
    [
        'code' => 'pre_registered',
        'value' => '가등록',
        'label' => '가등록',
    ],
    [
        'code' => 'new_order',
        'value' => '신상주문',
        'label' => '신상주문',
    ],
    [
        'code' => 'registered',
        'value' => '등록완료',
        'label' => '등록완료',
    ],
    [
        'code' => 'waiting_sale',
        'value' => '입고확정',
        'label' => '입고확정',
    ],
    [
        'code' => 'sale_product',
        'value' => '판매상품',
        'label' => '판매상품',
    ],
    [
        'code' => 'purchase_agency',
        'value' => '구매대행',
        'label' => '구매대행',
    ],
    [
        'code' => 'godo_deleted',
        'value' => '고도몰삭제',
        'label' => '고도몰삭제',
    ],
];

$data = [
    'prd_kind_name' => $prd_kind_name,
    'categories' => $categories,
    'sub_categories_by_kind' => $subCategoriesByKind,
    'preference_tags' => $preferenceTags,
    'importing_country' => $importing_country,
    'purchase_type_options' => $purchase_type_options,
    'sale_status_options' => $sale_status_options,
];

return $data;