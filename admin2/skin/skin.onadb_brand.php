<?php

	// 변수 초기화
	$_pn = $_GET['pn'] ?? $_pn ?? 1;
	$_sort_kind = $_GET['sort_kind'] ?? $_sort_kind ?? "";
	$_open_mode = $_GET['open_mode'] ?? $_open_mode ?? "";
	$_s_text = $_GET['s_text'] ?? $_POST['s_text'] ?? "";
	$s_kind_code = $_GET['s_kind_code'] ?? $_POST['s_kind_code'] ?? "";
	
	// 배열 초기화 (inc_common.php에서 정의되지 않은 경우 대비)
	if (!isset($koedge_prd_kind_array)) {
		$koedge_prd_kind_array = [];
	}
	if (!isset($_arr_national)) {
		$_arr_national = [];
	}
	
	$_where = " WHERE bd_onadb_active = 'Y' ";

	if( $_pn == "" ) $_pn = 1;

	$total_count = sql_counter(_DB_BRAND, $_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "brandLlist.list", "");

	if( $_sort_kind == "stock" ){
		$_sort = " D.ps_stock DESC ";
	}elseif( $_sort_kind == "stock_asc" ){
		$_sort = " D.ps_stock ASC ";
	}elseif( $_sort_kind == "idx" ){
		$_sort = " A.CD_IDX DESC ";
	}elseif( $_sort_kind == "rack_code" ){
		$_sort = " D.ps_rack_code ASC ";
	}elseif( $_sort_kind == "soldout" ){
		$_sort = " ( CASE WHEN D.ps_stock < 1  THEN 0 WHEN D.ps_stock > 0  THEN 1 END ), D.ps_soldout_date DESC ";

	}

	$_sort = " bd_onadb_sort_num asc ";

	$_limit = " limit ".$from_record.", ".$list_num;
	if( $_open_mode == "popup" ){
		$_limit = "";
	}

	$_query = "select * from "._DB_BRAND."
		".$_where." ORDER BY ".$_sort." ".$_limit;
	$_result = sql_query_error($_query);

