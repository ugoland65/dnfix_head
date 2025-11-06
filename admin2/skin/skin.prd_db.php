<?php
/*
=================================================================================
뷰 파일: admin2/skin/skin.prd_reg_form.php
호출경로 : /ad/ajax/prd_reg_form
설명: 상품 등록 폼 화면
작성자: Lion65
수정일: 2025-03-15
=================================================================================

GET
@getParam {int} $_prd_idx - 상품 시퀀스

CONTROLLER
/application/Controllers/Admin/ProductController.php

*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\Admin\ProductController;

$productController = new ProductController(); 

$viewData = $productController->prdDbIndex();

/*
echo "<pre>";
print_r($viewData);
echo "</pre>";
*/
?>

<div id="contents_head">
	<h1>상품 DB</h1>
	
	<div class="head-btn-wrap m-l-10">
		<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdDB.makeGrouping('');" >선택상품 그룹핑</button>
	</div>

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='/ad/prd/prd_reg'" > 
			<i class="fas fa-plus-circle"></i>
			신규상품 등록
		</button>
	</div>

	<div class="head-right-fixed-wrap">

		<div>
			<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
		</div>
		<div class="m-t-7">
			<ul class="m-t-5">
				<input type='text' name='s_text' id='s_text' size='20' value="<?=$_s_text ?? '' ?>" placeholder="검색어" >
			</ul>
			<ul class="m-t-5">
				<select name="s_brand" id="s_brand" >
					<option value="">전체 브랜드</option>
					<?
					foreach( $viewData['brandForSelect'] as $brand ){
					?>
					<option value="<?=$brand['BD_IDX']?>" <? if( $brand['BD_IDX'] == ($_s_brand ?? '') ) echo "selected";?> ><?=$brand['BD_NAME']?></option>
					<? } ?>
				</select>
			</ul>
			<ul class="m-t-5">
				<select name="s_kind_code" id="s_kind_code" >
					<option value="">전체 종류</option>
					<?
					for($t=0; $t<count($koedge_prd_kind_array); $t++){
					?>
					<option value="<?=$koedge_prd_kind_array[$t]['code']?>" <? if( ($s_kind_code ?? '') == $koedge_prd_kind_array[$t]['code'] ) echo "selected";?>><?=$koedge_prd_kind_array[$t]['name']?></option>
					<? } ?>
				</select>
				<select name="s_national" id="s_national" >
					<option value="">수입국</option>
					<?
					for ($i=0; $i<count($_arr_national); $i++){
					?>
					<option value="<?=$_arr_national[$i]['code']?>" ><?=$_arr_national[$i]['name']?></option>
					<? } ?>
				</select>
				<select name="s_tier" id="s_tier">
					<option value="">티어</option>
					<? for ($i=1; $i<6; $i++){ ?>
					<option value="<?=$i?>"><?=$i?> 티어</option>
					<? } ?>
				</select>
			</ul>
			<ul class="m-t-5">
				SORT : 
				<select name="sort_kind" id="sort_kind" >
					<option value="stock" <? if( ($_sort_kind ?? '') == "stock" ) echo "selected";?>>재고 많은순</option>
					<option value="stock_asc" <? if( ($_sort_kind ?? '') == "stock_asc" ) echo "selected";?>>재고 적은순</option>
					<option value="idx" <? if( ($_sort_kind ?? '') == "idx" ) echo "selected";?> >상품 등록순</option>
					<option value="rack_code" <? if( ($_sort_kind ?? '') == "rack_code" ) echo "selected";?> >랙코드순</option>
					<option value="soldout" <? if( ($_sort_kind ?? '') == "soldout" ) echo "selected";?> >품절일 최근순</option>
				</select>
			</ul>
			<ul class="m-t-15">
				<button type="button" id="searchBtn" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" > 
					<i class="fas fa-search"></i> 검색
				</button>
			</ul>
		</div>

	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap" class="have-head-right-fixed">

		<div id="list_wrap">
			<style type="text/css">
				.no-image{ display:inline-block; width:70px; height:70px; line-height:70px; box-sizing:border-box; border:1px solid #ddd; background-color:#eee; 
					color:#999; font-size:11px;
				}

				.prd-memo{ color:#6b49ff; }
			</style>

	<? if( isset($_s_text) ){ ?>
		<div class="search-title">	
			검색어 ( <b style='color:red;'><?=$_s_text?></b> ) 검색결과 : <b><?=$viewData['pagination']['total']?></b>건 검색되었습니다.
			<button type="button" class="search-reset-btn btn btnstyle1 btnstyle1-inverse btnstyle1-sm" id="search_reset">검색 초기화</button>
		</div>
	<? }else{ ?>
		<div class="total">Total : <span><b><?=number_format($viewData['pagination']['total'])?></b></span> &nbsp; | &nbsp; 
			<span><b><?=$viewData['pagination']['current_page']?></b></span> / 
			<span><b><?=$viewData['pagination']['last_page']?></b></span> page
		</div>
	<? } ?>

	<table class="table-style m-t-6">	
		<tr class="list">
			<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
			<th class="list-idx">고유번호</th>
			<th class="" style="width:80px;">이미지</th>
			<th class="" style="width:80px;">아이콘</th>
			<th class="">분류</th>
			<th class="">티어</th>
			<th class="" style="width:300px;">이름</th>
			<th class="">브랜드</th>
			<th class="">HBTI</th>
			<th class="">등록일</th>
			<th class="">관리</th>
			<th class="">비고</th>
		</tr>
		<?
		foreach( $viewData['prdList'] as $list ){

			if( $list['CD_IMG'] ){
				$img_path = '/data/comparion/'.$list['CD_IMG'];
			}

			if( $list['CD_IMG2'] ){
				$img_path2 = '/data/comparion/'.$list['CD_IMG2'];
			}

		?>
		<tr align="center" id="trid_<?=$list['CD_IDX']?>" class="<?=$_tr_class?>">
			<td class="list-checkbox"><input type="checkbox" name="key_check[]" class="checkSelect" value="<?=$list['CD_IDX']?>" ></td>	
			<td class="list-idx">
				<div>
					<ul><span style="font-size:12px;"><?=$list['CD_IDX']?></span></ul>
					<? if( $list['ps_idx'] ){ ?><ul>( <b style='color:#0093e9; font-size:14px;'><?=$list['ps_idx']?></b> )</ul><? } ?>
				</div>
			</td>
			<td onclick="onlyAD.prdView('<?=$list['CD_IDX']?>','info');" style="cursor:pointer;" >
				<? if( $list['CD_IMG'] ){ ?>
					<img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;">
				<? }else{ ?>
					<div class="no-image">No image</div>
				<? } ?>
			</td>
			<td >
				<? if( $list['CD_IMG2'] ){ ?>
					<img src="<?=$img_path2?>" style="height:70px; border:1px solid #eee !important;">
				<? }else{ ?>
					<div class="no-image">No image</div>
				<? } ?>
			</td>
			<td class="">
				<?=$koedge_prd_kind_name[$list['CD_KIND_CODE']] ?? "미지정"?>
			</td>
			<td class="">
				<?=$list['cd_tier']?>
			</td>
			<td class="text-left">
				<div>
					<? if( $list['CD_RELEASE_DATE'] > 0 ){ ?><ul class="m-b-5 f-s-11" style="color:#777">출시일 : <?=$list['CD_RELEASE_DATE']?></ul><? } ?>
					<ul>
						<b onclick="onlyAD.prdView('<?=$list['CD_IDX']?>','info');" style="cursor:pointer;" ><?=$list['CD_NAME']?></b>

						<? 
						/*
							if( $_ad_id == "admin" ){ ?>
						<button type="button" id="" class="btnstyle1 btnstyle1-xs" onclick="onlyAD.prdViewTest('<?=$list['CD_IDX']?>')" >구 상품정보</button>
						<? } 
						*/
						?>

					</ul>
					<? if( $list['CD_NAME_OG'] ){ ?><ul class="m-t-5"><?=$list['CD_NAME_OG']?></ul><? } ?>
					<? if( $list['cd_memo2'] ){ ?><ul class="m-t-3" style="word-break:break-all"><span class="prd-memo"><i class="fas fa-feather-alt"></i> <?=$list['cd_memo2']?></span></ul><? } ?>
					
					<? if( $list['inSaleIconHtml'] ){ ?>
						<ul ><?=$list['inSaleIconHtml']?></ul>
					<? } ?>

				</div>
			</td>
			<td class="">
				<div>
					<ul><a href="/ad/prd/prd_db/brand_idx=<?=$list['CD_BRAND_IDX']?>:"><?=$list['brand_name1']?></a></ul>
					<? if($list['brand_name2']){ ?><ul class="m-t-5"><a href="/ad/prd/prd_db/brand_idx=<?=$list['CD_BRAND2_IDX']?>:"><?=$list['brand_name2']?></a></ul><? } ?>
				</div>
			</td>
			<td class="">
				<div>
					<ul><?=$list['hbti_html']?></ul>
				</div>
			</td>
			<td class="">
				<span class="f-s-12"><?=date('y.m.d H:i',strtotime($list['cd_reg_time']))?></span>
			</td>
			<td class="">

				<? if( !$list['ps_idx'] ){ ?>
					<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="prdDB.listDel('<?=$list['CD_IDX']?>');"><i class="far fa-trash-alt"></i></button>
				<? } ?>
					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="prdDB.prdCopy('<?=$list['CD_IDX']?>');" >복사</button>

			</td>
			<td class="text-left">
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-sm" onclick="footerGlobal.comment('prd','<?=$list['CD_IDX']?>')" >
					댓글
					<? if( $list['comment_count'] > 0 ) { ?> : <b><?=$list['comment_count']?></b><? } ?>
				</button>
			</td>
		<tr>
		<? } ?>
	</table>
		</div>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap"><?=$viewData['paginationHtml']?></div>
</div>
<script type="text/javascript">
<!--
const prdDB = (function() {

	function init() {

	}

	function list( pn ) {

		var sValue = $("#s_text").val();
		var sBrand = $("#s_brand").val();
		var sKindCode = $("#s_kind_code").val();
		var sNational = $("#s_national").val();
		var sTier = $("#s_tier").val();
		var sSortKind = $("#sort_kind").val();

		$.ajax({
			url: "/ad/ajax/prd_db_list",
			data: { "pn":pn, "s_text":sValue, "s_brand":sBrand, "s_kind_code":sKindCode, "s_national":sNational, "s_tier":sTier, "sort_kind":sSortKind },
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
				//loading('off','white');
			}
		});

	}

	function listDel( idx ) {

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
							url: "/ad/processing/prd",
							data: { "a_mode":"prdDel", "idx": idx },
							type: "POST",
							dataType: "json",
							success: function(res){
								if (res.success == true ){
									toast2("success", "상품삭제", "삭제가 완료되었습니다.");
									$("#trid_"+ idx).remove();
								}else{
									showAlert("Error", res.msg, "dialog" );
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
				},
				cencle: {
					text: '취소',
					action: function(){
					}
				}
			}
		});

	}

	function prdCopy( idx ) {

		$.ajax({
			url: "/ad/processing/prd",
			data: { "a_mode":"prd_copy", "idx":idx },
			type: "POST",
			dataType: "json",
			success: function(res){
				alert("복사등록 되었습니다.");
				location.reload();
			},
			error: function(request, status, error){
				console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				showAlert("Error", "에러", "alert2" );
				return false;
			},
			complete: function() {
				
			}
		});

	}

	//그룹핑 만들기
	function makeGrouping( grouping_mode ) {

		var checkboxCount = $(".checkSelect:checked").length;
		if( checkboxCount == 0 ){
			showAlert("Error", "선택된 상품이 없습니다.", "dialog" );
			return false; 
		}

		var chkArray = new Array();

		$("input[name='key_check[]']:checked").each(function() { 
			var tmpVal = $(this).val(); 
			chkArray.push(tmpVal);
		});

		var width = "1200px";

		prdWindow = $.alert({
			boxWidth : width,
			useBootstrap : false,
			title : "선택상품 그룹핑",
			backgroundDismiss: false,
			closeIcon: true,
			closeIconClass: 'fas fa-times',
			content:function () {
				var self = this;
				return $.ajax({
					url: '/ad/ajax/prd_make_grouping',
					data: { "grouping_mode":grouping_mode, "chkArray":chkArray },
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

	}

	return {
		init,
		list,
		listDel,
		prdCopy,
		makeGrouping
	};

})();

//prdDB.list();

	$("#s_text").bind("keydown", function(e){
		if(e.which=="13"){
			prdDB.list();
		}
	});

	$(function(){
        // 각 입력 필드에 focusout 이벤트 핸들러 추가
        $("#s_brand, #s_kind_code, #s_national, #s_tier").on('focusout', function() {
            // 즉시 검색을 실행하지 않고, 필드에서 포커스가 빠져나갔을 때 검색 준비만 함
        });

        $("#searchBtn").on('click',function(){
            // 검색 파라미터 수집
            var params = {};

            // 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
            var fields = {
                's_brand': $("#s_brand").val(),
                's_kind_code': $("#s_kind_code").val(),
                's_national': $("#s_national").val(),
                's_tier': $("#s_tier").val(),
                'sort_kind': $("#sort_kind").val(),
                's_text': $("#s_text").val()
            };

            // 유효한 값만 params에 추가
            for (var key in fields) {
                if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '') {
                    params[key] = fields[key];
                }
            }

            // URL 쿼리 문자열 생성
            var queryString = Object.keys(params)
                .map(function(key) {
                    return key + '=' + encodeURIComponent(params[key]);
                })
                .join('&');

            // 페이지 이동
            location.href = '/ad/prd/prd_db' + (queryString ? '?' + queryString : '');
        });
    });

//--> 
</script>