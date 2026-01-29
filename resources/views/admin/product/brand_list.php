<style>
   .no-image {
      width: 50px;
      height: 50px;
      line-height: 120%;
      box-sizing: border-box;
      border: 1px solid #ddd;
      background-color: #eee;
      color: #999;
      font-size: 11px;
      padding-top: 10px;
      text-align: center;
   }
</style>
<div id="contents_head">
   <h1>브랜드 목록</h1>

   <button class="btnstyle1 btnstyle1-success btnstyle1-sm m-l-10"
      onclick="window.open('https://docs.google.com/document/d/13Rlew5ROg1WcwaRbnRiSD0QpsinIHB4K4L5-Jqw2nWY/edit?usp=drive_link', '_blank');">
      브랜드 관리 매뉴얼 문서
   </button>

   <div id="head_write_btn">
      <button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="brandLlist.brandCreate()">
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
               Total : <span><b><?= number_format($pagination['total'] ?? 0) ?></b></span> &nbsp; | &nbsp;
               <span><b><?= $pagination['current_page'] ?? 1 ?></b></span> / <?= $pagination['last_page'] ?? 1 ?> page
            </ul>
            <ul class="m-l-10">
               <input type='text' name='search_value' id='search_value' value="<?= $_GET['search_value'] ?? '' ?>" placeholder="검색어" style="min-width: 200px;">
            </ul>
            <ul>
               <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" id="searchBtn">
                  <i class="fas fa-search"></i> 검색
               </button>
               <button type="button" class="btnstyle1 btnstyle1-sm" id="search_reset">
                  <i class="far fa-trash-alt"></i> 초기화
               </button>
            </ul>
            <ul class="right">
               <select name="sort_kind" id="sort_kind">
                  <option value="idx" <? if ($sort_kind == "idx") echo "selected"; ?>>등록순</option>
                  <option value="updated_at" <? if ($sort_kind == "updated_at") echo "selected"; ?>>수정순</option>
                  <option value="name" <? if ($sort_kind == "name") echo "selected"; ?>>이름순(국문)</option>
                  <option value="name_en" <? if ($sort_kind == "name_en") echo "selected"; ?>>이름순(영문)</option>
                  <option value="have_stock_count" <? if ($sort_kind == "have_stock_count") echo "selected"; ?>>보유상품수순</option>
                  <option value="product_count" <? if ($sort_kind == "product_count") echo "selected"; ?>>일반상품수순</option>
                  <option value="partner_count" <? if ($sort_kind == "partner_count") echo "selected"; ?>>공급사 상품수순</option>
               </select>
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
                        <th class="">관리 로고</th>
                        <th class="">브랜드 이름 (국문)</th>
                        <th class="">브랜드 이름 (영문)</th>
                        <th class="">일반<br>상품수</th>
                        <th class="">공급사<br>상품수</th>
                        <th class="">보유<br>상품수</th>
                        <th class="">수정일<br>등록일</th>
                        <th class="">쑈당몰 활성</th>
                        <th class="">매칭 카테고리</th>
                        <th class="">매칭 브랜드</th>
                        <th class="">노출로고<br>PC</th>
                        <th class="">노출로고<br>모바일</th>
                        <th class="">브랜드소개</th>
                        <th class="">평가등급</th>
                        <th class="">종합점수</th>
                        <th class="">수익성</th>
                        <th class="">상품력·차별성</th>
                        <th class="">고객 리스크</th>
                        <th class="">운영·공급 안정성</th>
                        <th class="">성장성</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     foreach ($brandList as $brand) {

                        if ($brand['BD_LOGO']) {
                           $img_path = '/data/brand_logo/' . $brand['BD_LOGO'];
                        }
                     ?>
                        <tr>
                           <td><input type="checkbox" name="" onclick="select_all()"></td>
                           <td class="text-center"><?= $brand['BD_IDX'] ?></td>
                           <td>
                              <button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="brandModify('<?= $brand['BD_IDX'] ?>')">관리</button>
                           </td>
                           <td>
                              <?php if ($brand['BD_LOGO']) { ?>
                                 <img src="<?= $img_path ?>" style="height:50px; border:1px solid #eee !important;">
                              <?php } else { ?>
                                 <!--
                                    <div class="no-image 50">No<br>image</div>
                                    -->
                              <?php } ?>
                           </td>
                           <td>
                              <b><?= $brand['BD_NAME'] ?></b>
                              <br>
                              <span class="list-memo-text"><?= $brand['bd_memo'] ?? '' ?></span>

                           </td>
                           <td><?= $brand['BD_NAME_EN'] ?? '' ?></td>
                           <td class="text-center">
                              <?php if ($brand['product_count'] > 0) { ?>
                                 <a href="/ad/prd/prd_db?s_brand=<?= $brand['BD_IDX'] ?>" target="_blank"><?= number_format($brand['product_count']) ?></a>
                              <?php } ?>
                           </td>
                           <td class="text-center">
                              <?php if ($brand['partner_count'] > 0) { ?>
                                 <a href="/admin/provider_product/list?s_brand=<?= $brand['BD_IDX'] ?>" target="_blank"><?= number_format($brand['partner_count']) ?></a>
                              <?php } ?>
                           </td>
                           <td class="text-center">
                              <?php if ($brand['have_stock_count'] > 0) { ?>
                                 <a href="/admin/product/product_stock?s_brand=<?= $brand['BD_IDX'] ?>&in_stock=have" target="_blank"><?= number_format($brand['have_stock_count']) ?></a>
                              <?php } ?>
                           </td>
                           <td>
                              <?= date('Y-m-d', strtotime($brand['updated_at'])) ?>
                              <br>
                              <?= date('Y-m-d', strtotime($brand['created_at'])) ?>
                           </td>
                           <td>
                              <?php if ($brand['bd_showdang_active'] == 'Y') { ?>
                                 <button type="button" id="" class="btnstyle1 btnstyle1-xs" onclick="window.open('https://showdang.co.kr/goods/brand_list.php?cateCd=<?= $brand['bd_matching_cate'] ?>', '_blank');">노출중 사이트보기</button>
                              <?php } ?>
                           </td>
                           <td>
                              <?php if ($brand['bd_matching_cate']) { ?>
                                 <a href="http://gdadmin.dnfix202439.godomall.com/goods/category_tree.php?cateCd=<?= $brand['bd_matching_cate'] ?>" target="_blank">
                                    <button type="button" id="" class="btnstyle1 btnstyle1-xs">#<?= $brand['bd_matching_cate'] ?></button>
                                 </a>
                              <?php } ?>
                           </td>
                           <td>
                              <?php if ($brand['bd_matching_brand']) { ?>
                                 <a href="http://gdadmin.dnfix202439.godomall.com/goods/category_tree.php?cateType=brand&cateCd=<?= $brand['bd_matching_brand'] ?>" target="_blank">
                                    <button type="button" id="" class="btnstyle1 btnstyle1-xs">#<?= $brand['bd_matching_brand'] ?></button>
                                 </a>
                              <?php } ?>
                           </td>
                           <td>
                              <?php if ( !empty($brand['bd_api_info']['logo']) ) { ?>
                                 <img src="https://showdang.co.kr/data/<?= $brand['bd_api_info']['logo'] ?>" style="height:50px; border:1px solid #eee !important;">
                              <?php } ?>
                           </td>
                           <td>
                              <?php if ( !empty($brand['bd_api_info']['logo_mobile']) ) { ?>
                                 <div style="background-color:#111; padding:5px;">
                                 <img src="https://showdang.co.kr/data/<?= $brand['bd_api_info']['logo_mobile'] ?>" style="height:20px; ">
                                 </div>
                              <?php } ?>
                           </td>

                           <td>
                              <?php if ( !empty($brand['bd_api_introduce']) ) { ?>
                                 <div style="max-width:300px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.4; max-height:2.8em; white-space:normal;">
                                    <?= $brand['bd_api_introduce'] ?>
                                 </div>
                              <?php } ?>
                           </td>

                           <td class="text-center">
                              <?php if (!empty($brand['brand_grade_computed'])) { ?>
                                 <span class="grade-badge grade-<?=$brand['brand_grade_computed']?>">
                                    <?=$brand['brand_grade_computed']?>
                                 </span>
                              <?php } else { ?>
                                 -
                              <?php } ?>
                           </td>

                           <td class="text-center"><?= $brand['brand_eval_total_score'] ?></td>
                           <td class="text-center"><?= $brand['brand_eval_profit_score'] ?></td>
                           <td class="text-center"><?= $brand['brand_eval_product_score'] ?></td>
                           <td class="text-center"><?= $brand['brand_eval_risk_score'] ?></td>
                           <td class="text-center"><?= $brand['brand_eval_ops_score'] ?></td>
                           <td class="text-center"><?= $brand['brand_eval_growth_score'] ?></td>
                           
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
   // 검색 파라미터 수집 공통 함수
   function getSearchParams(additionalParams) {
      var params = {};

      // 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
      var fields = {
         'sort_kind': $("#sort_kind").val(),
         'search_value': $("#search_value").val(),
      };

      // 추가 파라미터가 있으면 병합
      if (additionalParams) {
         fields = Object.assign(fields, additionalParams);
      }

      // 유효한 값만 params에 추가
      for (var key in fields) {
         if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '') {
            params[key] = fields[key];
         }
      }

      return params;
   }

   // 검색 파라미터로 페이지 이동
   function navigateWithParams(params) {
      // URL 쿼리 문자열 생성
      var queryString = Object.keys(params)
         .map(function(key) {
            return key + '=' + encodeURIComponent(params[key]);
         })
         .join('&');

      // 페이지 이동
      location.href = '/admin/brand/list' + (queryString ? '?' + queryString : '');
   }

   $("#search_reset").click(function() {
      var url = "?";
      window.location.href = url;
   });

   $("#sort_kind").change(function() {
      // 정렬 모드 추가하여 검색 파라미터 수집
      var params = getSearchParams({
         'sort_mode': $(this).val()
      });

      // 페이지 이동
      navigateWithParams(params);
   });

   $("#searchBtn").on('click', function() {
      // 검색 파라미터 수집
      var params = getSearchParams();

      // 페이지 이동
      navigateWithParams(params);
   });

   $("#search_value").on('keydown', function(event) {
      if (event.key === 'Enter') {
         event.preventDefault();
         $("#searchBtn").trigger('click');
      }
   });

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