?>
<div id="contents_head">
	<h1>onaDB 브랜드 정렬</h1>
	
	<!-- 
	<div class="head-btn-wrap m-l-10">
		<button type="button" class="btnstyle1 btnstyle1-sm" onclick="" >주문처 관리</button>
	</div>
	-->

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="onadbBrandLlist.reg()" > 
			<i class="fas fa-plus-circle"></i>
			신규 브랜드 생성
		</button>
	</div>


	<div class="head-right-fixed-wrap">

		<div>
			<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
		</div>
		<div class="m-t-7">
			<ul class="m-t-5">
				<input type='text' name='s_text' id='s_text' value="<?=$_s_text ?>" placeholder="검색어" style="width:100%;">
			</ul>
			<ul class="m-t-5">
				<select name="s_kind_code" id="s_kind_code" >
					<option value="">전체 종류</option>
					<?
					for($t=0; $t<count($koedge_prd_kind_array); $t++){
					?>
					<option value="<?=$koedge_prd_kind_array[$t]['code'] ?? ''?>" <? if( $s_kind_code == ($koedge_prd_kind_array[$t]['code'] ?? '') ) echo "selected";?>><?=$koedge_prd_kind_array[$t]['name'] ?? ''?></option>
					<? } ?>
				</select>
				<select name="s_national" id="s_national" >
					<option value="">수입국</option>
					<?
					for ($i=0; $i<count($_arr_national); $i++){
					?>
					<option value="<?=$_arr_national[$i]['code'] ?? ''?>" ><?=$_arr_national[$i]['name'] ?? ''?></option>
					<? } ?>
				</select>
			</ul>
			<ul class="m-t-5">
				SORT : 
				<select name="sort_kind" id="sort_kind" >
					<option value="stock" <? if( $_sort_kind == "stock" ) echo "selected";?>>재고 많은순</option>
					<option value="stock_asc" <? if( $_sort_kind == "stock_asc" ) echo "selected";?>>재고 적은순</option>
					<option value="idx" <? if( $_sort_kind == "idx" ) echo "selected";?> >상품 등록순</option>
					<option value="rack_code" <? if( $_sort_kind == "rack_code" ) echo "selected";?> >랙코드순</option>
					<option value="soldout" <? if( $_sort_kind == "soldout" ) echo "selected";?> >품절일 최근순</option>
				</select>
			</ul>
			<ul class="m-t-15">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="onadbBrandLlist.list();" > 
					<i class="fas fa-search"></i> 검색
				</button>
			</ul>
		</div>

	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<style type="text/css">
		.no-image{ display:inline-block; width:50px; height:50px; line-height:120%; box-sizing:border-box; border:1px solid #ddd; background-color:#eee; 
			color:#999; font-size:11px; padding-top:10px;
		}
		.table-style tr.tr-no-stock td{ background-color:#eee !important; }
		.table-style tr.tr-normal td{ background-color:#fff !important; }

		<? if( $_open_mode == "popup" ){ ?>
		.table-style tr td{ word-break:break-all; }
		<? } ?>

		</style>

		<? if( $_s_text ){ ?>
			<div class="search-title">	
				검색어 ( <b style='color:red;'><?=$_s_text?></b> ) 검색결과 : <b><?=$total_count?></b>건 검색되었습니다.
				<button type="button" class="search-reset-btn btn btnstyle1 btnstyle1-inverse btnstyle1-sm" id="search_reset">검색 초기화</button>
			</div>
		<? }else{ ?>
			<div class="total">Total : <span><b><?=number_format($total_count)?></b></span> &nbsp; | &nbsp;  <span><b><?=$_pn?></b></span> / <?=$total_page?> page</div>
		<? } ?>

		<form id="onadb_brand_form">
		<input type="hidden" name="a_mode" id="a_mode" >

		<table class="table-style m-t-5" id="onadb_brand_table">	
			<tr class="list">
				<th class=""></th>
				<? if( $_open_mode != "popup" ){ ?>
				<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
				<? } ?>

				<th class="list-idx">IDX</th>

				<th class="" style="width:60px;">이미지</th>
				<th class="">노출순서</th>
				<th class="">이름(국문)</th>
				<th class="">이름(영문)</th>
				<th class="">노출</th>
				<th class="">상품수</th>
				<th class="">보유상품</th>
				<th class="">상세내용</th>
				<th class="">삭제</th>
			</tr>
			<tbody>
		<?

		$_total_sum1 = 0;
		$_total_sum2 = 0;

		while($list = sql_fetch_array($_result)){
			
			if (!is_array($list)) continue;

			$img_path = "";
			if( $list['BD_LOGO'] ?? '' ){
				$img_path = '/data/brand_logo/'.$list['BD_LOGO'];
			}

			$_showdang_active_text = "";
			$_tr_class = "";
			if( ($list['bd_showdang_active'] ?? '') == "N" ){
				$_tr_class = "tr-no-stock";
			}elseif( ($list['bd_showdang_active'] ?? '') == "Y" ){
				$_tr_class = "tr-normal";
				$_showdang_active_text = "쑈당몰 노출";
			}

			$_list_active_text = "";
			if( ($list['BD_LIST_ACTIVE'] ?? '') == "N" ){
				$_list_active_text = "검색 제외";
			}

		
			$query_count = " select 
				count( A.CD_IDX ) AS prdcount,
				COUNT( D.ps_idx ) as stock_count,
				COUNT( CASE WHEN D.ps_stock > 0  THEN 0 END ) as have_stock_count
				from "._DB_COMPARISON." A
				left join prd_stock D ON (D.ps_prd_idx = A.CD_IDX  ) where CD_BRAND_IDX = '".($list['BD_IDX'] ?? '')."' ";

			$result_count = mysqli_query($connect, $query_count);
			$_prd_brand_count = sql_fetch_array($result_count);
			
			if (!is_array($_prd_brand_count)) {
				$_prd_brand_count = ['prdcount' => 0, 'stock_count' => 0, 'have_stock_count' => 0];
			}


		?>
		<tr align="center" id="trid_<?=$list['BD_IDX'] ?? ''?>" class="<?=$_tr_class?>">
			<td ><p class="position-move-btn"><i class="fas fa-arrows-alt-v"></i></p></td>
			<? if( $_open_mode != "popup" ){ ?>
			<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list['CD_IDX'] ?? ''?>" ></td>
			<? } ?>

			<td class="list-idx"><?=$list['BD_IDX'] ?? ''?></td>
			<td >
				<? if( $list['BD_LOGO'] ?? '' ){ ?>
					<img src="<?=$img_path?>" style="height:50px; border:1px solid #eee !important;">
				<? }else{ ?>
					<div class="no-image 50">No<br>image</div>
				<? } ?>
			</td>
			<td class="">
				<?=$list['bd_onadb_sort_num'] ?? ''?>
				<input type="hidden" name="brand_idx[]" value="<?=$list['BD_IDX'] ?? ''?>">
			</td>
			<td class="text-left"><B><?=$list['BD_NAME'] ?? ''?></B></td>
			<td class="text-left"><?=$list['BD_NAME_EN'] ?? ''?></td>
			<td class="">
				<div>

				<? if( ($list['bd_showdang_active'] ?? '') == "Y" ){ ?>
					<ul><?=$_showdang_active_text?></ul>
				<? } ?>
				<? if( ($list['bd_onadb_active'] ?? '') == "Y" ){ ?>
					<ul>오나DB 노출</ul>
				<? } ?>
				<? if( ($list['BD_LIST_ACTIVE'] ?? '') == "N" ){ ?>
					<ul>검색 제외</ul>
				<? } ?>


				</div>
			</td>
			<td class="">
				<a href="/ad/prd/prd_db/brand_idx=<?=$list['BD_IDX'] ?? ''?>:"><? if( ($_prd_brand_count['prdcount'] ?? 0) > 0 )  echo $_prd_brand_count['prdcount']; ?></a>
			</td>
			<td class="">
				<? if( ($_prd_brand_count['stock_count'] ?? 0) > 0 ){ ?>
					<a href="/ad/prd/prd_main/brand_idx=<?=$list['BD_IDX'] ?? ''?>:">
					<?=$_prd_brand_count['stock_count'] ?? 0?> / 
					<b><?=$_prd_brand_count['have_stock_count'] ?? 0?></b>
					</a>
				<? } ?>
			</td>
			<td>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="onadbBrandLlist.view('<?=$list['BD_IDX'] ?? ''?>')" > 상세내용 </button>
			</td>
			<td>
				<? if( ($list['bd_showdang_active'] ?? '') == "N" && ($_prd_brand_count['prdcount'] ?? 0) == 0 ){ ?>
				<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="onadbBrandLlist.del('<?=$list['BD_IDX'] ?? ''?>')" ><i class="fas fa-trash-alt"></i></button>
				<? } ?>
			</td>
		<tr>
		<? } ?>
			</tbody>

		</table>
		</form>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="onadbBrandLlist.showSort()" > 
		<i class="far fa-check-circle"></i> 노출순서 저장
	</button>
</div>
<script type="text/javascript"> 
<!--
var onadbBrandLlist = function() {

	return {

		init : function() {

		},

		//오나디비 노출순서 변경
		showSort: function(  ) {
			
			$("#a_mode").val("onadb_brand_sort");

			var formData = $("#onadb_brand_form").serializeArray();

			$.ajax({
				url: "/ad/processing/onadb",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						location.reload();
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
					$(obj).attr('disabled', false);
				}
			});

		},

		list: function( pn ) {

			var search_value = $("#search_value").val();
			
			if( search_value ){
				oo_import = "all";
			}

			$.ajax({
				url: "/ad/ajax/brand_list",
				data: { "pn":pn },
				type: "POST",
				dataType: "html",
				success: function(getdata){
					$('#list_wrap').html(getdata);
				},
				error: function(){
					showAlert("Error", "에러2", "alert2" );
					return false;
				},
				complete: function() {
					loading('off','white');
				}
			});

		},

		reg : function(obj) {

			var width = "800px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "브랜드 신규생성",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/brand_info',
						data: { "pmode":"newReg" },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
							
						}
					},
				}
			});

		},

		view : function( idx ) {

			var width = "800px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "브랜드 정보",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/brand_info',
						data: { "idx":idx },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
							
						}
					},
				}
			});

		},

		del : function( idx ) {

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '삭제하시면 데이터는 복구하지 못합니다.',
				autoClose: 'cencle|9000',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제',
						btnClass: 'btn-red',
						action: function(){
							
							$.ajax({
								url: "/ad/processing/brand",
								data: { "a_mode":"brand_del", "idx": idx },
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										$("#trid_"+ idx).remove();
									}else{
										showAlert("Error", res.msg, "dialog" );
										return false;
									}
								},
								error: function(){
									showAlert("Error", "에러", "dialog" );
									return false;
								},
								complete: function() {
								}
							});

						}
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});

		},

	};

}();

//onadbBrandLlist.list();

$(function(){

	$("#onadb_brand_table tbody").sortable({
		axis:"y"
	}).disableSelection();

});

//--> 
</script>