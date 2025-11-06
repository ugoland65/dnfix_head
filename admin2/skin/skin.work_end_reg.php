<?

	$_query = "select 
		COUNT( CASE WHEN A.cd_cost_price > 0  THEN 0 END ) as have_cost_price_count,
		COUNT( CASE WHEN A.cd_cost_price = 0  THEN 0 END ) as nohave_cost_price_count,
		SUM( CASE WHEN A.cd_cost_price > 0 THEN A.cd_cost_price * D.ps_stock END )as cost_price_sum,
		SUM( CASE WHEN A.cd_sale_price > 0 THEN A.cd_sale_price * D.ps_stock END )as sale_price_sum,
		
		A.cd_cost_price, A.cd_sale_price, A.CD_BRAND_IDX, 
		A.CD_IDX, A.CD_KIND_CODE, A.cd_national, A.cd_weight_fn, A.cd_code_fn, A.CD_IMG, A.CD_NAME, A.CD_NAME_OG, 
		B.BD_NAME as brand_name1,
		D.ps_idx, D.ps_stock, D.ps_rack_code
		from "._DB_COMPARISON." A
		left join "._DB_BRAND." B ON (B.BD_IDX = A.CD_BRAND_IDX  ) 
		inner join prd_stock D ON (D.ps_prd_idx = A.CD_IDX  ) 
		WHERE D.ps_stock > 0 GROUP BY CD_BRAND_IDX ORDER BY cost_price_sum DESC ";
	$_result = sql_query_error($_query);

?>
<style type="text/css">
.table-style tr.ok td{ background-color:#eee; }
.table-style tr.nohave td{ background-color:#feffb6; }

.work-end-reg-wrap{ display:table; }
.work-end-reg-wrap > ul{ display:table-cell; vertical-align:top; }
.work-end-reg-wrap > ul.work-end-reg-form-wrap{ padding-left:20px; }
</style>
<div id="contents_head">
	<h1>일일 마감</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
	
		<form id="work_end_form">
		<input type="hidden" name="a_mode" value="day_work_end" >

		<div class="work-end-reg-wrap">
			<ul>

				<div>재고 현황</div>
				<table class="table-style m-t-5">	
					<tr class="list">
						<th class="">브랜드</th>
						<th class="">원가 O</th>
						<th class="">원가 X</th>
						<th class="">원가합계</th>
						<th class="">판매가합계</th>
					</tr>
				<?

				$_total_count_sum1 = 0;
				$_total_count_sum2 = 0;

				$_total_sum1 = 0;
				$_total_sum2 = 0;
				
				while($list = sql_fetch_array($_result)){

					$_total_count_sum1 += $list['have_cost_price_count'];
					$_total_count_sum2 += $list['nohave_cost_price_count'];

					$_total_sum1 += $list['cost_price_sum'];
					$_total_sum2 += $list['sale_price_sum'];

					if( $list['nohave_cost_price_count'] > 0 ){
						$_tr_class= "nohave";
					}else{
						$_tr_class= "ok";
					}

					if( $list['CD_BRAND_IDX'] == 0 ){
						$_brand_name = "브랜드 미지정";
					}else{
						$_brand_name = $list['brand_name1'];
					}

				?>

					<input type="hidden" name="brand_idx[]" value="<?=$list['CD_BRAND_IDX']?>">
					<input type="hidden" name="brand_name[]" value="<?=$_brand_name?>">
					<input type="hidden" name="cost_count1[]" value="<?=$list['have_cost_price_count']?>">
					<input type="hidden" name="cost_count2[]" value="<?=$list['nohave_cost_price_count']?>">
					<input type="hidden" name="cost_price_sum[]" value="<?=$list['cost_price_sum']?>">
					<input type="hidden" name="sale_price_sum[]" value="<?=$list['sale_price_sum']?>">

					<tr class="<?=$_tr_class?>" >
						<td class="">
							(<?=$list['CD_BRAND_IDX']?>) <a href="/ad/prd/prd_main/brand_idx=<?=$list['CD_BRAND_IDX']?>:"><?=$_brand_name?></a>
						</td>
						<td class="text-right">
							<?=$list['have_cost_price_count']?>
						</td>
						<td class="text-right">
							<?=$list['nohave_cost_price_count']?>
						</td>
						<td class="text-right">
							<?=number_format($list['cost_price_sum'])?>
						</td>
						<td class="text-right">
							<?=number_format($list['sale_price_sum'])?>
						</td>
					</tr>
				<? } ?>

					<input type="hidden" name="total_count_sum1" value="<?=$_total_count_sum1?>">
					<input type="hidden" name="total_count_sum2" value="<?=$_total_count_sum2?>">
					<input type="hidden" name="total_cost_price_sum" value="<?=$_total_sum1?>">
					<input type="hidden" name="total_sale_price_sum" value="<?=$_total_sum2?>">

					<tr class="list">
						<th class=""></th>
						<th class="text-right"><?=number_format($_total_count_sum1)?></th>
						<th class="text-right"><?=number_format($_total_count_sum2)?></th>
						<th class="text-right"><b><?=number_format($_total_sum1)?></b></th>
						<th class="text-right"><b><?=number_format($_total_sum2)?></b></th>
					</tr>
				</table>

			</ul>
			<ul class="work-end-reg-form-wrap">
				
				<div>
					<ul>날짜 : <div class="calendar-input" style="display:inline-block;"><input type="text" name="day_code"  value="<?=date("Y-m-d")?>" style="width:80px;" ></div></ul>
					<ul>
						<textarea name="memo" id="memo" placeholder="메모" ></textarea>
					</ul>
				</div>

					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="dayWorkEndReg.save(this);" > 
						<i class="far fa-check-circle"></i> 일일마감 등록
					</button>

			</ul>
		</div>

		</form>

	</div>
</div>
<script type="text/javascript"> 
<!-- 
var dayWorkEndReg = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		save : function( obj ) {

			var formData = $("#work_end_form").serializeArray();

			//$(obj).attr('disabled', true);
			$.ajax({
				url: "/ad/processing/accounting",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						alert("저장되었습니다.");
						location.href='/ad/accounting/work_end_reg';
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					//$(obj).attr('disabled', false);
				}
			});

		}
	};

}();
//--> 
</script> 