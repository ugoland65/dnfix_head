////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 보기
var orderSheet = function() {

	var osWindow;

	var C = function() {
	};

	return {

		init : function() {

		},

		// 신규 주문서 생성
		osReg: function( ) {

			var width = "1000px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "신규주문서 생성",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/order_sheet_info',
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

		// 주문서 보기
		osView : function(obj, idx, openmode) {

			if( !openmode ) openmode = "detail";
			var width = "1000px";

			osWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "주문서 상세보기",
				backgroundDismiss: false,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/order_sheet_info',
						data: { "idx":idx, "openmode":openmode },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},

				buttons: {
					savemode1: {
						text: '저장후 닫기 (부모창 새로고침)',
						btnClass: 'btn-red',
						action: function(){
							orderSheetReg.save('', 'closed');
						}
					},
					savemode2: {
						text: '저장후 남아있기',
						btnClass: 'btn-blue',
						action: function(){
							orderSheetReg.save('','stay');
							return false;
						}
					},
					cencle: {
						text: '닫기',
						action: function(){
						}
					}
				}


			});

		},

		// 주문서 리셋
		osViewReset : function(idx) {

			$.ajax({
				url: '/ad/ajax/order_sheet_info',
				data: { "idx":idx },
				type: "POST",
				dataType: "html",
				success: function(res){
					osWindow.setContent(res);
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

		// 주문서 삭제
		osDel : function( obj, idx, state ) {

			if( state != "1" ){
				showAlert("Error", "주문은 [작성중] 상태에서만 삭제가 가능합니다.", "alert2" );
				return false;
			}

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '주문서를 삭제합니다. 삭제시 복구되지 않습니다.<br> 주문은 [작성중] 상태에서만 삭제가 가능합니다.',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제하기',
						btnClass: 'btn-red',
						action: function(){

							$.ajax({
								url: "/ad/processing/order_sheet",
								data : { "a_mode" : "orderSheet_del", "idx" : idx  },
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										alert("삭제가 완료되었습니다.");
										location.href='/ad/order/order_sheet';
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
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});

		},

		// 주문서 출력팝업
		osPrint : function( idx, code, mode ){
			
			window.open("/admin2/product2/popup.order_sheet_print2.php?idx="+idx+"&code="+ code+"&mode="+ mode, "orderSGroup_"+ code, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");

		},

		osWindowView : function( idx, code, mode ) {
			window.open("/admin2/product2/popup.order_sheet_window.php?idx="+idx+"&code="+ code+"&mode="+ mode, "osWindow_"+ code, "width=1000,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
		},

		// 주문서 재고등록
		osStock : function( idx ){

			var width = "1000px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "재고 등록하기",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/order_sheet_stock',
						data: { "idx" : idx },
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

		// 디테일
		Detail: function( idx, oop_idx ) {
		
			$.ajax({
				url: "/ad/ajax/order_sheet_detail",
				data : { "idx" : idx, "open_oop_idx" : oop_idx },
				type: "POST",
				dataType: "html",
				success: function(html){
					$("#order_sheet_detail").html(html);
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

		},

		// 목록 조회
		List: function(mode, code) {

			$(".tabmenu-line a").removeClass('active');

			if( mode == "info" ){
				$("#info").addClass('active');
				$("#order_sheet_list").hide();
				$("#order_sheet_info").show();
				return false;
			}else{
				$("#order_sheet_list").show();
				$("#order_sheet_info").hide();
				if( mode == "연관" ){
					$("#relation").addClass('active');
				}else if( mode == "국내" ){
					$("#ko").addClass('active');
				}else if( mode == "수입" ){
					$("#import").addClass('active');
				}
			}

			$.ajax({
				url: "/ad/ajax/order_sheet_list",
				data : { "mode" : mode, "code" : code },
				type: "POST",
				dataType: "html",
				success: function(html){
					$("#order_sheet_list").html(html);
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 주문서 폼
var orderSheetForm = function() {

	var osFormWindow;
	var osFormGroupWindow;

	var C = function() {
	};

	return {
		
		init : function() {

		},

		reg : function(obj) {

			var width = "800px";

			osFormWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "신규 주문서폼 생성",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/order_sheet_form_info',
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

			var width = "1100px";

			osFormWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "주문서폼 관리",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/order_sheet_form_info',
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

		// 주문서 리셋
		viewReset : function( idx ) {

			$.ajax({
				url: '/ad/ajax/order_sheet_form_info',
				data: { "idx":idx },
				type: "POST",
				dataType: "html",
				success: function(res){
					osFormWindow.setContent(res);
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

		//주문서 폼 그룹
		groupView : function( idx ) {
		
			var width = "1200px";

			osFormGroupWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "주문서폼 그룹 관리",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/order_sheet_form_group_info',
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

		//주문서 폼 그룹  리셋
		groupViewReset : function( idx ) {
		
			$.ajax({
				url: '/ad/ajax/order_sheet_form_group_info',
				data: { "idx" : idx },
				type: "POST",
				dataType: "html",
				success: function(res){
					osFormGroupWindow.setContent(res);
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

	};

}();