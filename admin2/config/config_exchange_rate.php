<?
include "../lib/inc_common.php";

		$pageGroup = "config";
		$pageName = "config_exchange_rate";

		$now_day = date("Ymd",$gva_today_mktime_start);
		

		
	for($i=0;$i<7;$i++){

		${"before_Day".$i} = date("Ymd", strtotime($now_day." -".$i." day"));


		$url = "https://www.koreaexim.go.kr/site/program/financial/exchangeJSON?authkey=FgZEci4k7XznclTzYhHT8odYaOXzY7Vz&data=AP01&searchdate=".${"before_Day".$i};
		$is_post = false;
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
		${"exchange_data".$i} = json_decode($response, true);

	}
	


	$_inst_sell_count = 0;
	$_inst_get_count = 0;

	$exchange_rate_where = " ";
	$exchange_rate_query = "select * from "._DB_EXCHANGE_RATE." ".$exchange_rate_where." order by ER_IDX desc limit 0, 20";
	$exchange_rate_result = wepix_query_error($exchange_rate_query);
	while($exchange_rate_list = wepix_fetch_array($exchange_rate_result)){

		if( $exchange_rate_list[ER_KIND] == "sell" ){
			$_inst_sell_count++;
		}else{
			$_inst_get_count++;
		}

		

		$_ary_exchange_rate[] = array(
			"KIND" => $exchange_rate_list[ER_KIND],
			"DEFULT_SYMBOL" => $exchange_rate_list[ER_DEFULT_SYMBOL],
			"DEFULT" => $exchange_rate_list[ER_DEFULT_MONEY],
			"DOLLAR" => $exchange_rate_list[ER_DOLLAR_MONEY],
			"WON" => $exchange_rate_list[ER_WON_MONEY],
			"REG_DATE" => $exchange_rate_list[ER_REQ_DATE],
			"REG_ID" => $exchange_rate_list[ER_REQ_ID]
		);
	}
		

		$exchange_sell = wepix_fetch_array(wepix_query_error("select * from "._DB_EXCHANGE_RATE." ".$exchange_rate_where." where ER_KIND ='sell' order by ER_IDX desc limit 0, 1"));
		$_now_sell_defult_symbol = $exchange_sell[ER_DEFULT_SYMBOL];
		$_now_sell_defult = $exchange_sell[ER_DEFULT_MONEY];
		$_now_sell_dollar = $exchange_sell[ER_DOLLAR_MONEY];
		$_now_sell_won = $exchange_sell[ER_WON_MONEY];
	

		$exchange_get = wepix_fetch_array(wepix_query_error("select * from "._DB_EXCHANGE_RATE." ".$exchange_rate_where." where ER_KIND ='get' order by ER_IDX desc limit 0, 1"));
		$_now_get_defult_symbol = $exchange_get[ER_DEFULT_SYMBOL];
		$_now_get_defult = $exchange_get[ER_DEFULT_MONEY];
		$_now_get_dollar = $exchange_get[ER_DOLLAR_MONEY];
		$_now_get_won = $exchange_get[ER_WON_MONEY];

		$exchange_user = wepix_fetch_array(wepix_query_error("select * from "._DB_EXCHANGE_RATE." ".$exchange_rate_where." where ER_KIND ='user' order by ER_IDX desc limit 0, 1"));
		$_now_user_defult_symbol = $exchange_user[ER_DEFULT_SYMBOL];
		$_now_user_defult = $exchange_user[ER_DEFULT_MONEY];
		$_now_user_dollar = $exchange_user[ER_DOLLAR_MONEY];
		$_now_user_won = $exchange_user[ER_WON_MONEY];

		


include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.section-wrap3{ display:table; }
.section-wrap3-left,
.section-wrap3-center,
.section-wrap3-right{ display:table-cell; box-sizing:border-box; padding:0 10px; }
.section-wrap3-btn{ text-align:center; margin-top:10px;}
</STYLE>
<script type='text/javascript'>

