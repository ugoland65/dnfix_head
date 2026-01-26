// 정수형 숫자에서 Comma 제거
function unComma_int(input) {
   var inputString = new String;
   var outputString = new String;
   var outputNumber = new Number;
   var counter = 0;
   if (input == '')
   {
	return 0
   }
   inputString=String(input);
   outputString='';
   for (counter=0;counter <inputString.length; counter++)
   {
      outputString += (inputString.charAt(counter) != ',' ?inputString.charAt(counter) : '');
   }
   outputNumber = parseFloat(outputString);
   return (outputNumber);
}


// 정수형 천단위 콤마 삽입
function Comma_int(input, obj) {
  input = unComma_int(input);

  var inputString = new String;
  var outputString = new String;
  var counter = 0;
  var decimalPoint = 0;
  var end = 0;
  var modval = 0;

  inputString = input.toString();
  outputString = '';
  decimalPoint = inputString.indexOf('.', 1);

  if(decimalPoint == -1) {
     end = inputString.length - (inputString.charAt(0)=='0' ? 1:0);
     for (counter=1;counter <=inputString.length; counter++)
     {
        var modval =counter - Math.floor(counter/3)*3;
        outputString = (modval==0 && counter <end ? ',' : '') + inputString.charAt(inputString.length - counter) + outputString;
     }
  }
  else {
     end = decimalPoint - ( inputString.charAt(0)=='-' ? 1 :0);
     for (counter=1; counter <= decimalPoint ; counter++)
     {
        outputString = (counter==0  && counter <end ? ',' : '') +  inputString.charAt(decimalPoint - counter) + outputString;
     }
     for (counter=decimalPoint; counter < decimalPoint+3; counter++)
     {
        outputString += inputString.charAt(counter);
     }
 }
    return (outputString);
}


// 숫자체크
function is_onlynumeric( v, o ) {
  var chk = v.replace(/[^,0-9]/g,'');
  if( v != chk ) { window.alert('숫자만 입력해주세요.'); o.value = chk; return false; }
}

function setCookie(name, value, expires, path, domain, secure) {

 secure = (secure == null) ? false : secure;
 var exdate=new Date();
 exdate.setDate(exdate.getDate() + expires);
 document.cookie = name + "=" + escape(value)
 + ((expires == null) ? "" : ("; expires=" + exdate.toUTCString()))
 //+ ((path == null) ? "path=/" : ("; path=" + path))
 + ((path == null) ? "path=/" : ("; path=/"))
 + ((domain == null) ? "" : ("; doamin=" + domain))
 + ((secure == true) ? "; secure" : "");

}


var onlyAD = function() {

	var osWindow;

	//상품 창 prd_provider_info
	function prdProviderQuick(idx, vmode){
		if( vmode == undefined ) vmode = "info"; 
		window.open("/ad/ajax/prd_provider_info?prd_idx="+ idx +"&vmode="+vmode, "prdProviderQuick_"+idx, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
	}

	return {

		init : function() {

		},
		prdProviderQuick,

		// 상품 상세 보기
		prdView : function( idx, vmode = 'info', prd_mode = "basic" ) {

			var url = "/ad/ajax/prd_info/"+ idx +"?vmode="+vmode;
			if( prd_mode == "stock" ) {
				url = "/ad/ajax/prd_info/"+ idx +"?vmode="+vmode+"&prd_mode=stock";
			}
			window.open(url, "prd_quick_"+idx, "width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
		},

		// 상품 상세 테스트
		prdViewTest : function( idx ) {
			if( vmode == undefined ) vmode = "comparison"; 
			window.open("/admin2/comparison/popup.comparison_modify.php?idx="+ idx +"&vmode="+vmode, "comparison_quick_"+idx, 
				"width=1270,height=830,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
		},

		staffHolidayView : function( idx ) {

			var width = "600px";

			$.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "휴가/월차/반차 내용보기",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/staff_holiday_view',
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

		// 주문서 보기
		orderSheetView : function( idx, openpage ) {

			var width = "1000px";

			osWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "주문서 상세보기",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/order_sheet_info',
						data: { "idx":idx, "openpage":openpage },
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
		orderSheetReset : function(idx, openpage) {

			$.ajax({
				url: '/ad/ajax/order_sheet_info',
				data: { "idx":idx, "openpage":openpage },
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
	};

}();

var adminLayout = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		
		//사이트 아이콘으로 메뉴 호출
		sideIcon : function( mode ) {

			/*
			$("#wrap_left_icon ul").each(function(){
				$(this).attr("class", "");
			});
			$("#wli_"+ mode).attr("class", "active");
			*/

			if( $("#wrap_left").hasClass("display-none") ) {
				 $("#wrap_left").removeClass('display-none');
			} 

			$.ajax({
				type: "post",
				url : "/admin2/skin/skin.menu_"+ mode +".php",
				data : { quickmode : "on" },
				success: function(shtml) {
					$('#wrap_left_body').html(shtml);
				}
			});

		}
	};

}();