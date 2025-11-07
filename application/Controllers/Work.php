<?php
namespace App\Controllers;

use App\Core\BaseClass;


class Work extends BaseClass {

    public function __construct() {
        parent::__construct();
    }

	/**
	 * 워크정보 불러오기
	 */
	public function getWorkInfo() {




$jsObject = <<<JS
{
    "219": {
        "name_ko" : "토이즈 하트",
        "name_en" : "Toy's Heart",
        "code" : "TH",
        "img" : "/dg_image/brand_image/th_300.jpg",
        "bg" : "/dg_image/brand_image/th_bg.png",
        "mobile_bg" : "/dg_image/brand_image/th_bg_mobile.png",
        "w_logo" : "/dg_image/brand_image/th_w_logo.png?v=1",
        "size" : "mid",
        "cate_no" : "219",
		"introduce" : "1983년도에 설립되어 2중구조 오나홀의 초인기 시리즈인 세븐틴 시리즈를 필두로 <br>한 손으로 사용 가능한 핸드홀을 중점적으로 다수의 히트 상품을 출시해온 브랜드 입니다. <br>특화된 브랜드 파워로 인하여 모르는 사람이 없을 정도로 존재감이 있는 만큼 안정된 상품력을 자랑합니다."
	},
    "62": {
        "name_ko" : "타마토이즈",
        "name_en" : "Tama toys",
        "code" : "TM",
        "img" : "/dg_image/brand_image/tama_300.jpg",
        "bg" : "/dg_image/brand_image/tama_bg.png",
        "mobile_bg" : "/dg_image/brand_image/tama_bg_mobile.png",
        "w_logo" : "/dg_image/brand_image/tama_w_logo.png?v=1",
        "size" : "mid",
        "cate_no" : "62",
		"introduce" : "일본의 유명한 AV 제작사인 Total Media Agency의 성인용품 브랜드 입니다. <br>다양한 캐릭터와 스토리 라인, 유명한 애니메이션의 패러디, 인기 있는 버추얼 캐릭터와의 콜라보 등등 <br>트렌드에 민감하게 반응하여 신상품 출시 주기가 매우 빠르고 종류도 다양하기로 유명합니다. <br>오나홀 외 다양한 성인용품을 선보이며 다양한 취향의 사용자들의 니즈를 만족시키는 제품을 출시하기로 유명한 브랜드입니다."
	},
    "55": {
        "name_ko" : "라이드 재팬",
        "name_en" : "RIDE JAPAN",
        "code" : "RJ",
        "img" : "/dg_image/brand_image/ride_300.jpg",
        "bg" : "/dg_image/brand_image/ride_bg.png?v=1",
        "mobile_bg" : "/dg_image/brand_image/ride_bg_mobile.png",
        "w_logo" : "/dg_image/brand_image/ride_w_logo.png",
        "size" : "mid",
        "cate_no" : "55",
		"introduce" : "2011년에 런칭한 비교적 신흥 메이커로 분류되는 브랜드로써 합리적인 가격과 신소재를 활용한 높은 품질의 제품을 <br>연이어 배출하여 꾸준히 사랑받고 있는 브랜드입니다. <br>국내에서도 가성비가 좋다고 입소문이 나있으며 대표 상품인 버진루프 시리즈와 <br>다수의 히트작을 보유하고 있는 브랜드입니다."
	},
    "59": {
        "name_ko" : "닛포리 기프트",
        "name_en" : "N.P.G",
        "code" : "NG",
        "img" : "/dg_image/brand_image/npg_300.jpg",
        "bg" : "/dg_image/brand_image/npg_bg.png?v=1",
        "mobile_bg" : "/dg_image/brand_image/npg_bg_mobile.png",
        "w_logo" : "/dg_image/brand_image/npg_w_logo.png?v=1",
        "size" : "mid",
        "cate_no" : "59",
		"introduce" : "오랜 전통을 자랑하는 대형 성인 업체 브랜드입니다.<br>명기의 증명 시리즈 등과 같이 실제와 혼동할 수 있을 만큼의 정교한 제품을 중심적으로 출시하며 핸드형 뿐만 아닌 <br>중, 대형 홀까지 높은 퀄리티를 자랑하는 상품을 다수 생산하고 있습니다."
	},
    "56": {
        "name_ko" : "매직아이즈",
        "name_en" : "Magic eyes",
        "code" : "ME",
        "img" : "/dg_image/brand_image/magic_300.jpg",
        "bg" : "/dg_image/brand_image/magic_bg.png",
        "mobile_bg" : "/dg_image/brand_image/magic_bg_mobile.png",
        "w_logo" : "/dg_image/brand_image/magic_w_logo.png?v=1",
        "size" : "mid",
        "cate_no" : "56",
		"introduce" : "독창성 넘치는 상품과 실용성이 높은 오나홀을 출시하는 인기 브랜드입니다.<br>국내에서는 진실의 입, 스지망 쿠파 로린코 시리즈 등이 유명한 히트작으로 알려져 있습니다.<br>핸드홀 부터 대형홀 그리고 윤활젤 등 다른 소재를 융합하거나 유녀 조형을 장점으로 하는 기술력이 뛰어나기로 정평이 나 있습니다."
	},
    "58": {
        "name_ko" : "텐가",
        "name_en" : "TENGA",
        "code" : "TG",
        "img" : "/dg_image/brand_image/tenga_300.jpg",
        "cate_no" : "58",
		"introduce" : "2005년 설립되어 압도적인 지명도로 오나홀의 대명사가 돼버린 브랜드 입니다.<br>국내에서도 플립홀,컵,에그,스피너 등등 대히트 제품을 출시하여 안전하고 기능적이면서 깔끔한 디자인으로 오나홀의 대중화에 큰 영향을 준 것은 틀림없습니다.<br>퀄리티 높은 디자인과 확실한 기능을 겸비한 제품을 보유하고 있는 브랜드입니다."
	},
    "922": {
        "name_ko" : "이로하",
        "name_en" : "iroha",
        "code" : "TG-IH",
        "img" : "/dg_image/brand_image/iroha_300.jpg",
        "bg" : "/dg_image/brand_image/iroha_bg.png?v=1",
        "cate_no" : "922",
		"introduce" : "성인용품의 유명 브랜드 텐가에서 여성 개발진들이 여성만을 위한 상품을 출시하는 브랜드로 런칭하였습니다.<br>텐가는 2005년 설립되어 압도적인 지명도로 성인용품의 대명사가 돼버린 브랜드로써 퀄리티 높은 디자인과 확실한 기능을 겸비한 제품을 보유하고 있습니다.<br>이로하는 건강한 음식을 먹고 양질의 수면을 취하는 것처럼 여성 욕구를 셀프 플레져 아이템으로 구현 즐겁고, 안전한 셀프케어를 선사합니다."
	},
    "64": {
        "name_ko" : "핫파워즈",
        "name_en" : "Hot Powers",
        "code" : "HP",
        "img" : "/dg_image/brand_image/hp_300.jpg",
        "bg" : "/dg_image/brand_image/hp_bg.png?v=1",
        "mobile_bg" : "/dg_image/brand_image/hp_bg_mobile.png",
        "w_logo" : "/dg_image/brand_image/hp_w_logo.png",
        "size" : "mid",
        "cate_no" : "64",
		"introduce" : "오나홀에대한 도전정신과 장인 정신을 모두 보유한 브랜드로, 놀라운 창의력을 바탕으로 <br>컨셉트의 구현과 몰입감을 제공합니다. 오나홀 브랜드 중 가장 정밀하고 <br>다양한 경도 스펙트럼을 보유하고 있으며, 적재적소에 각 소재를 설계하는 능력이 뛰어납니다."
	},
    "948": {
        "name_ko" : "쿨프",
        "name_en" : "COOLP",
        "code" : "CP",
        "img" : "/dg_image/brand_image/coolp_300.jpg",
        "size" : "mid",
        "cate_no" : "948",
		"introduce" : "2021년 후반 새롭게 런칭하는 브랜드로써 핫파워즈에서 기획하고 타사 공장에서 OEM 제작하는 핫파워즈의 세컨 브랜드입니다.<br>기존의 핫파워즈만의 아이텐티티를 벗어나 다양한 제품을 선보일 것으로 기대되는 브랜드입니다."
	},
    "401": {
        "name_ko" : "로마",
        "name_en" : "Loma",
        "code" : "LM",
        "img" : "/dg_image/brand_image/loma_300.jpg",
        "cate_no" : "401",
		"introduce" : ""
	},
    "1019": {
        "name_ko" : "러스트",
        "name_en" : "LUST",
        "code" : "KO-LT",
        "img" : "/dg_image/brand_image/lust_300.jpg?v=1",
        "bg" : "/dg_image/brand_image/lust_bg.png",
        "bg_color" : "#11423c",
        "mobile_bg" : "",
        "w_logo" : "/dg_image/brand_image/lust_w_logo.png",
        "size" : "mid", "info_class" : "",
        "cate_no" : "1019",
		"introduce" : "2년 동안 2천 번 이상 테스트를 하여 탄생한 제작자의 영혼을 갈아 넣은 상품 <br>이라는 타이틀로 2022년 신규 런칭한 국산 브랜드입니다."
	},
    "73": {
        "name_ko" : "막코스재팬",
        "name_en" : "MACCOS JAPAN",
        "code" : "MC",
        "img" : "/dg_image/brand_image/maccos_300.jpg",
        "bg" : "/dg_image/brand_image/maccos_bg.png",
        "mobile_bg" : "/dg_image/brand_image/maccos_bg_mobile.png",
        "w_logo" : "/dg_image/brand_image/maccos_w_logo.png",
        "size" : "mid",
        "cate_no" : "73",
		"introduce" : "2017년 여름 런칭한 일본의 종합 성인 용품 브랜드입니다. <br>막코스재팬의 maccos는 Max Cost Performance의 약어로 최대의 가성비라는 뜻입니다. <br>강력한 가성비를 중심으로, 과하지 않은 세련된 패키지 디자인을 선보입니다."
	},
    "537": {
        "name_ko" : "지 프로젝트",
        "name_en" : "G PROJECT",
        "code" : "GP",
        "img" : "/dg_image/brand_image/gproject_300.jpg",
        "bg" : "/dg_image/brand_image/gproject_bg.png?v=1",
        "bg_color" : "#fad8cf",
        "mobile_bg" : "/dg_image/brand_image/gproject_bg_mobile.png",
        "w_logo" : "/dg_image/brand_image/gproject_w_logo.png",
        "size" : "mid", "info_class" : "black",
        "cate_no" : "537",
		"introduce" : "완성도 높은 퀄리티와 내구성으로 제작하여 어덜트 굿즈를 안심·안전하게<br>제대로 사용할 수 있는 제품을 만들자라는 기업 목표입니다.<br>깔끔하고 귀여운 패키지 디자인을 선보이며,<br>복잡하지 않고 단순하면서도 강력한 사용감을 제공하는 브랜드입니다."
	},
    "538": {
        "name_ko" : "P P P",
        "name_en" : "PxPxP",
        "code" : "PX",
        "img" : "/dg_image/brand_image/ppp_300.jpg",
        "bg" : "/dg_image/brand_image/ppp_bg.png",
        "size" : "mid",
        "cate_no" : "538",
		"introduce" : "과하지 않으면서 세련된, 하나의 완성품이라는 이미지를 주는 브랜드입니다.<br>에로틱을 진지하게 생각하며 에로틱의 정의 라는 타이틀로 어덜트굿즈를 출시중인 브랜드 입니다.<br>대마인 시리즈와, 푸닛토 딜로 시리즈로 유명합니다."
	},
    "539": {
        "name_ko" : "에그제",
        "name_en" : "eXe",
        "code" : "EX",
        "img" : "/dg_image/brand_image/exe_300.jpg",
        "cate_no" : "539",
		"introduce" : "AV 여배우와의 리얼계 콜라보는 물론, 막강한 '푸니아나'IP를 통해 다양한 오나홀을 전개하고 있습니다.<br>핸디형으로는 실험적인 시도를, 대형은 강력한 완성도를 자랑합니다."
	},
    "75": {
        "name_ko" : "옐로랩",
        "name_en" : "YELOLAB",
        "code" : "YL",
        "img" : "/dg_image/brand_image/yelolab_300.jpg",
        "cate_no" : "75",
		"introduce" : "다양한 협업은 물론 고 볼륨 홀의 소화도 무리없이 해내는, 빠른 성장이 기대되는 브랜드입니다."
	},
    "63": {
        "name_ko" : "키테루키테루",
        "name_en" : "kiterukiteru",
        "code" : "KK",
        "img" : "/dg_image/brand_image/kiteru_300.jpg",
        "cate_no" : "63",
		"introduce" : "인간이 아닌 모에화된 마물을 컨셉트로, 현실엔 존재하지 않는 다양한 판타지 오나홀을 전개하는 브랜드입니다.<br>그 중 촉수를 중심으로 내부구조에 대한 변화를 선도하고 있습니다.<br>두 가지 IP를 집중적으로 전개하고 있습니다."
	},
    "61": {
        "name_ko" : "에이원",
        "name_en" : "a-one",
        "code" : "AO",
        "img" : "/dg_image/brand_image/aone_300.jpg",
        "cate_no" : "61",
		"introduce" : "많은 인기를 얻은 에어돌 러브바디 시리즈로 유명한 브랜드 입니다.<br>계속하여 참신한 상품을 출시하려고 노력하고 있으며 폭 넓은 제품을 취급하고 있는 제조사 입니다."
	},
    "69": {
        "name_ko" : "필웍스",
        "name_en" : "fillworks",
        "code" : "FW",
        "img" : "/dg_image/brand_image/fillworks_300.jpg",
        "cate_no" : "69",
		"introduce" : "높은 가성비와 부담스럽지 않은 볼륨을 가진 핸디형 오나홀을 전개하는 브랜드입니다."
	},
    "76": {
        "name_ko" : "메이트",
        "name_en" : "MATE",
        "code" : "MT",
        "img" : "/dg_image/brand_image/mate_300.jpg",
        "cate_no" : "76",
		"introduce" : "'오나펫 소재'라고 불릴정도로 놀라운 소재를 개발한 브랜드입니다.<br>핸디형 오나홀에 특화되어있으며, 연속 CQ구조의 전문가라고 해도 과언이 아닐 정도로 관통감을 중시하는 브랜드입니다."
	},
    "71": {
        "name_ko" : "키스 미 러브",
        "name_en" : "KISS-ME-LOVE",
        "code" : "KI",
        "img" : "/dg_image/brand_image/kiss_300.jpg",
        "cate_no" : "71",
		"introduce" : "입문하기 좋은 스탠다드 홀을 중심으로 전개하고 있는 브랜드입니다."
	},
    "67": {
        "name_ko" : "엔조이 토이즈",
        "name_en" : "ENJOY TOYS",
        "code" : "EJ",
        "img" : "/dg_image/brand_image/enjoy_300.jpg",
        "cate_no" : "67",
		"introduce" : "2011년 일본에서 설립하여 현제부터 미래의 요구에 대하여 기술력과 상상력을 가지고 항상 진화해 간다는 콘셉트인 브랜드 입니다.<br>부담없이 가볍게 즐길 수 있는 스고망 라인은 물론, AV 여배우 콜라보를 통한 하이엔드 페라홀과 고퀄리티 오나홀을 전개하고 있는 브랜드입니다."
	},
    "74": {
        "name_ko" : "피치 토이즈",
        "name_en" : "PEACH TOYS",
        "code" : "PT",
        "img" : "/dg_image/brand_image/peach_260.jpg",
        "cate_no" : "74",
		"introduce" : ""
	},
    "81": {
        "name_ko" : "이케부쿠로 토이즈",
        "name_en" : "IKEBUKURO TOYS",
        "code" : "ET",
        "img" : "/dg_image/brand_image/ikebukuro_300.jpg",
        "cate_no" : "81",
		"introduce" : ""
	},
    "150": {
        "name_ko" : "오나간",
        "name_en" : "おながん",
        "code" : "OG",
        "img" : "/dg_image/brand_image/onagan_300.jpg",
        "cate_no" : "150",
		"introduce" : ""
	},
    "845": {
        "name_ko" : "세츠겐노 울프 완구",
        "name_en" : "雪原のWOLF玩具",
        "code" : "SW",
        "img" : "/dg_image/brand_image/s_wolf_300.jpg",
        "cate_no" : "845",
		"introduce" : "설원의 늑대 완구"
	},
    "79": {
        "name_ko" : "온도",
        "name_en" : "ONDO!",
        "code" : "OD",
        "img" : "/dg_image/brand_image/ondo_300.jpg",
        "cate_no" : "79",
		"introduce" : ""
	},
	"65": {
        "name_ko" : "러브팩터",
        "name_en" : "LOVE FACTOR",
        "code" : "LF",
        "img" : "/dg_image/brand_image/love_factor.jpg",
        "cate_no" : "65",
		"introduce" : "일본의 종합 어덜트굿즈 브랜드입니다. 두근두근 두근두근 하는 상품을 만드는 것이 브랜드의 목표입니다."
	},
	"437": {
        "name_ko" : "레텐",
        "name_en" : "Leten",
        "code" : "LT",
        "img" : "/dg_image/brand_image/leten_300.jpg",
        "cate_no" : "437",
		"introduce" : ""
	},
	"60": {
        "name_ko" : "렌즈",
        "name_en" : "Rends",
        "code" : "RS",
        "img" : "/dg_image/brand_image/rends_300.jpg",
        "cate_no" : "60",
		"introduce" : "2008년 설립후 소매점으로 출발 소비자의 니즈를 파악하여 일본 내에서 기획·개발된 고품질의 제품을 출시하는것이 목표인 브랜드 입니다.<br>Reality · Extravagant · Neat · Dream · Satisfaction 가 브랜드 사명이며 앞글자를 따서 RENDS라는 회사를 설립 동명의 브랜드를 출시하였습니다.<br>"
	},
	"838": {
        "name_ko" : "보르제",
        "name_en" : "VORZE",
        "code" : "VR",
        "img" : "/dg_image/brand_image/vorze_300.jpg",
        "cate_no" : "838",
		"introduce" : "렌즈의 프로젝트 팀이 만든 프리미엄 브랜드입니다.<br>성인용품을 일반 가전제품과 동등한 퀄리티로 제작한다 라는 컨셉으로 '성 가전'이라는 장르를 구축하는데 성공했습니다."
	},
	"543": {
        "name_ko" : "판타스틱 베이비",
        "name_en" : "FANTASTIC BABY",
        "code" : "RS",
        "img" : "/dg_image/brand_image/fantastic2_300.jpg",
        "mobile_bg" : "/dg_image/brand_image/fantastic2_bg_mobile.png",
        "w_logo" : "/dg_image/brand_image/fantastic2_w_logo.png",
        "size" : "mid",
        "cate_no" : "543",
		"introduce" : "일본의 자사 공장을 보유하고 있는 몇 안 되는 브랜드입니다. <br>2020년 「토이즈 크리에이트」로 브랜드명을 잠깐 변경하였지만 다시 「판타스틱 베이비」라는 브랜드명으로 돌아왔습니다. <br>현재는 토이즈 크리에이트와 독립된 회사로서 각각의 브랜드입니다."
	},
	"70": {
        "name_ko" : "토이즈 크리에이트",
        "name_en" : "Toy's create",
        "code" : "RS",
        "img" : "/dg_image/brand_image/toyscreate_300.jpg",
        "size" : "mid",
        "cate_no" : "70",
		"introduce" : "일본의 성인용품 브랜드입니다. <br>「판타스틱 베이비」의 OEM 브랜드로 알려졌으나 현재는 독립하여 개별 브랜드로 상품을 출시하고 있습니다."
	},
	"532": {
        "name_ko" : "SSI 재팬",
        "name_en" : "SSI JAPAN",
        "code" : "SS",
        "img" : "/dg_image/brand_image/ssi_300.jpg",
        "cate_no" : "532",
		"introduce" : ""
	},
	"78": {
        "name_ko" : "케이엠 프로듀스",
        "name_en" : "K.M.Produce",
        "code" : "KM",
        "img" : "/dg_image/brand_image/kmp_300.jpg",
        "cate_no" : "78",
		"introduce" : "도쿄 시부야 에 위치한 일본의 유명 성인 비디오 (AV) 회사 입니다."
	},
	"531": {
        "name_ko" : "텝펜",
        "name_en" : "TEPPEN",
        "code" : "TP",
        "img" : "/dg_image/brand_image/teppen_300.jpg",
        "cate_no" : "531",
		"introduce" : ""
	},
	"867": {
        "name_ko" : "루네 기프트",
        "name_en" : "Lune Gift",
        "code" : "TP",
        "img" : "/dg_image/brand_image/lune_300.jpg",
        "cate_no" : "867",
		"introduce" : "유한회사 마리골드의 에로게임 브랜드 루네소프트 인기 캐릭터를 콜라보 및 생산한 제품입니다."
	},
	"77": {
        "name_ko" : "리그레 재팬",
        "name_en" : "Ligre japan",
        "code" : "LJ",
        "img" : "/dg_image/brand_image/ligre_300.jpg",
        "bg" : "/dg_image/brand_image/ligre_bg.png?v=1",
        "size" : "mid",
        "cate_no" : "77",
		"introduce" : "일본의 어덜트굿즈 브랜드입니다. 연령이나 성별에 사로잡히지 않고, 성을 있는 그대로 즐기는 것을<br> 리그레 재팬은 응원합니다. 생활 속에서 '즐거움'은 필수 요소이고 성은 즐거움입니다. <br>즐거움 중에 성이 있습니다. 그런 즐거움을 성인용품이라는 카테고리 안에서 만들어 내어<br>누구나 위화감 없이 성을 즐기는 미래를 창조해 갑니다. "
	},
	"952": {
        "name_ko" : "모플 토이즈",
        "name_en" : "MOPLE TOYS",
        "code" : "MP",
        "img" : "/dg_image/brand_image/mople_300.jpg",
        "cate_no" : "952",
		"introduce" : "2021년 4월 신규 런칭한 일본 오나홀 브랜드 입니다."
	},
	"548": {
        "name_ko" : "무소우 토이즈",
        "name_en" : "MOUSOU-TOYS",
        "code" : "MS",
        "img" : "/dg_image/brand_image/mousou_300.jpg",
        "cate_no" : "548",
		"introduce" : "2020년  신규 런칭한 일본 오나홀 브랜드 입니다."
	},
	"149": {
        "name_ko" : "로스쿠르",
        "name_en" : "LoScul",
        "code" : "MS",
        "img" : "/dg_image/brand_image/loscul_300.jpg",
        "cate_no" : "149",
		"introduce" : ""
	},
	"80": {
        "name_ko" : "후지 월드공예",
        "name_en" : "Fuji world",
        "code" : "FJ",
        "img" : "/dg_image/brand_image/fujiworld_300.jpg",
        "cate_no" : "80",
		"introduce" : ""
	},

	"779": {
        "name_ko" : "나카지마 화학",
        "name_en" : "PEPEE by Nakajima Chemicals",
        "code" : "MS",
        "img" : "/dg_image/brand_image/nakajima_300.jpg",
        "cate_no" : "779",
		"introduce" : "1947년 설립된 나카지마 화확사는 사용자가 안심하고 신뢰할 수 있는 인체에 무해하고도 고성능을 유지할 수 있는 환경친화적인 상품을 만들겠다는<br>목표로 1994년 부터 자체 브랜드 페페를 개발하여 런칭하였습니다. 국내에서는 페페젤로 유명한 브랜드 입니다."
	},
	"438": {
        "name_ko" : "이지 러브",
        "name_en" : "Easy Love",
        "code" : "EL",
        "img" : "/dg_image/brand_image/easylove_300.jpg",
        "cate_no" : "438",
		"introduce" : ""
	},
	"894": {
        "name_ko" : "드라이웰",
        "name_en" : "DRYWELL",
        "code" : "KR-DW",
        "img" : "/dg_image/brand_image/drywell_300.jpg",
        "bg" : "/dg_image/brand_image/drywell_bg.png",
        "cate_no" : "894",
		"introduce" : "1987년부터 일본 시부야에서 성인 용품 상점으로 시작하여 수십 년 동안 발전하면서 일본의 성인 산업에서 Sexual Wellness의 주요 브랜드가 되었습니다.<br>2014년 이후 시계 시장에서 돋보이는 활동을 하고 있습니다."
	},
	"783": {
        "name_ko" : "지니",
        "name_en" : "ZINI",
        "code" : "KR-ZN",
        "img" : "/dg_image/brand_image/zini_300.jpg",
        "cate_no" : "783",
		"introduce" : "지니는 한국 섹스토이의 발전을 느낄 수 있는 대표적 브랜드입니다.<br>국내 성인용품업체 ㈜부르르에서 론칭한 이래 다양한 성인 기구를 출시했습니다. 특히 세련된 디자인의 남성 자위 용품, 여성 자위 용품 등은 큰 호응을 받고 있습니다."
	},
	"890": {
        "name_ko" : "네이키드 팩토리",
        "name_en" : "NAKED FACTORY",
        "code" : "KR-NF",
        "img" : "/dg_image/brand_image/naked_300.jpg",
        "cate_no" : "783",
		"introduce" : "2017년 2월 한국 부산에서 설립된 브랜드 입니다.<br>리얼한 소재로 제작된 토르소 전문 대한민국 프리미엄 제조사라는 타이틀과 '양 보다 질'이라는 철학으로 고품질 제품을 선보이고 있습니다."
	},
	"846": {
        "name_ko" : "러브돌",
        "name_en" : "LoveDoll",
        "code" : "KR-LD",
        "img" : "/dg_image/brand_image/lovedoll_300.jpg",
        "cate_no" : "846",
		"introduce" : "한국 성원 프렌차이즈에서 런칭한 브랜드 입니다. 활발하게 직접생산 또는 OEM 방식의 다양한 성인 기구를 출시했습니다.<br>Love doll은 남성 성적 표현에서 상대 또는 대상에 대한 희망을 주로 인형(doll)으로 표현하는데 착안하여 용품을 생산/공급하는데 있어서 정성과 사랑을 담겠다는 신념으로 탄생하였습니다."
	},
	"851": {
        "name_ko" : "시즈마",
        "name_en" : "SIZMA",
        "code" : "KR-SZ",
        "img" : "/dg_image/brand_image/sizma_300.jpg",
        "cate_no" : "851",
		"introduce" : "2008년 론칭한 이후 Sizma(시즈마)는 섹시한 스타일로 2030 여성들의 섹시 라이프 스타일을 대표하는 브랜드입니다.<br>최근에는 에세머를 위한 에스엠용품도 전문적으로 자체 생산하여 출시되고 있어, 트랜드를 반영하는 리더 브랜드로서 다양한 스타일 제품을 합리적인 가격에 제공하는 브랜드 Sizma(시즈마) 입니다."
	},
	"853": {
        "name_ko" : "센스토이",
        "name_en" : "SENSTOY",
        "code" : "KR-ST",
        "img" : "/dg_image/brand_image/senstoy_300.jpg",
        "cate_no" : "853",
		"introduce" : ""
	},
	"936": {
        "name_ko" : "센스바디",
        "name_en" : "SENSBODY",
        "code" : "KR-SB",
        "img" : "/dg_image/brand_image/sensbody_300.jpg",
        "bg" : "/dg_image/brand_image/sensbody_bg.png",
        "cate_no" : "936",
		"introduce" : "넥서스메디케어 주식회사에서 2020년 신규런칭한 브랜드 입니다.<br>꿈꿔오던 판타지 대상과의 콜라보로 실제로 내부까지 본을 떠 내부 주름 깊은 곳 까지 완벽히 재현해 포용적이고 편안하게 즐길 수 있는 오나홀 제품을 출시했습니다.<br>인위적인 자극을 주기보다 리얼주름, 리얼돌기로 실제와 같은 자극으로 더 많은 성적 만족감을 추구하고 있습니다. "
	},
	"943": {
        "name_ko" : "하이쓰",
        "name_en" : "HEISS",
        "code" : "KR-HE",
        "img" : "/dg_image/brand_image/heiss_300.jpg",
        "cate_no" : "943",
		"introduce" : "넥서스메디케어 주식회사에서 런칭한 국산 오나홀 전문 브랜드 입니다."
	},
	"938": {
        "name_ko" : "나비",
        "name_en" : "nabi",
        "code" : "KR-NB",
        "img" : "/dg_image/brand_image/nabi_300.jpg",
        "cate_no" : "938",
		"introduce" : ""
	},
	"833": {
        "name_ko" : "솔로즈",
        "name_en" : "solos",
        "code" : "KR-SL",
        "img" : "/dg_image/brand_image/solos_300.jpg",
        "cate_no" : "833",
		"introduce" : ""
	},
	"873": {
        "name_ko" : "도라토이",
        "name_en" : "DORATOY",
        "code" : "KR-DR",
        "img" : "/dg_image/brand_image/doratoy_300.jpg",
        "cate_no" : "873",
		"introduce" : "2017년 론칭한 국산 브랜드 입니다. 국내생산품인 중형 엉덩이형 오피스걸 시리즈가 대표상품 입니다."
	},
	"951": {
        "name_ko" : "칠색향",
        "name_en" : "七色香",
        "code" : "KR-7C",
        "img" : "/dg_image/brand_image/7color_300.jpg",
        "cate_no" : "951",
		"introduce" : ""
	},

	"861": {
        "name_ko" : "에로카이",
        "name_en" : "EROKAY",
        "code" : "KR-EK",
        "img" : "/dg_image/brand_image/erokay_300.jpg",
        "cate_no" : "861",
		"introduce" : ""
	},
	"863": {
        "name_ko" : "에프스틸",
        "name_en" : "FSTEEL",
        "code" : "KR-FS",
        "img" : "/dg_image/brand_image/fsteel_300.jpg",
        "cate_no" : "863",
		"introduce" : ""
	},
	"866": {
        "name_ko" : "프리티 러브",
        "name_en" : "PRETTY LOVE",
        "code" : "KR-PL",
        "img" : "/dg_image/brand_image/prettylove_300.jpg?v=1",
        "cate_no" : "866",
		"introduce" : "1998년의 중국 성생활건강 전문 위원회 활동을 시작으로,  2011년 독일에서 '프리티 러브' 브랜드를 출범시켰습니다.<br>레드닷 어워드 다중 수상의 깔끔한 디자인을 필두로, 상하이의 성 박람회는 물론 다양한 브랜드 전개를 이어가고 있습니다."
	},
	"896": {
        "name_ko" : "바일러",
        "name_en" : "BAILE",
        "code" : "KR-BA",
        "img" : "/dg_image/brand_image/baile_300.jpg",
        "cate_no" : "896",
		"introduce" : "1993년에 설립된 BAILE는 중국에 제조 및 생산 시스템을 갖추고 미국과 유럽 및 중국에 많은 특허를 포함하여 여러 중요한 인증을 획득하였습니다.<br>회사 이름을 그대로 사용한 바일러 외 프리티러브 외 크레이지불, Mr·play 등등의 브랜드 런칭하였습니다."
	},
	"923": {
        "name_ko" : "크레이지 불",
        "name_en" : "CRAZY BULL",
        "code" : "KR-BA",
        "img" : "/dg_image/brand_image/crazybull_300.jpg",
        "cate_no" : "923",
		"introduce" : "중국의 대형 성인용품 제조회사 바일러(BAILE)에서 남성만을 위한 상품을 출시하는 브랜드로 런칭하였습니다.<br>1993년에 설립된 BAILE는 중국에 제조 및 생산 시스템을 갖추고 미국과 유럽 및 중국에 많은 특허를 포함하여 여러 중요한 인증을 획득하였습니다.<br>윤활제 없이 물로만 사용가능한 워터스킨 제품으로 유명한 브랜드 입니다."
	},
	"918": {
        "name_ko" : "락오프",
        "name_en" : "Rocks-Off",
        "code" : "KR-RF",
        "img" : "/dg_image/brand_image/rockoff_300.jpg?v=1",
        "bg" : "/dg_image/brand_image/rockoff_bg.png",
        "cate_no" : "918",
		"introduce" : "영국에서 2003년 설립된 락오프는 혁신과 품질로 세계적인 명성을 갖는 섹스토이 브랜드 입니다.<br>성인용품 관련 30여개 이상 수상 이력, 영국 섹슈얼 제품의 선도업체이자 성인용품 상위권 랭크 브랜드 입니다.<br>스타일과 기능의 조화를 이루어 혁신적인 하이엔드 제품을 출시하고 있습니다."
	},
	"921": {
        "name_ko" : "다이베",
        "name_en" : "dibe",
        "code" : "KR-DI",
        "img" : "/dg_image/brand_image/dibe_300.jpg",
        "cate_no" : "921",
		"introduce" : "2012년 설립된 중국의 다이베 전자 기술에서 런칭한 성인용품 전문 브랜드입니다.<br>미국, 호주, 영국, 독일 등에서 많은 인기를 얻고 있으며 섹스토이 같지 않은 귀여운 디자인이 돋보입니다."
	},
	"885": {
        "name_ko" : "가라쿠",
        "name_en" : "GALAKU",
        "code" : "KR-GA",
        "img" : "/dg_image/brand_image/galaku_300.jpg",
        "cate_no" : "885",
		"introduce" : ""
	},
	"874": {
        "name_ko" : "에스핸드",
        "name_en" : "S-HANDE",
        "code" : "KR-SH",
        "img" : "/dg_image/brand_image/s_hande_300.jpg",
        "cate_no" : "874",
		"introduce" : "중국에 본사를 두고 중국 OEM 방식으로 생산하여 유럽 및 세계적으로 유통하고 있는 성인용품 브랜드 입니다.<br>독일 브랜드로 디자인 등록되었으며 CE 인증,Ros 인증, FDA 승인, SGS 인증 완료"
	},
	"1053": {
        "name_ko" : "오 마이 스카이",
        "name_en" : "OMYSKY",
        "code" : "KR-OT",
        "img" : "/dg_image/brand_image/omysky_300.jpg",
        "cate_no" : "1053",
		"introduce" : ""
	},
	"884": {
        "name_ko" : "오터치",
        "name_en" : "OTOUCH",
        "code" : "KR-OT",
        "img" : "/dg_image/brand_image/otouch_300.jpg",
        "cate_no" : "884",
		"introduce" : ""
	},
    "82": {
        "name_ko" : "맨즈맥스",
        "name_en" : "Men's max",
        "code" : "MM",
        "img" : "/dg_image/brand_image/mensmax_300.jpg",
        "cate_no" : "82",
		"introduce" : "일본의 엔조이 토이즈(ENJOY TOYS)에서 런칭한 남성 성인용품 전문 브랜드 입니다."
	},
    "899": {
        "name_ko" : "유니더스",
        "name_en" : "unidus",
        "code" : "KR-UD",
        "img" : "/dg_image/brand_image/unidus_300.jpg",
        "cate_no" : "899",
		"introduce" : "(바이오제네틱스) 유니더스는 라텍스 고무 제품을 전문적으로 생산하는 코스닥 상장 기업입니다.<br>주요 제품으로는 콘돔, 지샥크, 장갑 세가지가 있습니다."
	},
    "842": {
        "name_ko" : "프라임",
        "name_en" : "Prime",
        "code" : "PR",
        "img" : "/dg_image/brand_image/prime_300.jpg",
        "cate_no" : "842",
		"introduce" : "프라임은 2017년 런칭한 일본의 성인용품 브랜드입니다.<br>최고의 재미! 궁극의 쾌락! 압도적인 재미를 추구하며 나이트 라이프를 즐겁게 만들자가 모토로 제품을 제작하고 있습니다."
	},
    "942": {
        "name_ko" : "와일드 원",
        "name_en" : "Wild One",
        "code" : "WO",
        "img" : "/dg_image/brand_image/wildone_300.jpg",
        "cate_no" : "942",
		"introduce" : "1991년 설립하여 일본에 시부야에 본점을 두고  신주쿠, 우에노, 신바시 등등의 오프라인 매장 브랜드입니다.<br>제조사 SSI JAPAN과 같은 그룹 계열회사입니다.<br>제조사 SSI JAPAN와 합작여 동명 브랜드 제품을 출시하고 있습니다."
	},
    "68": {
        "name_ko" : "솔브멘",
        "name_en" : "solvemen",
        "code" : "SM",
        "img" : "/dg_image/brand_image/solvemen_300.jpg",
        "cate_no" : "68",
		"introduce" : "2020년 신규 런칭한 일본의 성인용품 브랜드입니다. <br>핸드형 오나홀을 중점적으로 선보이고 있으며 야한 일러스트나 사진으로 패키징을 하는 그동안에 다른 제품들과 차별화된 아이덴티티를 고집하고 있습니다."
	},
    "997": {
        "name_ko" : "아크웨이브",
        "name_en" : "ARCWAVE",
        "code" : "AW",
        "img" : "/dg_image/brand_image/arcwave_300.jpg",
        "bg" : "/dg_image/brand_image/arcwave_bg.png",
        "size" : "mid",
        "cate_no" : "997",
		"introduce" : "여성용 전동 성인용품으로 유명한 우머나이저(Womanizer)와 위-바이브(We-Vibe) 브랜드를 소유한<br>글로벌 섹슈얼 웰니스 그룹 와우테크(Wowtech)가 남성용 브랜드 아크웨이브(Arcwave) 출시<br>아크웨이브는 자신의 즐거움을 새롭게 정의 내리고<br>적극적으로 추구하는 현대적이고 미래지향적인 남성을 목표로 하는 브랜드다."
	},
    "1069": {
        "name_ko" : "데몬킹",
        "name_en" : "Demon King : 大魔王",
        "code" : "TM",
        "img" : "/dg_image/brand_image/demonking_300.jpg",
        "size" : "mid",
        "cate_no" : "1069",
		"introduce" : "大魔王 ACHATUS로 런칭을한 중국의 성인용품 브랜드입니다. 일본의 동명 브랜드 대마왕과 동일한 이름입니다. <br>일본 대마왕의 제품 디자이너가 참여한 제품도 있어 일본 브랜드 대마왕의 영향이 있는 것으로 예상되며 <br>현재는 독립적인 브랜드로서 우수한 제품을 계속 출시하여 라인업을 구축하고 있습니다."
	},
    "1075": {
        "name_ko" : "유이라",
        "name_en" : "YUIRA",
        "code" : "TR",
        "img" : "/dg_image/brand_image/yuira_300.jpg",
        "size" : "mid",
        "cate_no" : "1075",
		"introduce" : "일본의 유통회사 톱 마샬( Top Marshal )에서 런칭한 성인 용품 브랜드입니다. <br>2021년 4월 1일부터 전신인 SMIRAL로부터 성인 용품 제조·판매업 분할 승계되었습니다. <br>I lead you to the Paradise (나는 당신을 파라다이스로 인도한다)라는 슬로건으로 앞세워  <br>주력이 였던 YUIRA 컵 홀 시리즈에서 대형 및 가슴 여성 제품까지 2021년부터 독립된 브랜드로서 더 활발한 신상품을 출시할 것이라 기대합니다."
	},
    "1078": {
        "name_ko" : "핸디",
        "name_en" : "Handy",
        "code" : "TR",
        "img" : "/dg_image/brand_image/handy_300.jpg",
        "size" : "mid",
        "cate_no" : "1078",
		"introduce" : "노르웨이의 스윗테크(Sweet Tech)에서 제작한 핸디(Handy)는 남성의 쾌락을 위해 설계된 자동 스트로입니다. <br>최고의 경험을 추구하기 위해 최고의 기술력 디테일로 제작되었습니다."
	},
    "1087": {
        "name_ko" : "프로페설 제이슨 C",
        "name_en" : "PROF.JASON C",
        "code" : "TR",
        "img" : "/dg_image/brand_image/profjasonc_300.jpg",
        "size" : "mid",
        "cate_no" : "1087",
		"introduce" : "2005년 설립된 홍콩 Chisa-novelties의 성인용품 브랜드입니다."
	},
    "1097": {
        "name_ko" : "새티스파이어",
        "name_en" : "Satisfyer",
        "code" : "ST",
        "img" : "/dg_image/brand_image/satisfyer_300.jpg",
        "size" : "mid",
        "cate_no" : "1097",
		"introduce" : "2016년 설립하여 독일 빌레펠트에 본사를 둔 Triple A Internetshops GmbH에 속한 브랜드로 성, 건강 제품 및 장치를 제공합니다.<br>제품의 특징별로 60여 개의 일러스트로 표현된 패키지가 인상적입니다.<br>비교적 가격경쟁력이 있어 가성비 높은 제품들이 많습니다."
	},
    "1108": {
        "name_ko" : "노토와",
        "name_en" : "NOTOWA",
        "code" : "NO",
        "img" : "/dg_image/brand_image/notowa_300.jpg",
        "size" : "mid",
        "cate_no" : "1108",
		"introduce" : "2022년 신규 런칭한 일본의 오나홀 전문 브랜드입니다. <br>자체 개발한 신소재 인공 피부 소재 no.18 스킨은 공업용 오일을 전혀 사용하지 않고 오로지 식품성 오일만을 사용하여 <br> 피부에 직접 닿는 제품이기 때문에 식품에 사용할 수 있는 규격만을 엄선해 안심 유의해 상품을 개발하고 있습니다."
	},
    "1100": {
        "name_ko" : "메르시",
        "name_en" : "merci",
        "code" : "NO",
        "img" : "/dg_image/brand_image/merci_300.jpg",
        "size" : "mid",
        "cate_no" : "1100",
		"introduce" : "일본의 성인용품 유통회사인 프레시어스(PRECIOUS)의 오리지널 브랜드입니다."
	}
}
JS;

function jsToPhpArray($jsObject) {
    $phpArray = str_replace(
        ['{', '}', ':', 'null', 'true', 'false'],
        ['[', ']', '=>', 'null', 'true', 'false'],
        $jsObject
    );

    // 작은따옴표로 변경 및 키 변환
    $phpArray = preg_replace('/"([^"]+)":/', "'$1' =>", $phpArray);

    // 데이터 내 작은따옴표를 이스케이프 처리
    $phpArray = str_replace("'", "\\'", $phpArray);
    $phpArray = str_replace('"', "'", $phpArray);

    return $phpArray;
}
$phpArray = jsToPhpArray($jsObject); // 변환된 배열
/*

function jsToPhpArray($jsObject) {
    // JavaScript 객체 구문을 PHP 배열 구문으로 변환
    $phpArrayString = str_replace(
        ['{', '}', ':', 'null', 'true', 'false'],
        ['[', ']', '=>', 'null', 'true', 'false'],
        $jsObject
    );

    // 작은따옴표로 변경 및 키 변환
    $phpArrayString = preg_replace('/"([^"]+)":/', "'$1' =>", $phpArrayString);
    $phpArrayString = str_replace('"', "'", $phpArrayString);

    // PHP 배열로 평가
    $phpArray = eval("return $phpArrayString;");

    return $phpArray;
}
*/

/*
// JavaScript 객체를 변환한 PHP 배열이라고 가정
$phpArray = jsToPhpArray($jsObject); // 변환된 배열

// 루프를 사용하여 배열 순회
foreach ($phpArray as $key => $value) {
    echo "Key: $key\n";
    
    // 하위 배열을 순회
    foreach ($value as $subKey => $subValue) {
        echo "  $subKey: $subValue\n";
    }

    echo "\n"; // 항목 구분용 공백
}
*/



$phpArray = [
    '219'=> [
        'name_ko' => '토이즈 하트',
        'name_en' => 'Toy\'s Heart',
        'code' => 'TH',
        'img' => '/dg_image/brand_image/th_300.jpg',
        'bg' => '/dg_image/brand_image/th_bg.png',
        'mobile_bg' => '/dg_image/brand_image/th_bg_mobile.png',
        'w_logo' => '/dg_image/brand_image/th_w_logo.png?v=1',
        'size' => 'mid',
        'cate_no' => '219',
		'introduce' => '1983년도에 설립되어 2중구조 오나홀의 초인기 시리즈인 세븐틴 시리즈를 필두로 
한 손으로 사용 가능한 핸드홀을 중점적으로 다수의 히트 상품을 출시해온 브랜드 입니다. 
특화된 브랜드 파워로 인하여 모르는 사람이 없을 정도로 존재감이 있는 만큼 안정된 상품력을 자랑합니다.'
	],
    '62'=> [
        'name_ko' => '타마토이즈',
        'name_en' => 'Tama toys',
        'code' => 'TM',
        'img' => '/dg_image/brand_image/tama_300.jpg',
        'bg' => '/dg_image/brand_image/tama_bg.png',
        'mobile_bg' => '/dg_image/brand_image/tama_bg_mobile.png',
        'w_logo' => '/dg_image/brand_image/tama_w_logo.png?v=1',
        'size' => 'mid',
        'cate_no' => '62',
		'introduce' => '일본의 유명한 AV 제작사인 Total Media Agency의 성인용품 브랜드 입니다. 
다양한 캐릭터와 스토리 라인, 유명한 애니메이션의 패러디, 인기 있는 버추얼 캐릭터와의 콜라보 등등 
트렌드에 민감하게 반응하여 신상품 출시 주기가 매우 빠르고 종류도 다양하기로 유명합니다. 
오나홀 외 다양한 성인용품을 선보이며 다양한 취향의 사용자들의 니즈를 만족시키는 제품을 출시하기로 유명한 브랜드입니다.'
	],
    '55'=> [
        'name_ko' => '라이드 재팬',
        'name_en' => 'RIDE JAPAN',
        'code' => 'RJ',
        'img' => '/dg_image/brand_image/ride_300.jpg',
        'bg' => '/dg_image/brand_image/ride_bg.png?v=1',
        'mobile_bg' => '/dg_image/brand_image/ride_bg_mobile.png',
        'w_logo' => '/dg_image/brand_image/ride_w_logo.png',
        'size' => 'mid',
        'cate_no' => '55',
		'introduce' => '2011년에 런칭한 비교적 신흥 메이커로 분류되는 브랜드로써 합리적인 가격과 신소재를 활용한 높은 품질의 제품을 
연이어 배출하여 꾸준히 사랑받고 있는 브랜드입니다. 
국내에서도 가성비가 좋다고 입소문이 나있으며 대표 상품인 버진루프 시리즈와 
다수의 히트작을 보유하고 있는 브랜드입니다.'
	],
    '59'=> [
        'name_ko' => '닛포리 기프트',
        'name_en' => 'N.P.G',
        'code' => 'NG',
        'img' => '/dg_image/brand_image/npg_300.jpg',
        'bg' => '/dg_image/brand_image/npg_bg.png?v=1',
        'mobile_bg' => '/dg_image/brand_image/npg_bg_mobile.png',
        'w_logo' => '/dg_image/brand_image/npg_w_logo.png?v=1',
        'size' => 'mid',
        'cate_no' => '59',
		'introduce' => '오랜 전통을 자랑하는 대형 성인 업체 브랜드입니다.
명기의 증명 시리즈 등과 같이 실제와 혼동할 수 있을 만큼의 정교한 제품을 중심적으로 출시하며 핸드형 뿐만 아닌 
중, 대형 홀까지 높은 퀄리티를 자랑하는 상품을 다수 생산하고 있습니다.'
	],
    '56'=> [
        'name_ko' => '매직아이즈',
        'name_en' => 'Magic eyes',
        'code' => 'ME',
        'img' => '/dg_image/brand_image/magic_300.jpg',
        'bg' => '/dg_image/brand_image/magic_bg.png',
        'mobile_bg' => '/dg_image/brand_image/magic_bg_mobile.png',
        'w_logo' => '/dg_image/brand_image/magic_w_logo.png?v=1',
        'size' => 'mid',
        'cate_no' => '56',
		'introduce' => '독창성 넘치는 상품과 실용성이 높은 오나홀을 출시하는 인기 브랜드입니다.
국내에서는 진실의 입, 스지망 쿠파 로린코 시리즈 등이 유명한 히트작으로 알려져 있습니다.
핸드홀 부터 대형홀 그리고 윤활젤 등 다른 소재를 융합하거나 유녀 조형을 장점으로 하는 기술력이 뛰어나기로 정평이 나 있습니다.'
	],
    '58'=> [
        'name_ko' => '텐가',
        'name_en' => 'TENGA',
        'code' => 'TG',
        'img' => '/dg_image/brand_image/tenga_300.jpg',
        'cate_no' => '58',
		'introduce' => '2005년 설립되어 압도적인 지명도로 오나홀의 대명사가 돼버린 브랜드 입니다.
국내에서도 플립홀,컵,에그,스피너 등등 대히트 제품을 출시하여 안전하고 기능적이면서 깔끔한 디자인으로 오나홀의 대중화에 큰 영향을 준 것은 틀림없습니다.
퀄리티 높은 디자인과 확실한 기능을 겸비한 제품을 보유하고 있는 브랜드입니다.'
	],
    '922'=> [
        'name_ko' => '이로하',
        'name_en' => 'iroha',
        'code' => 'TG-IH',
        'img' => '/dg_image/brand_image/iroha_300.jpg',
        'bg' => '/dg_image/brand_image/iroha_bg.png?v=1',
        'cate_no' => '922',
		'introduce' => '성인용품의 유명 브랜드 텐가에서 여성 개발진들이 여성만을 위한 상품을 출시하는 브랜드로 런칭하였습니다.
텐가는 2005년 설립되어 압도적인 지명도로 성인용품의 대명사가 돼버린 브랜드로써 퀄리티 높은 디자인과 확실한 기능을 겸비한 제품을 보유하고 있습니다.
이로하는 건강한 음식을 먹고 양질의 수면을 취하는 것처럼 여성 욕구를 셀프 플레져 아이템으로 구현 즐겁고, 안전한 셀프케어를 선사합니다.'
	],
    '64'=> [
        'name_ko' => '핫파워즈',
        'name_en' => 'Hot Powers',
        'code' => 'HP',
        'img' => '/dg_image/brand_image/hp_300.jpg',
        'bg' => '/dg_image/brand_image/hp_bg.png?v=1',
        'mobile_bg' => '/dg_image/brand_image/hp_bg_mobile.png',
        'w_logo' => '/dg_image/brand_image/hp_w_logo.png',
        'size' => 'mid',
        'cate_no' => '64',
		'introduce' => '오나홀에대한 도전정신과 장인 정신을 모두 보유한 브랜드로, 놀라운 창의력을 바탕으로 
컨셉트의 구현과 몰입감을 제공합니다. 오나홀 브랜드 중 가장 정밀하고 
다양한 경도 스펙트럼을 보유하고 있으며, 적재적소에 각 소재를 설계하는 능력이 뛰어납니다.'
	],
    '948'=> [
        'name_ko' => '쿨프',
        'name_en' => 'COOLP',
        'code' => 'CP',
        'img' => '/dg_image/brand_image/coolp_300.jpg',
        'size' => 'mid',
        'cate_no' => '948',
		'introduce' => '2021년 후반 새롭게 런칭하는 브랜드로써 핫파워즈에서 기획하고 타사 공장에서 OEM 제작하는 핫파워즈의 세컨 브랜드입니다.
기존의 핫파워즈만의 아이텐티티를 벗어나 다양한 제품을 선보일 것으로 기대되는 브랜드입니다.'
	],
    '401'=> [
        'name_ko' => '로마',
        'name_en' => 'Loma',
        'code' => 'LM',
        'img' => '/dg_image/brand_image/loma_300.jpg',
        'cate_no' => '401',
		'introduce' => ''
	],
    '1019'=> [
        'name_ko' => '러스트',
        'name_en' => 'LUST',
        'code' => 'KO-LT',
        'img' => '/dg_image/brand_image/lust_300.jpg?v=1',
        'bg' => '/dg_image/brand_image/lust_bg.png',
        'bg_color' => '#11423c',
        'mobile_bg' => '',
        'w_logo' => '/dg_image/brand_image/lust_w_logo.png',
        'size' => 'mid', 'info_class' => '',
        'cate_no' => '1019',
		'introduce' => '2년 동안 2천 번 이상 테스트를 하여 탄생한 제작자의 영혼을 갈아 넣은 상품 
이라는 타이틀로 2022년 신규 런칭한 국산 브랜드입니다.'
	],
    '73'=> [
        'name_ko' => '막코스재팬',
        'name_en' => 'MACCOS JAPAN',
        'code' => 'MC',
        'img' => '/dg_image/brand_image/maccos_300.jpg',
        'bg' => '/dg_image/brand_image/maccos_bg.png',
        'mobile_bg' => '/dg_image/brand_image/maccos_bg_mobile.png',
        'w_logo' => '/dg_image/brand_image/maccos_w_logo.png',
        'size' => 'mid',
        'cate_no' => '73',
		'introduce' => '2017년 여름 런칭한 일본의 종합 성인 용품 브랜드입니다. 
막코스재팬의 maccos는 Max Cost Performance의 약어로 최대의 가성비라는 뜻입니다. 
강력한 가성비를 중심으로, 과하지 않은 세련된 패키지 디자인을 선보입니다.'
	],
    '537'=> [
        'name_ko' => '지 프로젝트',
        'name_en' => 'G PROJECT',
        'code' => 'GP',
        'img' => '/dg_image/brand_image/gproject_300.jpg',
        'bg' => '/dg_image/brand_image/gproject_bg.png?v=1',
        'bg_color' => '#fad8cf',
        'mobile_bg' => '/dg_image/brand_image/gproject_bg_mobile.png',
        'w_logo' => '/dg_image/brand_image/gproject_w_logo.png',
        'size' => 'mid', 'info_class' => 'black',
        'cate_no' => '537',
		'introduce' => '완성도 높은 퀄리티와 내구성으로 제작하여 어덜트 굿즈를 안심·안전하게
제대로 사용할 수 있는 제품을 만들자라는 기업 목표입니다.
깔끔하고 귀여운 패키지 디자인을 선보이며,
복잡하지 않고 단순하면서도 강력한 사용감을 제공하는 브랜드입니다.'
	],
    '538'=> [
        'name_ko' => 'P P P',
        'name_en' => 'PxPxP',
        'code' => 'PX',
        'img' => '/dg_image/brand_image/ppp_300.jpg',
        'bg' => '/dg_image/brand_image/ppp_bg.png',
        'size' => 'mid',
        'cate_no' => '538',
		'introduce' => '과하지 않으면서 세련된, 하나의 완성품이라는 이미지를 주는 브랜드입니다.
에로틱을 진지하게 생각하며 에로틱의 정의 라는 타이틀로 어덜트굿즈를 출시중인 브랜드 입니다.
대마인 시리즈와, 푸닛토 딜로 시리즈로 유명합니다.'
	],
    '539'=> [
        'name_ko' => '에그제',
        'name_en' => 'eXe',
        'code' => 'EX',
        'img' => '/dg_image/brand_image/exe_300.jpg',
        'cate_no' => '539',
		'introduce' => 'AV 여배우와의 리얼계 콜라보는 물론, 막강한 \'푸니아나\'IP를 통해 다양한 오나홀을 전개하고 있습니다.
핸디형으로는 실험적인 시도를, 대형은 강력한 완성도를 자랑합니다.'
	],
    '75'=> [
        'name_ko' => '옐로랩',
        'name_en' => 'YELOLAB',
        'code' => 'YL',
        'img' => '/dg_image/brand_image/yelolab_300.jpg',
        'cate_no' => '75',
		'introduce' => '다양한 협업은 물론 고 볼륨 홀의 소화도 무리없이 해내는, 빠른 성장이 기대되는 브랜드입니다.'
	],
    '63'=> [
        'name_ko' => '키테루키테루',
        'name_en' => 'kiterukiteru',
        'code' => 'KK',
        'img' => '/dg_image/brand_image/kiteru_300.jpg',
        'cate_no' => '63',
		'introduce' => '인간이 아닌 모에화된 마물을 컨셉트로, 현실엔 존재하지 않는 다양한 판타지 오나홀을 전개하는 브랜드입니다.
그 중 촉수를 중심으로 내부구조에 대한 변화를 선도하고 있습니다.
두 가지 IP를 집중적으로 전개하고 있습니다.'
	],
    '61'=> [
        'name_ko' => '에이원',
        'name_en' => 'a-one',
        'code' => 'AO',
        'img' => '/dg_image/brand_image/aone_300.jpg',
        'cate_no' => '61',
		'introduce' => '많은 인기를 얻은 에어돌 러브바디 시리즈로 유명한 브랜드 입니다.
계속하여 참신한 상품을 출시하려고 노력하고 있으며 폭 넓은 제품을 취급하고 있는 제조사 입니다.'
	],
    '69'=> [
        'name_ko' => '필웍스',
        'name_en' => 'fillworks',
        'code' => 'FW',
        'img' => '/dg_image/brand_image/fillworks_300.jpg',
        'cate_no' => '69',
		'introduce' => '높은 가성비와 부담스럽지 않은 볼륨을 가진 핸디형 오나홀을 전개하는 브랜드입니다.'
	],
    '76'=> [
        'name_ko' => '메이트',
        'name_en' => 'MATE',
        'code' => 'MT',
        'img' => '/dg_image/brand_image/mate_300.jpg',
        'cate_no' => '76',
		'introduce' => '\'오나펫 소재\'라고 불릴정도로 놀라운 소재를 개발한 브랜드입니다.
핸디형 오나홀에 특화되어있으며, 연속 CQ구조의 전문가라고 해도 과언이 아닐 정도로 관통감을 중시하는 브랜드입니다.'
	],
    '71'=> [
        'name_ko' => '키스 미 러브',
        'name_en' => 'KISS-ME-LOVE',
        'code' => 'KI',
        'img' => '/dg_image/brand_image/kiss_300.jpg',
        'cate_no' => '71',
		'introduce' => '입문하기 좋은 스탠다드 홀을 중심으로 전개하고 있는 브랜드입니다.'
	],
    '67'=> [
        'name_ko' => '엔조이 토이즈',
        'name_en' => 'ENJOY TOYS',
        'code' => 'EJ',
        'img' => '/dg_image/brand_image/enjoy_300.jpg',
        'cate_no' => '67',
		'introduce' => '2011년 일본에서 설립하여 현제부터 미래의 요구에 대하여 기술력과 상상력을 가지고 항상 진화해 간다는 콘셉트인 브랜드 입니다.
부담없이 가볍게 즐길 수 있는 스고망 라인은 물론, AV 여배우 콜라보를 통한 하이엔드 페라홀과 고퀄리티 오나홀을 전개하고 있는 브랜드입니다.'
	],
    '74'=> [
        'name_ko' => '피치 토이즈',
        'name_en' => 'PEACH TOYS',
        'code' => 'PT',
        'img' => '/dg_image/brand_image/peach_260.jpg',
        'cate_no' => '74',
		'introduce' => ''
	],
    '81'=> [
        'name_ko' => '이케부쿠로 토이즈',
        'name_en' => 'IKEBUKURO TOYS',
        'code' => 'ET',
        'img' => '/dg_image/brand_image/ikebukuro_300.jpg',
        'cate_no' => '81',
		'introduce' => ''
	],
    '150'=> [
        'name_ko' => '오나간',
        'name_en' => 'おながん',
        'code' => 'OG',
        'img' => '/dg_image/brand_image/onagan_300.jpg',
        'cate_no' => '150',
		'introduce' => ''
	],
    '845'=> [
        'name_ko' => '세츠겐노 울프 완구',
        'name_en' => '雪原のWOLF玩具',
        'code' => 'SW',
        'img' => '/dg_image/brand_image/s_wolf_300.jpg',
        'cate_no' => '845',
		'introduce' => '설원의 늑대 완구'
	],
    '79'=> [
        'name_ko' => '온도',
        'name_en' => 'ONDO!',
        'code' => 'OD',
        'img' => '/dg_image/brand_image/ondo_300.jpg',
        'cate_no' => '79',
		'introduce' => ''
	],
	'65'=> [
        'name_ko' => '러브팩터',
        'name_en' => 'LOVE FACTOR',
        'code' => 'LF',
        'img' => '/dg_image/brand_image/love_factor.jpg',
        'cate_no' => '65',
		'introduce' => '일본의 종합 어덜트굿즈 브랜드입니다. 두근두근 두근두근 하는 상품을 만드는 것이 브랜드의 목표입니다.'
	],
	'437'=> [
        'name_ko' => '레텐',
        'name_en' => 'Leten',
        'code' => 'LT',
        'img' => '/dg_image/brand_image/leten_300.jpg',
        'cate_no' => '437',
		'introduce' => ''
	],
	'60'=> [
        'name_ko' => '렌즈',
        'name_en' => 'Rends',
        'code' => 'RS',
        'img' => '/dg_image/brand_image/rends_300.jpg',
        'cate_no' => '60',
		'introduce' => '2008년 설립후 소매점으로 출발 소비자의 니즈를 파악하여 일본 내에서 기획·개발된 고품질의 제품을 출시하는것이 목표인 브랜드 입니다.
Reality · Extravagant · Neat · Dream · Satisfaction 가 브랜드 사명이며 앞글자를 따서 RENDS라는 회사를 설립 동명의 브랜드를 출시하였습니다.
'
	],
	'838'=> [
        'name_ko' => '보르제',
        'name_en' => 'VORZE',
        'code' => 'VR',
        'img' => '/dg_image/brand_image/vorze_300.jpg',
        'cate_no' => '838',
		'introduce' => '렌즈의 프로젝트 팀이 만든 프리미엄 브랜드입니다.
성인용품을 일반 가전제품과 동등한 퀄리티로 제작한다 라는 컨셉으로 \'성 가전\'이라는 장르를 구축하는데 성공했습니다.'
	],
	'543'=> [
        'name_ko' => '판타스틱 베이비',
        'name_en' => 'FANTASTIC BABY',
        'code' => 'RS',
        'img' => '/dg_image/brand_image/fantastic2_300.jpg',
        'mobile_bg' => '/dg_image/brand_image/fantastic2_bg_mobile.png',
        'w_logo' => '/dg_image/brand_image/fantastic2_w_logo.png',
        'size' => 'mid',
        'cate_no' => '543',
		'introduce' => '일본의 자사 공장을 보유하고 있는 몇 안 되는 브랜드입니다. 
2020년 「토이즈 크리에이트」로 브랜드명을 잠깐 변경하였지만 다시 「판타스틱 베이비」라는 브랜드명으로 돌아왔습니다. 
현재는 토이즈 크리에이트와 독립된 회사로서 각각의 브랜드입니다.'
	],
	'70'=> [
        'name_ko' => '토이즈 크리에이트',
        'name_en' => 'Toy\'s create',
        'code' => 'RS',
        'img' => '/dg_image/brand_image/toyscreate_300.jpg',
        'size' => 'mid',
        'cate_no' => '70',
		'introduce' => '일본의 성인용품 브랜드입니다. 
「판타스틱 베이비」의 OEM 브랜드로 알려졌으나 현재는 독립하여 개별 브랜드로 상품을 출시하고 있습니다.'
	],
	'532'=> [
        'name_ko' => 'SSI 재팬',
        'name_en' => 'SSI JAPAN',
        'code' => 'SS',
        'img' => '/dg_image/brand_image/ssi_300.jpg',
        'cate_no' => '532',
		'introduce' => ''
	],
	'78'=> [
        'name_ko' => '케이엠 프로듀스',
        'name_en' => 'K.M.Produce',
        'code' => 'KM',
        'img' => '/dg_image/brand_image/kmp_300.jpg',
        'cate_no' => '78',
		'introduce' => '도쿄 시부야 에 위치한 일본의 유명 성인 비디오 (AV) 회사 입니다.'
	],
	'531'=> [
        'name_ko' => '텝펜',
        'name_en' => 'TEPPEN',
        'code' => 'TP',
        'img' => '/dg_image/brand_image/teppen_300.jpg',
        'cate_no' => '531',
		'introduce' => ''
	],
	'867'=> [
        'name_ko' => '루네 기프트',
        'name_en' => 'Lune Gift',
        'code' => 'TP',
        'img' => '/dg_image/brand_image/lune_300.jpg',
        'cate_no' => '867',
		'introduce' => '유한회사 마리골드의 에로게임 브랜드 루네소프트 인기 캐릭터를 콜라보 및 생산한 제품입니다.'
	],
	'77'=> [
        'name_ko' => '리그레 재팬',
        'name_en' => 'Ligre japan',
        'code' => 'LJ',
        'img' => '/dg_image/brand_image/ligre_300.jpg',
        'bg' => '/dg_image/brand_image/ligre_bg.png?v=1',
        'size' => 'mid',
        'cate_no' => '77',
		'introduce' => '일본의 어덜트굿즈 브랜드입니다. 연령이나 성별에 사로잡히지 않고, 성을 있는 그대로 즐기는 것을
 리그레 재팬은 응원합니다. 생활 속에서 \'즐거움\'은 필수 요소이고 성은 즐거움입니다. 
즐거움 중에 성이 있습니다. 그런 즐거움을 성인용품이라는 카테고리 안에서 만들어 내어
누구나 위화감 없이 성을 즐기는 미래를 창조해 갑니다. '
	],
	'952'=> [
        'name_ko' => '모플 토이즈',
        'name_en' => 'MOPLE TOYS',
        'code' => 'MP',
        'img' => '/dg_image/brand_image/mople_300.jpg',
        'cate_no' => '952',
		'introduce' => '2021년 4월 신규 런칭한 일본 오나홀 브랜드 입니다.'
	],
	'548'=> [
        'name_ko' => '무소우 토이즈',
        'name_en' => 'MOUSOU-TOYS',
        'code' => 'MS',
        'img' => '/dg_image/brand_image/mousou_300.jpg',
        'cate_no' => '548',
		'introduce' => '2020년  신규 런칭한 일본 오나홀 브랜드 입니다.'
	],
	'149'=> [
        'name_ko' => '로스쿠르',
        'name_en' => 'LoScul',
        'code' => 'MS',
        'img' => '/dg_image/brand_image/loscul_300.jpg',
        'cate_no' => '149',
		'introduce' => ''
	],
	'80'=> [
        'name_ko' => '후지 월드공예',
        'name_en' => 'Fuji world',
        'code' => 'FJ',
        'img' => '/dg_image/brand_image/fujiworld_300.jpg',
        'cate_no' => '80',
		'introduce' => ''
	],

	'779'=> [
        'name_ko' => '나카지마 화학',
        'name_en' => 'PEPEE by Nakajima Chemicals',
        'code' => 'MS',
        'img' => '/dg_image/brand_image/nakajima_300.jpg',
        'cate_no' => '779',
		'introduce' => '1947년 설립된 나카지마 화확사는 사용자가 안심하고 신뢰할 수 있는 인체에 무해하고도 고성능을 유지할 수 있는 환경친화적인 상품을 만들겠다는
목표로 1994년 부터 자체 브랜드 페페를 개발하여 런칭하였습니다. 국내에서는 페페젤로 유명한 브랜드 입니다.'
	],
	'438'=> [
        'name_ko' => '이지 러브',
        'name_en' => 'Easy Love',
        'code' => 'EL',
        'img' => '/dg_image/brand_image/easylove_300.jpg',
        'cate_no' => '438',
		'introduce' => ''
	],
	'894'=> [
        'name_ko' => '드라이웰',
        'name_en' => 'DRYWELL',
        'code' => 'KR-DW',
        'img' => '/dg_image/brand_image/drywell_300.jpg',
        'bg' => '/dg_image/brand_image/drywell_bg.png',
        'cate_no' => '894',
		'introduce' => '1987년부터 일본 시부야에서 성인 용품 상점으로 시작하여 수십 년 동안 발전하면서 일본의 성인 산업에서 Sexual Wellness의 주요 브랜드가 되었습니다.
2014년 이후 시계 시장에서 돋보이는 활동을 하고 있습니다.'
	],
	'783'=> [
        'name_ko' => '지니',
        'name_en' => 'ZINI',
        'code' => 'KR-ZN',
        'img' => '/dg_image/brand_image/zini_300.jpg',
        'cate_no' => '783',
		'introduce' => '지니는 한국 섹스토이의 발전을 느낄 수 있는 대표적 브랜드입니다.
국내 성인용품업체 ㈜부르르에서 론칭한 이래 다양한 성인 기구를 출시했습니다. 특히 세련된 디자인의 남성 자위 용품, 여성 자위 용품 등은 큰 호응을 받고 있습니다.'
	],
	'890'=> [
        'name_ko' => '네이키드 팩토리',
        'name_en' => 'NAKED FACTORY',
        'code' => 'KR-NF',
        'img' => '/dg_image/brand_image/naked_300.jpg',
        'cate_no' => '783',
		'introduce' => '2017년 2월 한국 부산에서 설립된 브랜드 입니다.
리얼한 소재로 제작된 토르소 전문 대한민국 프리미엄 제조사라는 타이틀과 \'양 보다 질\'이라는 철학으로 고품질 제품을 선보이고 있습니다.'
	],
	'846'=> [
        'name_ko' => '러브돌',
        'name_en' => 'LoveDoll',
        'code' => 'KR-LD',
        'img' => '/dg_image/brand_image/lovedoll_300.jpg',
        'cate_no' => '846',
		'introduce' => '한국 성원 프렌차이즈에서 런칭한 브랜드 입니다. 활발하게 직접생산 또는 OEM 방식의 다양한 성인 기구를 출시했습니다.
Love doll은 남성 성적 표현에서 상대 또는 대상에 대한 희망을 주로 인형(doll)으로 표현하는데 착안하여 용품을 생산/공급하는데 있어서 정성과 사랑을 담겠다는 신념으로 탄생하였습니다.'
	],
	'851'=> [
        'name_ko' => '시즈마',
        'name_en' => 'SIZMA',
        'code' => 'KR-SZ',
        'img' => '/dg_image/brand_image/sizma_300.jpg',
        'cate_no' => '851',
		'introduce' => '2008년 론칭한 이후 Sizma(시즈마)는 섹시한 스타일로 2030 여성들의 섹시 라이프 스타일을 대표하는 브랜드입니다.
최근에는 에세머를 위한 에스엠용품도 전문적으로 자체 생산하여 출시되고 있어, 트랜드를 반영하는 리더 브랜드로서 다양한 스타일 제품을 합리적인 가격에 제공하는 브랜드 Sizma(시즈마) 입니다.'
	],
	'853'=> [
        'name_ko' => '센스토이',
        'name_en' => 'SENSTOY',
        'code' => 'KR-ST',
        'img' => '/dg_image/brand_image/senstoy_300.jpg',
        'cate_no' => '853',
		'introduce' => ''
	],
	'936'=> [
        'name_ko' => '센스바디',
        'name_en' => 'SENSBODY',
        'code' => 'KR-SB',
        'img' => '/dg_image/brand_image/sensbody_300.jpg',
        'bg' => '/dg_image/brand_image/sensbody_bg.png',
        'cate_no' => '936',
		'introduce' => '넥서스메디케어 주식회사에서 2020년 신규런칭한 브랜드 입니다.
꿈꿔오던 판타지 대상과의 콜라보로 실제로 내부까지 본을 떠 내부 주름 깊은 곳 까지 완벽히 재현해 포용적이고 편안하게 즐길 수 있는 오나홀 제품을 출시했습니다.
인위적인 자극을 주기보다 리얼주름, 리얼돌기로 실제와 같은 자극으로 더 많은 성적 만족감을 추구하고 있습니다. '
	],
	'943'=> [
        'name_ko' => '하이쓰',
        'name_en' => 'HEISS',
        'code' => 'KR-HE',
        'img' => '/dg_image/brand_image/heiss_300.jpg',
        'cate_no' => '943',
		'introduce' => '넥서스메디케어 주식회사에서 런칭한 국산 오나홀 전문 브랜드 입니다.'
	],
	'938'=> [
        'name_ko' => '나비',
        'name_en' => 'nabi',
        'code' => 'KR-NB',
        'img' => '/dg_image/brand_image/nabi_300.jpg',
        'cate_no' => '938',
		'introduce' => ''
	],
	'833'=> [
        'name_ko' => '솔로즈',
        'name_en' => 'solos',
        'code' => 'KR-SL',
        'img' => '/dg_image/brand_image/solos_300.jpg',
        'cate_no' => '833',
		'introduce' => ''
	],
	'873'=> [
        'name_ko' => '도라토이',
        'name_en' => 'DORATOY',
        'code' => 'KR-DR',
        'img' => '/dg_image/brand_image/doratoy_300.jpg',
        'cate_no' => '873',
		'introduce' => '2017년 론칭한 국산 브랜드 입니다. 국내생산품인 중형 엉덩이형 오피스걸 시리즈가 대표상품 입니다.'
	],
	'951'=> [
        'name_ko' => '칠색향',
        'name_en' => '七色香',
        'code' => 'KR-7C',
        'img' => '/dg_image/brand_image/7color_300.jpg',
        'cate_no' => '951',
		'introduce' => ''
	],

	'861'=> [
        'name_ko' => '에로카이',
        'name_en' => 'EROKAY',
        'code' => 'KR-EK',
        'img' => '/dg_image/brand_image/erokay_300.jpg',
        'cate_no' => '861',
		'introduce' => ''
	],
	'863'=> [
        'name_ko' => '에프스틸',
        'name_en' => 'FSTEEL',
        'code' => 'KR-FS',
        'img' => '/dg_image/brand_image/fsteel_300.jpg',
        'cate_no' => '863',
		'introduce' => ''
	],
	'866'=> [
        'name_ko' => '프리티 러브',
        'name_en' => 'PRETTY LOVE',
        'code' => 'KR-PL',
        'img' => '/dg_image/brand_image/prettylove_300.jpg?v=1',
        'cate_no' => '866',
		'introduce' => '1998년의 중국 성생활건강 전문 위원회 활동을 시작으로,  2011년 독일에서 \'프리티 러브\' 브랜드를 출범시켰습니다.
레드닷 어워드 다중 수상의 깔끔한 디자인을 필두로, 상하이의 성 박람회는 물론 다양한 브랜드 전개를 이어가고 있습니다.'
	],
	'896'=> [
        'name_ko' => '바일러',
        'name_en' => 'BAILE',
        'code' => 'KR-BA',
        'img' => '/dg_image/brand_image/baile_300.jpg',
        'cate_no' => '896',
		'introduce' => '1993년에 설립된 BAILE는 중국에 제조 및 생산 시스템을 갖추고 미국과 유럽 및 중국에 많은 특허를 포함하여 여러 중요한 인증을 획득하였습니다.
회사 이름을 그대로 사용한 바일러 외 프리티러브 외 크레이지불, Mr·play 등등의 브랜드 런칭하였습니다.'
	],
	'923'=> [
        'name_ko' => '크레이지 불',
        'name_en' => 'CRAZY BULL',
        'code' => 'KR-BA',
        'img' => '/dg_image/brand_image/crazybull_300.jpg',
        'cate_no' => '923',
		'introduce' => '중국의 대형 성인용품 제조회사 바일러(BAILE)에서 남성만을 위한 상품을 출시하는 브랜드로 런칭하였습니다.
1993년에 설립된 BAILE는 중국에 제조 및 생산 시스템을 갖추고 미국과 유럽 및 중국에 많은 특허를 포함하여 여러 중요한 인증을 획득하였습니다.
윤활제 없이 물로만 사용가능한 워터스킨 제품으로 유명한 브랜드 입니다.'
	],
	'918'=> [
        'name_ko' => '락오프',
        'name_en' => 'Rocks-Off',
        'code' => 'KR-RF',
        'img' => '/dg_image/brand_image/rockoff_300.jpg?v=1',
        'bg' => '/dg_image/brand_image/rockoff_bg.png',
        'cate_no' => '918',
		'introduce' => '영국에서 2003년 설립된 락오프는 혁신과 품질로 세계적인 명성을 갖는 섹스토이 브랜드 입니다.
성인용품 관련 30여개 이상 수상 이력, 영국 섹슈얼 제품의 선도업체이자 성인용품 상위권 랭크 브랜드 입니다.
스타일과 기능의 조화를 이루어 혁신적인 하이엔드 제품을 출시하고 있습니다.'
	],
	'921'=> [
        'name_ko' => '다이베',
        'name_en' => 'dibe',
        'code' => 'KR-DI',
        'img' => '/dg_image/brand_image/dibe_300.jpg',
        'cate_no' => '921',
		'introduce' => '2012년 설립된 중국의 다이베 전자 기술에서 런칭한 성인용품 전문 브랜드입니다.
미국, 호주, 영국, 독일 등에서 많은 인기를 얻고 있으며 섹스토이 같지 않은 귀여운 디자인이 돋보입니다.'
	],
	'885'=> [
        'name_ko' => '가라쿠',
        'name_en' => 'GALAKU',
        'code' => 'KR-GA',
        'img' => '/dg_image/brand_image/galaku_300.jpg',
        'cate_no' => '885',
		'introduce' => ''
	],
	'874'=> [
        'name_ko' => '에스핸드',
        'name_en' => 'S-HANDE',
        'code' => 'KR-SH',
        'img' => '/dg_image/brand_image/s_hande_300.jpg',
        'cate_no' => '874',
		'introduce' => '중국에 본사를 두고 중국 OEM 방식으로 생산하여 유럽 및 세계적으로 유통하고 있는 성인용품 브랜드 입니다.
독일 브랜드로 디자인 등록되었으며 CE 인증,Ros 인증, FDA 승인, SGS 인증 완료'
	],
	'1053'=> [
        'name_ko' => '오 마이 스카이',
        'name_en' => 'OMYSKY',
        'code' => 'KR-OT',
        'img' => '/dg_image/brand_image/omysky_300.jpg',
        'cate_no' => '1053',
		'introduce' => ''
	],
	'884'=> [
        'name_ko' => '오터치',
        'name_en' => 'OTOUCH',
        'code' => 'KR-OT',
        'img' => '/dg_image/brand_image/otouch_300.jpg',
        'cate_no' => '884',
		'introduce' => ''
	],
    '82'=> [
        'name_ko' => '맨즈맥스',
        'name_en' => 'Men\'s max',
        'code' => 'MM',
        'img' => '/dg_image/brand_image/mensmax_300.jpg',
        'cate_no' => '82',
		'introduce' => '일본의 엔조이 토이즈(ENJOY TOYS)에서 런칭한 남성 성인용품 전문 브랜드 입니다.'
	],
    '899'=> [
        'name_ko' => '유니더스',
        'name_en' => 'unidus',
        'code' => 'KR-UD',
        'img' => '/dg_image/brand_image/unidus_300.jpg',
        'cate_no' => '899',
		'introduce' => '(바이오제네틱스) 유니더스는 라텍스 고무 제품을 전문적으로 생산하는 코스닥 상장 기업입니다.
주요 제품으로는 콘돔, 지샥크, 장갑 세가지가 있습니다.'
	],
    '842'=> [
        'name_ko' => '프라임',
        'name_en' => 'Prime',
        'code' => 'PR',
        'img' => '/dg_image/brand_image/prime_300.jpg',
        'cate_no' => '842',
		'introduce' => '프라임은 2017년 런칭한 일본의 성인용품 브랜드입니다.
최고의 재미! 궁극의 쾌락! 압도적인 재미를 추구하며 나이트 라이프를 즐겁게 만들자가 모토로 제품을 제작하고 있습니다.'
	],
    '942'=> [
        'name_ko' => '와일드 원',
        'name_en' => 'Wild One',
        'code' => 'WO',
        'img' => '/dg_image/brand_image/wildone_300.jpg',
        'cate_no' => '942',
		'introduce' => '1991년 설립하여 일본에 시부야에 본점을 두고  신주쿠, 우에노, 신바시 등등의 오프라인 매장 브랜드입니다.
제조사 SSI JAPAN과 같은 그룹 계열회사입니다.
제조사 SSI JAPAN와 합작여 동명 브랜드 제품을 출시하고 있습니다.'
	],
    '68'=> [
        'name_ko' => '솔브멘',
        'name_en' => 'solvemen',
        'code' => 'SM',
        'img' => '/dg_image/brand_image/solvemen_300.jpg',
        'cate_no' => '68',
		'introduce' => '2020년 신규 런칭한 일본의 성인용품 브랜드입니다. 
핸드형 오나홀을 중점적으로 선보이고 있으며 야한 일러스트나 사진으로 패키징을 하는 그동안에 다른 제품들과 차별화된 아이덴티티를 고집하고 있습니다.'
	],
    '997'=> [
        'name_ko' => '아크웨이브',
        'name_en' => 'ARCWAVE',
        'code' => 'AW',
        'img' => '/dg_image/brand_image/arcwave_300.jpg',
        'bg' => '/dg_image/brand_image/arcwave_bg.png',
        'size' => 'mid',
        'cate_no' => '997',
		'introduce' => '여성용 전동 성인용품으로 유명한 우머나이저(Womanizer)와 위-바이브(We-Vibe) 브랜드를 소유한
글로벌 섹슈얼 웰니스 그룹 와우테크(Wowtech)가 남성용 브랜드 아크웨이브(Arcwave) 출시
아크웨이브는 자신의 즐거움을 새롭게 정의 내리고
적극적으로 추구하는 현대적이고 미래지향적인 남성을 목표로 하는 브랜드다.'
	],
    '1069'=> [
        'name_ko' => '데몬킹',
        'name_en' => 'Demon King => 大魔王',
        'code' => 'TM',
        'img' => '/dg_image/brand_image/demonking_300.jpg',
        'size' => 'mid',
        'cate_no' => '1069',
		'introduce' => '大魔王 ACHATUS로 런칭을한 중국의 성인용품 브랜드입니다. 일본의 동명 브랜드 대마왕과 동일한 이름입니다. 
일본 대마왕의 제품 디자이너가 참여한 제품도 있어 일본 브랜드 대마왕의 영향이 있는 것으로 예상되며 
현재는 독립적인 브랜드로서 우수한 제품을 계속 출시하여 라인업을 구축하고 있습니다.'
	],
    '1075'=> [
        'name_ko' => '유이라',
        'name_en' => 'YUIRA',
        'code' => 'TR',
        'img' => '/dg_image/brand_image/yuira_300.jpg',
        'size' => 'mid',
        'cate_no' => '1075',
		'introduce' => '일본의 유통회사 톱 마샬( Top Marshal )에서 런칭한 성인 용품 브랜드입니다. 
2021년 4월 1일부터 전신인 SMIRAL로부터 성인 용품 제조·판매업 분할 승계되었습니다. 
I lead you to the Paradise (나는 당신을 파라다이스로 인도한다)라는 슬로건으로 앞세워  
주력이 였던 YUIRA 컵 홀 시리즈에서 대형 및 가슴 여성 제품까지 2021년부터 독립된 브랜드로서 더 활발한 신상품을 출시할 것이라 기대합니다.'
	],
    '1078'=> [
        'name_ko' => '핸디',
        'name_en' => 'Handy',
        'code' => 'TR',
        'img' => '/dg_image/brand_image/handy_300.jpg',
        'size' => 'mid',
        'cate_no' => '1078',
		'introduce' => '노르웨이의 스윗테크(Sweet Tech)에서 제작한 핸디(Handy)는 남성의 쾌락을 위해 설계된 자동 스트로입니다. 
최고의 경험을 추구하기 위해 최고의 기술력 디테일로 제작되었습니다.'
	],
    '1087'=> [
        'name_ko' => '프로페설 제이슨 C',
        'name_en' => 'PROF.JASON C',
        'code' => 'TR',
        'img' => '/dg_image/brand_image/profjasonc_300.jpg',
        'size' => 'mid',
        'cate_no' => '1087',
		'introduce' => '2005년 설립된 홍콩 Chisa-novelties의 성인용품 브랜드입니다.'
	],
    '1097'=> [
        'name_ko' => '새티스파이어',
        'name_en' => 'Satisfyer',
        'code' => 'ST',
        'img' => '/dg_image/brand_image/satisfyer_300.jpg',
        'size' => 'mid',
        'cate_no' => '1097',
		'introduce' => '2016년 설립하여 독일 빌레펠트에 본사를 둔 Triple A Internetshops GmbH에 속한 브랜드로 성, 건강 제품 및 장치를 제공합니다.
제품의 특징별로 60여 개의 일러스트로 표현된 패키지가 인상적입니다.
비교적 가격경쟁력이 있어 가성비 높은 제품들이 많습니다.'
	],
    '1108'=> [
        'name_ko' => '노토와',
        'name_en' => 'NOTOWA',
        'code' => 'NO',
        'img' => '/dg_image/brand_image/notowa_300.jpg',
        'size' => 'mid',
        'cate_no' => '1108',
		'introduce' => '2022년 신규 런칭한 일본의 오나홀 전문 브랜드입니다. 
자체 개발한 신소재 인공 피부 소재 no.18 스킨은 공업용 오일을 전혀 사용하지 않고 오로지 식품성 오일만을 사용하여 
 피부에 직접 닿는 제품이기 때문에 식품에 사용할 수 있는 규격만을 엄선해 안심 유의해 상품을 개발하고 있습니다.'
	],
    '1100'=> [
        'name_ko' => '메르시',
        'name_en' => 'merci',
        'code' => 'NO',
        'img' => '/dg_image/brand_image/merci_300.jpg',
        'size' => 'mid',
        'cate_no' => '1100',
		'introduce' => '일본의 성인용품 유통회사인 프레시어스(PRECIOUS)의 오리지널 브랜드입니다.'
	]
];



	foreach ( $phpArray as $key => $brand ){

		// 2차 배열 값을 변수로 할당
		$nameKo = $brand['name_ko'] ?? "";
		$nameEn = $brand['name_en'] ?? "";
		$code = $brand['code'] ?? "";
		$img = $brand['img'] ?? "";
		$bg = $brand['bg'] ?? "";
		$bg_color = $brand['bg_color'] ?? "";
		$info_class = $brand['info_class'] ?? "";
		$mobile_bg = $brand['mobile_bg'] ?? "";
		$mobileBg = $brand['mobile_bg'] ?? "";
		$wLogo = $brand['w_logo'] ?? "";
		$size = $brand['size'] ?? "";
		$cateNo = $brand['cate_no'] ?? "";
		$introduce = $brand['introduce'] ?? "";

		$_bd_api_info_ary = [
			'active' => 'Y',
			'name' => addslashes($nameKo),
			'name_en' => addslashes($nameEn),
			'logo' => $img,
			'logo_mobile' => $wLogo,
			'bg' => $bg,
			'bg_rgb' => $bg_color,
			'info_class' => $bg_color,
			'bg_mobile' => $mobile_bg
		];

		$_bd_api_info = json_encode($_bd_api_info_ary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	
			$updateData = [
				'bd_api_info' => $_bd_api_info,
				'bd_api_introduce' => $introduce
			];

			$this->queryBuilder
				->table('BRAND_DB')
				->update($updateData, ['bd_cate_no' => $cateNo]);


	}










		$parentId = '69';
		$parentPath = '%/' . $parentId . '/%';

		$results = $this->queryBuilder
			->table('category')
		->where('group_code', '=', 'TaskGroup')
		->where('node_path', 'LIKE', $parentPath)
		->orderBy('node_path')
		->orderBy('sort_order')
		->get()
		->toArray();

	$tree =  $this->buildTree($results, $parentId);

		$data = $tree;

		return $data;

	}

	//재귀 함수로 트리 출력
	private function buildTree($nodes, $parentId = 1) {
		$tree = [];

		foreach ($nodes as $node) {
			if ($node['parent'] == $parentId) {
				$node['children'] = $this->buildTree($nodes, $node['idx']);
				$tree[] = $node;
			}
		}

		return $tree;
	}


}