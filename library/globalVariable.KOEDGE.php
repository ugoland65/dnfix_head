<?
$yen = 1000; //환율
//$kg_p = 6000; //1kg당 배송비
$kg_p = 6000; //1kg당 배송비
//$yen_cn = 194; //중국 환율
$yen_cn = 193; //중국 환율
$delivery_p_cn = 2800; //중국 개당배송비

//상품코드 한글화
$koedge_prd_kind_name['ONAHOLE'] = "오나홀";
$koedge_prd_kind_name['BREAST'] = "가슴장난감";
$koedge_prd_kind_name['DILDO'] = "딜도";
$koedge_prd_kind_name['REALDOLL'] = "리얼돌";
$koedge_prd_kind_name['MAN'] = "남성용품";
$koedge_prd_kind_name['WOMAN'] = "여성용품";
$koedge_prd_kind_name['SIDE'] = "보조용품";
$koedge_prd_kind_name['GEL'] = "윤활젤";
$koedge_prd_kind_name['CONDOM'] = "콘돔";
$koedge_prd_kind_name['ANAL'] = "애널";
$koedge_prd_kind_name['NIPPLE'] = "니플(유두)";
$koedge_prd_kind_name['PERFUME'] = "향수";
$koedge_prd_kind_name['PILLOW'] = "필로우";
$koedge_prd_kind_name['AIRDOLL'] = "에어돌";
$koedge_prd_kind_name['UNDERWEAR'] = "속옷";
$koedge_prd_kind_name['COSTUME'] = "코스튬";
$koedge_prd_kind_name['SET'] = "세트상품";
$koedge_prd_kind_name['ONLYORDER'] = "주문전용상품";

//상품코드
$koedge_prd_kind_array = [
	["code"=>"ONAHOLE", "name"=>"오나홀"],
	["code"=>"BREAST", "name"=>"가슴장난감"],
	["code"=>"DILDO", "name"=>"딜도"],
	["code"=>"REALDOLL", "name"=>"리얼돌"],
	["code"=>"MAN", "name"=>"남성용품"],
	["code"=>"WOMAN", "name"=>"여성용품"],
	["code"=>"SIDE", "name"=>"보조용품"],
	["code"=>"GEL", "name"=>"윤활젤"],
	["code"=>"CONDOM", "name"=>"콘돔"],
	["code"=>"ANAL", "name"=>"애널"],
	["code"=>"NIPPLE", "name"=>"니플(유두)"],
	["code"=>"PERFUME", "name"=>"향수"],
	["code"=>"PILLOW", "name"=>"필로우"],
	["code"=>"AIRDOLL", "name"=>"에어돌"],
	["code"=>"UNDERWEAR", "name"=>"속옷"],
	["code"=>"COSTUME", "name"=>"코스튬"],
	["code"=>"SET", "name"=>"세트상품"],
	["code"=>"ONLYORDER", "name"=>"주문전용상품"]
];


$hbtiTypes = [
    'SRJT', 'SRJE', 'SRPT', 'SRPE',
    'SFJT', 'SFJE', 'SFPT', 'SFPE',
    'HRJT', 'HRJE', 'HRPT', 'HRPE',
    'HFJT', 'HFJE', 'HFPT', 'HFPE',
];



