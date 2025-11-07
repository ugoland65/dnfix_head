<?
	// 변수 초기화
	$_s_text = $_GET['s_text'] ?? $_POST['s_text'] ?? "";
?>
<div id="contents_head">
	<h1>입출금 장부</h1>

	<div class="head-btn-wrap m-l-10">
		<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="ledge.bankStatementExcelUpload();" >입출금 엑셀등록</button>
	</div>

	<!-- 
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="moneyPlan.reg(this)" > 
			<i class="fas fa-plus-circle"></i>
			신규 등록
		</button>
	</div>
	-->

	<div class="head-right-fixed-wrap">

		<div>
			<ul class="filter-menu-title"><i class="fas fa-filter"></i> Filter</ul>
		</div>
		<div class="">

			<ul class="m-t-5">
				<div class="calendar-input" style="display:inline-block;"><input type="text" name="s_s_date"  id="s_s_date" value="" autocomplete="off" placeholder="시작일" ></div> ~ 
				<div class="calendar-input" style="display:inline-block;"><input type="text" name="s_e_date"  id="s_e_date" value="" autocomplete="off"placeholder="끝일" ></div>
			</ul>
			<ul class="m-t-5">

				<select name="s_kind" id="s_kind" >
					<option value="">종류</option>
					<option value="plus">입금</option>
					<option value="minus">출금</option>
				</select>

				<select name="s_bank" id="s_bank" >
					<option value="">은행</option>
					<option value="농협">농협</option>
					<option value="국민은행">국민은행</option>
					<option value="하나은행">하나은행</option>
				</select>

				<select name="s_state" id="s_state" >
					<option value="">확인</option>
					<option value="N">미확인</option>
					<option value="Y">확인</option>
				</select>

		</ul>
		<ul class="m-t-5">
			<input type='text' name='s_text' id='s_text' size='20' value="<?=$_s_text ?? '' ?>" placeholder="검색어" >
		</ul>
			<!-- 
			<ul class="m-t-5">
				SORT : 
				<select name="sort_kind" id="sort_kind" >
					<option value="stock" <? if( $_sort_kind == "stock" ) echo "selected";?>>재고 많은순</option>
					<option value="stock_asc" <? if( $_sort_kind == "stock_asc" ) echo "selected";?>>재고 적은순</option>
					<option value="idx" <? if( $_sort_kind == "idx" ) echo "selected";?> >상품 등록순</option>
					<option value="rack_code" <? if( $_sort_kind == "rack_code" ) echo "selected";?> >랙코드순</option>
				</select>
			</ul>
			-->

			<ul class="m-t-5">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-search-full" onclick="ledge.list('1');" > 
					<i class="fas fa-search"></i> 검색
				</button>
			</ul>

			<ul class="m-t-30">
				선택한 건수 일괄처리
			</ul>
			<ul class="m-t-5">
				<select name="batch_process_cate_kind" id="batch_process_cate_kind" onchange="ledge.batchProcessCate(this.value)" >
					<option value="">종류</option>
					<option value="수입">수입</option>
					<option value="지출">지출</option>
				</select>

				<select name="batch_process_cate" id="batch_process_cate" >
					<option value="">항목선택</option>
				</select>
			</ul>
			<ul class="m-t-5">
				<input type="text" name="batch_process_memo" id="batch_process_memo"  value="" placeholder="일괄처리 메모" >
			</ul>
			<ul class="m-t-5">
				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm p-t-5 p-b-5" onclick="ledge.batchProcess();" > 
					일괄처리 하기
				</button>
			</ul>
		</div>

	</div>

</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div id="list_wrap"></div>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script type="text/javascript"> 
<!-- 
var ledge = function() {

	var pageNum = "1";

	var C = function() {
	};

	return {

		init : function() {

		},

		list: function( pn ) {

			if( pn ){
				pageNum = pn;
			}else{
				pn = pageNum;
			}

			var sValue = $("#s_text").val();
			var sKind = $("#s_kind").val();
			var sBank = $("#s_bank").val();
			var sSdate = $("#s_s_date").val();
			var sEdate = $("#s_e_date").val();
			var sState = $("#s_state").val();

			$.ajax({
				url: "/ad/ajax/ledge_bankStatement_list",
				data: { "pn":pn, "s_text":sValue, "s_kind":sKind, "s_bank":sBank, "s_s_date":sSdate, "s_e_date":sEdate, "s_state":sState },
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

		bankStatementExcelUpload : function() {

			var width = "600px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "입출금 엑셀등록",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/ledge_bankStatementExcelUpload',
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

		bankStatementInfo : function( idx ) {

			var width = "600px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "입출금 상세내용",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/ledge_bankStatement_info',
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

		batchProcessCate : function( kind ) {

			$.ajax({
				url: "/ad/processing/accounting",
				data: { "a_mode":"ledgeCateLoad", "kind":kind },
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						
						var _option_text = "";

						for (var i = 0; i < res.ledge_cate.length; i++) {
							_option_text += "<option value='"+ res.ledge_cate[i].idx +"' >" + res.ledge_cate[i].name + "</option>";
						}
						//$("#batch_process_cate").append(_option_text);
						$("#batch_process_cate").html(_option_text);

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

				}
			});

		},

		batchProcess : function( ) {

			var checkboxCount = $(".checkSelect:checked").length;
			if( checkboxCount == 0 ){
				showAlert("Error", "선택된 건수가 없습니다.", "dialog" );
				return false; 
			}

			var chkArray = new Array();

			$("input[name='key_check[]']:checked").each(function() { 
				var tmpVal = $(this).val(); 
				chkArray.push(tmpVal);
			});

			var batch_process_cate_kind = $("#batch_process_cate_kind").val();
			var batch_process_cate = $("#batch_process_cate").val();
			var batch_process_memo = $("#batch_process_memo").val();

			$.ajax({
				url: "/ad/processing/accounting",
				data: { 
					"a_mode":"bankStatement_batch_process", 
					"chk_idx":chkArray,
					"batch_process_cate_kind":batch_process_cate_kind,
					"batch_process_cate":batch_process_cate,
					"batch_process_memo":batch_process_memo
				},
				type: "POST",
				dataType: "json",
				success: function(res){
					if ( res.success == true ){
						ledge.list();
						showAlert("Good", "수정완료 되었습니다.", "alert2", "good" );
						return false;
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

		}

	};

}();

ledge.list();
//--> 
</script> 