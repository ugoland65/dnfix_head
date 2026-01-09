<style>
.no-image{ width:50px; height:50px; line-height:120%; box-sizing:border-box; border:1px solid #ddd; background-color:#eee; 
	color:#999; font-size:11px; padding-top:10px; text-align:center;
}
</style>
<div id="contents_head">
	<h1>브랜드 목록</h1>

   <button class="btnstyle1 btnstyle1-success btnstyle1-sm m-l-10" 
      onclick="window.open('https://docs.google.com/document/d/13Rlew5ROg1WcwaRbnRiSD0QpsinIHB4K4L5-Jqw2nWY/edit?usp=drive_link', '_blank');">
         브랜드 관리 매뉴얼 문서
   </button>

    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="brandLlist.brandCreate()" > 
			<i class="fas fa-plus-circle"></i>
			신규 브랜드 생성
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
      <div id="list_new_wrap">

         <div class="table-top">
            <ul class="total">
                  Total : <span><b><?=number_format($pagination['total'] ?? 0)?></b></span> &nbsp; | &nbsp;
                  <span><b><?=$pagination['current_page'] ?? 1?></b></span> / <?=$pagination['last_page'] ?? 1?> page
				</ul>
         </div>

			<div class="table-wrap5 m-t-5">
				<div class="scroll-wrap">

               <table class="table-st1">
                     <thead>
                     <tr class="list">
                        <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                        <th class="list-idx">고유번호</th>
                        <th class="">관리</th>
                        <th class="">로고</th>
                        <th class="">브랜드 이름 (국문)</th>
                        <th class="">브랜드 이름 (영문)</th>
                        <th class="">쑈당몰 활성</th>
                        <th class="">일반상품수</th>
                        <th class="">공급사 상품수</th>
                        <th class="">보유상품수</th>
                        <th class="">수정일</th>
                        <th class="">등록일</th>
                     </tr>
                     </thead>
                     <tbody>
                        <?php
                           foreach( $brandList as $brand ){ 

                              if( $brand['BD_LOGO'] ){
                                 $img_path = '/data/brand_logo/'.$brand['BD_LOGO'];
                              }
                        ?>
                           <tr>
                              <td><input type="checkbox" name="" onclick="select_all()"></td>
                              <td><?=$brand['BD_IDX']?></td>
                              <td>
                                 <button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="brandModify('<?=$brand['BD_IDX']?>')" >관리</button>
                              </td>
                              <td >
                                 <?php if( $brand['BD_LOGO'] ){ ?>
                                    <img src="<?=$img_path?>" style="height:50px; border:1px solid #eee !important;">
                                 <?php }else{ ?>
                                    <!--
                                    <div class="no-image 50">No<br>image</div>
                                    -->
                                 <?php } ?>
                              </td>
                              <td>
                                 <b><?=$brand['BD_NAME']?></b>
                                 <br>
                                 <span class="list-memo-text"><?=$brand['bd_memo'] ?? ''?></span>
                              
                              </td>
                              <td><?=$brand['BD_NAME_EN'] ?? '' ?></td>
                              <td>
                                 <?php if( $brand['bd_showdang_active'] == 'Y' ){ ?>
                                    <button type="button" id="" class="btnstyle1 btnstyle1-xs" onclick="window.open('https://showdang.co.kr/goods/brand_list.php?cateCd=<?=$brand['bd_matching_cate']?>', '_blank');" >노출중 #<?=$brand['bd_matching_cate']?></button>
                                 <?php } ?>
                              </td>
                              <td class="text-center">
                                 <?php if( $brand['product_count'] > 0 ) { ?>
                                    <a href="/ad/prd/prd_db?s_brand=<?=$brand['BD_IDX']?>" target="_blank"><?=number_format($brand['product_count'])?></a>
                                 <?php } ?>
                              </td>
                              <td class="text-center">
                                 <?php if( $brand['partner_count'] > 0 ) { ?>
                                    <a href="/admin/provider_product/list?s_brand=<?=$brand['BD_IDX']?>" target="_blank"><?=number_format($brand['partner_count'])?></a>
                                 <?php } ?>
                              </td>
                              <td class="text-center">
                                 <?php if( $brand['have_stock_count'] > 0 ) { ?>
                                    <a href="/admin/product/product_stock?s_brand=<?=$brand['BD_IDX']?>&in_stock=have" target="_blank"><?=number_format($brand['have_stock_count'])?></a>
                                 <?php } ?>
                              </td>
                              <td><?=date('Y-m-d', strtotime($brand['updated_at']))?></td>
                              <td><?=date('Y-m-d', strtotime($brand['created_at']))?></td>
                           </tr>
                        <?php } /* endforeach */ ?>
                     </tbody>
                  </table>

            </div>
         </div>

      </div>
   </div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script type="text/javascript">

   var brandLlist = function() {

      /**
       * 브랜드 신규생성
       */
      function brandCreate() {
         openDialog('/admin/brand/reg', {}, '브랜드 신규생성', '800px', 'GET');
      }
      
      return {
         brandCreate
      }
   }();
</script>