//원가산출함수
function makeKoedgeCost($o_p=null, $weight=null, $ex_yen=null, $kg_p=null, $sale_price=null, $national=null){
	// 파라미터 기본값 설정 (null-safe)
	if($o_p === null) $o_p = 0;
	if($weight === null) $weight = "";
	if($ex_yen === null) $ex_yen = 0;
	if($kg_p === null) $kg_p = 0;
	if($sale_price === null) $sale_price = 0;
	if($national === null) $national = "";

	//원가 원전환
	if( $national == "cn" ){
		$op_won = $o_p * $ex_yen;
		$simbol = "위안";
	}else{
		$op_won = $o_p * ($ex_yen/100);
		$simbol = "￥";
	}

	if( $national == "cn" ){
		global $delivery_p_cn;
		$delivery_p  = $delivery_p_cn ?? 2800; // 배송비
	}else{
		$delivery_p  = $weight * ($kg_p * 0.001); // 배송비
	}

	$tariff_p = $op_won*0.08; //관세
	$tariff_vat_p = ($op_won + $tariff_p)*0.1; //부가세
	$tax_p = $tariff_p + $tariff_vat_p + $delivery_p;
	$cost_p = $op_won + $tax_p;

if( $national == "cn" ){
	$tariff_p2 = ($op_won/2)*0.08; //관세
	$tariff_vat_p2 = ( ($op_won/2) + $tariff_p2)*0.1; //부가세
	$tax_p2 = $tariff_p2 + $tariff_vat_p2 + $delivery_p;
	$cost_p2 = $op_won + $tax_p2;
}

	$str = "<div class='show_cost_result'>";
	$str .= "<ul>환율 : <b class='o-p'>".number_format($ex_yen)."</b>원 / kg당 : <b class='o-p'>".number_format($kg_p)."</b>원</ul>";
	$str .= "<ul>원전환 : <b class='o-p'>".number_format($o_p)."</b>".$simbol." -> <b class='o-p'>".number_format($op_won)."</b>원</ul>";
	$str .= "<ul>적용무게 : <b class='o-p'>".number_format($weight)."</b></ul>";
	$str .= "<ul>관세(8%) : ".number_format($tariff_p)."원 / 부가세 : ".number_format($tariff_vat_p)."원 / 배송비 : ".number_format($delivery_p)."원 = ".number_format($tax_p)."원</ul>";
	$str .= "<ul>원가 : <b class='cost-p'>".number_format($cost_p)."</b>원</ul>";

	$str .= "<ul>예상판매가(50%) : <b class='o-p'>".number_format($cost_p/0.5)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p/0.5 - $cost_p))."</b>원";

	if( ($cost_p/0.5) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p/0.5 - $cost_p)-2500)."</b>원";
	}
	$str .= "</ul>";

	$str .= "<ul>예상판매가(40%) : <b class='o-p'>".number_format($cost_p/0.6)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p/0.6 - $cost_p))."</b>원";

	if( ($cost_p/0.6) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p/0.6 - $cost_p)-2500)."</b>원";
	}
	$str .= "</ul>";

	$str .= "<ul>예상판매가(35%) : <b class='o-p'>".number_format($cost_p/0.65)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p/0.65 - $cost_p))."</b>원";

	if( ($cost_p/0.65) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p/0.65 - $cost_p)-2500)."</b>원";
	}
	$str .= "</ul>";

	$str .= "<ul>예상판매가(30%) : <b class='o-p'>".number_format($cost_p/0.7)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p/0.7 - $cost_p))."</b>원";

	if( ($cost_p/0.7) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p/0.7 - $cost_p)-2500)."</b>원";
	}
	$str .= "</ul>";

	$str .= "<ul>예상판매가(20%) : <b class='o-p'>".number_format($cost_p/0.8)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p/0.8 - $cost_p))."</b>원";

	if( ($cost_p/0.8) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p/0.8 - $cost_p)-2500)."</b>원";
	}
	$str .= "</ul>";