function exchangeSetting(kind){
	var form = document.form1;
    form.defult_symbol.value = document.getElementById("er_"+kind+"_defult_symbol").value;
	form.defult_money.value = document.getElementById("er_"+kind+"_defult_money").value;
	form.dollar_money.value = document.getElementById("er_"+kind+"_dollar_money").value;
    form.won_money.value = document.getElementById("er_"+kind+"_won_money").value;
	form.ex_kind.value = kind;
	form.submit();
};   

</script>
<div id="contents_head">
	<h1>환율 설정 </h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="section-wrap3">
			<div class="section-wrap3-left">
			<form name='form1' method='post' action='<?=_A_PATH_CONFIG_OK?>'>
				<input type='hidden' name='action_mode' value='exchangeSetting'>
				<input type='hidden' name='defult_symbol'>
				<input type='hidden' name='defult_money'>
				<input type='hidden' name='dollar_money'>
				<input type='hidden' name='won_money'>
				<input type='hidden' name='ex_kind'>
				
			</form>

				<div class="section-title">
					<h2>정산 환율</h2>
				</div>
				<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr>
						<th class="tds1">환율 기준</th>				    
						<th class="tds1">฿</th>
						<th class="tds1">$</th>
						<th class="tds1">₩</th>
					</tr>
					<tr>
						<td class="tds2"><input type="text" size='6' id="er_get_defult_symbol" value="<?=$_now_get_defult_symbol?>"></td>
						<td class="tds2"><input type="text" size='6' id="er_get_defult_money" value="<?=$_now_get_defult?>"></td>
						<td class="tds2"><input type="text" size='6' id="er_get_dollar_money" value="<?=$_now_get_dollar?> "></td> 
						<td class="tds2"><input type="text"  size='6' id="er_get_won_money" value="<?=$_now_get_won?> "></td>
					</tr>
				</table>

				<div class="section-wrap3-btn">
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="exchangeSetting('get');"><i class="far fa-check-circle"></i> 정산 환율 수정</button>
				</div>

				<div class="section-title">
					<h2>샵매출 환율</h2>
				</div>
				<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr>
						<th class="tds1">환율 기준</th>				    
						<th class="tds1">฿</th>
						<th class="tds1">$</th>
						<th class="tds1">₩</th>
					</tr>
					<tr>
						<td class="tds2"><input type="text" size='6' id="er_sell_defult_symbol" value="<?=$_now_sell_defult_symbol?>"></td>
						<td class="tds2"><input type="text" size='6' id="er_sell_defult_money" value="<?=$_now_sell_defult?>"></td>
						<td class="tds2"><input type="text" size='6' id="er_sell_dollar_money" value="<?=$_now_sell_dollar?> "></td> 
						<td class="tds2"><input type="text"  size='6' id="er_sell_won_money" value="<?=$_now_sell_won?> "></td>
					</tr>
				</table>
				<div class="section-wrap3-btn">
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="exchangeSetting('sell');"><i class="far fa-check-circle"></i> 샵 매출 환율 수정</button>
				</div>

				<div class="section-title">
					<h2>청구서 발행 환율(손님)</h2>
				</div>
				<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr>
						<th class="tds1">환율 기준</th>				    
						<th class="tds1">฿</th>
						<th class="tds1">$</th>
						<th class="tds1">₩</th>
					</tr>
					<tr>
						<td class="tds2"><input type="text" size='6' id="er_user_defult_symbol" value="<?=$_now_user_defult_symbol?>"></td>
						<td class="tds2"><input type="text" size='6' id="er_user_defult_money" value="<?=$_now_user_defult?>"></td>
						<td class="tds2"><input type="text" size='6' id="er_user_dollar_money" value="<?=$_now_user_dollar?> "></td> 
						<td class="tds2"><input type="text" size='6' id="er_user_won_money" value="<?=$_now_user_won?> "></td>
					</tr>
				</table>
				<div class="section-wrap3-btn">
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="exchangeSetting('user');"><i class="far fa-check-circle"></i> 샵 매출 환율 수정</button>
				</div>

			</div>
			<div class="section-wrap3-center">

				<div class="section-title">
					<h2>환율 변경 내역</h2>
				</div>
				<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr>
						<th>변경일</th>
						<th>종류</th>		
						<th>환율 기준</th>				    
						<th>฿</th>
						<th>$</th>
						<th>₩</th>
						<th>변경인</th>
					</tr>
<?
for($i=0; $i<count($_ary_exchange_rate); $i++){
	$_view_reg_date = date("Y.m.d H:i",$_ary_exchange_rate[$i]['REG_DATE']);
	 ($_ary_exchange_rate[$i]['KIND']=="sell") ? "#dee7f9" : "#f9e9de";

	if($_ary_exchange_rate[$i]['KIND'] == 'sell'){
		$_view_exchange_kind = '샵';
		$trcolor = "#dee7f9";
	}elseif($_ary_exchange_rate[$i]['KIND'] == 'get'){
		$_view_exchange_kind = '정산';
		$trcolor = "#f9e9de";
	}elseif($_ary_exchange_rate[$i]['KIND'] == 'user'){
		$_view_exchange_kind = '청구서';
		$trcolor = "#fae7f9";
	}
?>
					<tr bgcolor="<?=$trcolor?>">
						<td ><?=$_view_reg_date?></td>
						<td ><?=$_view_exchange_kind?></td>
						<td ><?=$_ary_exchange_rate[$i]['DEFULT_SYMBOL']?></td>
						<td ><?=$_ary_exchange_rate[$i]['DEFULT']?></td>
						<td ><?=$_ary_exchange_rate[$i]['DOLLAR']?></td> 
						<td ><?=$_ary_exchange_rate[$i]['WON']?></td>
						<td ><?=$_ary_exchange_rate[$i]['REG_ID']?></td>
					</tr>
<? } ?>
				</table>

			</div>

			<div class="section-wrap3-right">

				<div class="section-title">
					<h2>실시간 환율 데이터</h2>
				</div>
							<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
								<tr>
									<th >기준일</th>
									<th >통화명</th>		
									<th >매매 기준율</th>				    
									<th >보낼때</th>
									<th >받을때</th>

								</tr>
							<?
							
							for($i=0;$i<7;$i++){
								  $exchange_date = date("y-m-d", strtotime($now_day." -".$i." day"));
	  								if(${"exchange_data".$i}[20]['deal_bas_r'] != ''){
								?>
								

								<tr bgcolor="#FFDAB9">
									<td bgcolor="#E6E6FA" rowspan='2'><?=$exchange_date?></td>
									<td >WON</td>
									<td ><?=${"exchange_data".$i}[20]['deal_bas_r']?></td>
									<td ><?=${"exchange_data".$i}[20]['tts']?></td>
									<td ><?=${"exchange_data".$i}[20]['ttb']?></td> 
								</tr>

								<?
									$exchange_d = ${"exchange_data".$i}[20]['deal_bas_r'] * 1000;
									$exchange_d_2 = (int)str_replace(',', '',${"exchange_data".$i}[21]['deal_bas_r']);
									$_exchange_d_deal_bas_r  = $exchange_d /$exchange_d_2;

									$exchange_d_tts = ${"exchange_data".$i}[20]['tts'] * 1000;
									$exchange_d_tts2 = (int)str_replace(',', '',${"exchange_data".$i}[21]['tts']);
									$_exchange_d_tts = $exchange_d_tts /$exchange_d_tts2;

									$exchange_d_ttb = ${"exchange_data".$i}[20]['ttb'] * 1000;
									$exchange_d_ttb2 =  (int)str_replace(',', '',${"exchange_data".$i}[21]['ttb']);
									$_exchange_d_ttb = $exchange_d_ttb / $exchange_d_ttb2;
								?>

								<tr bgcolor="#B0E0E6">
									
									<td ><?=${"exchange_data".$i}[21]['cur_unit']?></td>
									<td ><?=round($_exchange_d_deal_bas_r,2)?></td>
									<td ><?=round($_exchange_d_tts,2)?></td>
									<td ><?=round($_exchange_d_ttb,2)?></td> 
								</tr>
							<?}else{?>
								
						
								
							<?}}?>
							</table>
			</div>
		</div>





	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>