if( $national == "cn" ){
	$str .= "<ul class='m-t-10'>반값신고 관세(8%) : ".number_format($tariff_p2)."원 / 부가세 : ".number_format($tariff_vat_p2)."원 / 배송비 : ".number_format($delivery_p)."원 = ".number_format($tax_p2)."원</ul>";
	$str .= "<ul>반값신고 원가 : <b class='cost-p'>".number_format($cost_p2)."</b>원</ul>";

	$str .= "<ul>예상판매가(50%) : <b class='o-p'>".number_format($cost_p2/0.5)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p2/0.5 - $cost_p2))."</b>원";

	if( ($cost_p2/0.5) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p2/0.5 - $cost_p2)-2500)."</b>원";
	}
	$str .= "</ul>";

	$str .= "<ul>예상판매가(40%) : <b class='o-p'>".number_format($cost_p2/0.6)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p2/0.6 - $cost_p2))."</b>원";

	if( ($cost_p2/0.6) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p2/0.6 - $cost_p2)-2500)."</b>원";
	}
	$str .= "</ul>";

	$str .= "<ul>예상판매가(35%) : <b class='o-p'>".number_format($cost_p2/0.65)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p2/0.65 - $cost_p2))."</b>원";
	if( ($cost_p2/0.65) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p2/0.65 - $cost_p2)-2500)."</b>원";
	}
	$str .= "</ul>";

	$str .= "<ul>예상판매가(30%) : <b class='o-p'>".number_format($cost_p2/0.7)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p2/0.7 - $cost_p2))."</b>원";
	if( ($cost_p2/0.7) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p2/0.7 - $cost_p2)-2500)."</b>원";
	}
	$str .= "</ul>";

	$str .= "<ul>예상판매가(20%) : <b class='o-p'>".number_format($cost_p2/0.8)."</b>원 | 마진 : <b class='cost-p2'>".number_format(($cost_p2/0.8 - $cost_p2))."</b>원";
	if( ($cost_p2/0.8) >= 30000 ){
		$str .= " | 3만무배 : <b class='cost-p'>".number_format(($cost_p2/0.8 - $cost_p2)-2500)."</b>원";
	}
	$str .= "</ul>";

}



	if( $sale_price > 0 ){
		//$margin_per = ($sale_price-$cost_p)/$cost_p;
		$margin_per = (($sale_price-$cost_p)/$sale_price)*100;
		$str .= "<ul>쑈당몰 판매가 : <b class='cost-p'>".number_format($sale_price)."</b>원 | 마진 : <b class='cost-p'>".number_format($sale_price-$cost_p)."</b> | 마진율 : <b class='cost-p2'>".round($margin_per,2)."</b>%</ul>";
	}

	$str .= "</div>";

	$result = $str;
	return $result;
}

//마진률 뱉어내기
function makeKoedgeMargin($o_p=null, $weight=null, $ex_yen=null, $kg_p=null, $sale_price=null){
	// 파라미터 기본값 설정 (null-safe)
	if($o_p === null) $o_p = 0;
	if($weight === null) $weight = 0;
	if($ex_yen === null) $ex_yen = 0;
	if($kg_p === null) $kg_p = 0;
	if($sale_price === null) $sale_price = 0;
	
	$op_won = $o_p * ($ex_yen/100); //원가 원전환
	$delivery_p  = $weight * ($kg_p * 0.001); // 배송비
	$tariff_p = $op_won*0.08; //관세
	$tariff_vat_p = ($op_won + $tariff_p)*0.1; //부가세
	$tax_p = $tariff_p + $tariff_vat_p + $delivery_p;
	$cost_p = $op_won + $tax_p;
	$margin_per = round((($sale_price-$cost_p)/$sale_price)*100,2);
	return $margin_per;
}

function makeKoedgeIp(){

	$randomIpList = explode(",","202.6,202.14,202.21,202.20,202.30,203.252,203.248,203.244,203.240,203.224,203.236,203.225,203.234,203.232,203.226,203.230,210.124,210.116,210.108,210.104,210.100,210.99,210.97,210.98,210.96,210.92,210.178,210.182,210.216,210.90,210.204,210.220,211.32,211.40,211.52,211.104,211.168,211.200,211.232,211.216,211.226,61.72,211.206,61.248,61.96,61.78,211.212,218.48,218.144,218.232,218.50,218.234,219.240,218.36,219.248,220.64,220.72,220.116,220.92,221.144,221.138,150.183,147.6,168.248,222.96,129.254,168.126,222.232,134.75,128.134,220.103,220.149,152.149,163.152,168.131,147.46,147.47,163.239,158.44,165.194,169.140,164.125,141.223,165.229,168.115,137.68,143.248,150.150,156.147,165.186,165.243,166.104,166.125,161.122,163.180,168.78,165.213,168.219,222.231,147.43,166.103,59.0,218.101,168.188,157.197,165.132,155.230,203.81,203.83,59.150,164.124,60.196,61.32,61.40,203.90,59.186,61.4,61.247,166.79,154.10,221.132,203.100,165.133,202.86,192.5,210.16,168.154,221.133,202.126,203.123,61.47,203.128,58.72,202.133,58.65,203.130,202.136,203.132,192.104,150.197,220.230,192.203,210.210,192.100,58.120,192.245,192.132,203.109,192.249,58.102,58.140,58.148,58.145,203.82,58.180,58.181,58.224,58.184,218.209,203.142,203.152,202.150,125.7,125.31,125.128,125.57,165.141,165.246,192.195,125.60,202.158,125.61,125.176,202.73,202.167,203.170,125.208,203.171,125.209,125.240,125.248,203.173,125.252,202.179,124.0,203.175,124.5,124.2,222.251,124.48,124.199,124.254,124.28,124.46,203.84,124.80,203.210,203.212,210.0,124.111,124.136,124.146,202.163,124.194,203.153,121.128,203.215,202.89,124.197,124.198,124.216,203.216,203.223,210.2,124.243,121.1,121.127,121.55,121.254,203.207,122.49,122.99,122.199,121.64,122.254,123.99,210.57,121.0,121.53,123.254,210.87,121.78,121.50,121.88,210.89,121.54,210.192,121.126,121.100,203.133,59.86,121.200,122.32,122.101,61.5,121.124,58.138,202.22,203.217,202.68,58.146,202.131,121.252,122.0,58.87,58.29,202.171,122.100,123.200,59.151,59.152,121.160,122.202,122.203,122.252,123.199,123.98,122.128,122.129,203.166,123.0,202.43,122.153,123.32,123.248,203.149,123.250,123.108,123.109,61.245,123.111,123.212,124.3,123.228,123.140,116.32,116.193,116.200,116.199,116.212,116.67,116.68,116.84,203.129,116.120,116.89,116.93,117.16,116.90,124.153,117.20,203.169,117.53,117.55,117.58,152.99,117.110,118.32,117.123,118.67,118.91,118.103,118.128,118.107,210.4,118.176,203.17,118.216,124.217,118.234,121.101,118.127,202.165,119.17,119.30,119.31,202.174,119.42,119.59,119.64,119.63,119.75,119.77,119.82,119.148,119.149,119.161,119.192,119.235,120.29,120.50,120.73,120.136,120.142,124.195,124.66,114.29,120.143,114.30,114.52,114.31,114.70,203.190,114.108,114.111,114.129,114.141,114.200,114.199,115.0,115.40,115.31,115.68,115.69,115.84,115.88,115.86,115.85,115.136,115.126,115.144,115.145,115.160,115.161,115.165,115.178,113.10,115.187,113.21,113.29,113.30,113.52,113.59,113.60,113.61,113.130,113.131,113.198,113.199,113.197,113.216,112.72,112.76,112.106,112.108,112.109,112.121,112.133,112.136,112.137,112.140,112.144,112.160,112.196,112.216,110.8,112.212,112.213,112.214,110.4,110.34,110.5,110.35,110.44,110.45,110.46,110.68,110.76,110.92,110.93,110.165,110.172,110.232,111.171,111.65,111.67,111.91,111.118,180.64,111.218,111.221,180.80,180.92,180.132,180.131,180.150,180.182,180.189,210.211,180.210,180.211,180.224,180.222,180.233,180.236,183.96,183.78,183.86,183.90,183.91,175.28,175.41,175.112,175.45,175.106,175.107,175.111,175.158,175.176,175.192,182.50");
	$ranNum = rand(0, 415);
	return $randomIpList[$ranNum];
}

$_gva_koedge_onadb_score_option = array("자극/기믹","유지관리","냄새/유분/소재","조임/탄력","마감/내구성","조형/패키지","진공");

	$arr_ko_1st = array('ㄱ','ㄴ','ㄷ','ㄹ','ㅁ','ㅂ','ㅅ','ㅇ','ㅈ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ','#'); //초성
	$arr_en_1st = array('A', 'B', 'C', 'D', 'E', 'F','G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 'P', 'Q', 'R','S', 'T', 'U', 'V', 'W', 'X','Y', 'Z', '@');

?>