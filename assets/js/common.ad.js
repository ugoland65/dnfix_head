var clareCalendar = {
	monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
	dayNamesMin: ['일','월','화','수','목','금','토'],
	weekHeader: 'Wk',
	dateFormat: 'yy-mm-dd', //형식(20120303)
	autoSize: false, //오토리사이즈(body등 상위태그의 설정에 따른다)
	changeMonth: true, //월변경가능
	changeYear: true, //년변경가능
	showMonthAfterYear: true, //년 뒤에 월 표시
	buttonImageOnly: false, //이미지표시
	yearRange: '2000:2030' //1990년부터 2020년까지
};

$.datepicker.setDefaults({
/*
        dateFormat: 'yy-mm-dd',	//날짜 포맷이다. 보통 yy-mm-dd 를 많이 사용하는것 같다.
        prevText: '이전 달',	// 마우스 오버시 이전달 텍스트
        nextText: '다음 달',	// 마우스 오버시 다음달 텍스트
        closeText: '닫기', // 닫기 버튼 텍스트 변경
        currentText: '오늘', // 오늘 텍스트 변경
        monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],	//한글 캘린더중 월 표시를 위한 부분
        monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],	//한글 캘린더 중 월 표시를 위한 부분
        dayNames: ['일', '월', '화', '수', '목', '금', '토'],	//한글 캘린더 요일 표시 부분
        dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],	//한글 요일 표시 부분
        dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],	// 한글 요일 표시 부분
        showMonthAfterYear: true,	// true : 년 월  false : 월 년 순으로 보여줌
        yearSuffix: '년',	//
        showButtonPanel: true,	// 오늘로 가는 버튼과 달력 닫기 버튼 보기 옵션
//        buttonImageOnly: true,	// input 옆에 조그만한 아이콘으로 캘린더 선택가능하게 하기
//        buttonImage: "images/calendar.gif",	// 조그만한 아이콘 이미지
//        buttonText: "Select date"	// 조그만한 아이콘 툴팁
*/
	monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
	dayNamesMin: ['일','월','화','수','목','금','토'],
	weekHeader: 'Wk',
	dateFormat: 'yy-mm-dd', //형식(20120303)
	autoSize: false, //오토리사이즈(body등 상위태그의 설정에 따른다)
	changeMonth: true, //월변경가능
	changeYear: true, //년변경가능
	showMonthAfterYear: true, //년 뒤에 월 표시
	buttonImageOnly: false, //이미지표시
	yearRange: '2019:2030' //1990년부터 2020년까지

});

//
//loading('','white');
function loading(active, mode, msg) {

	if( !msg ) msg = "처리중"; 
	if( active == "off" ){
		if( mode == "white" ){
			$('#back_mask').hide().removeClass('white-back-mask');
		}else{
			$('#back_mask').hide();
		}
	}else{
		if( mode == "white" ){
			$('#back_mask').addClass('white-back-mask').html('<div id="loading"><ul><img src="/ad/img/loading11.gif" /></ul></div>').show();
		}else{
			$('#back_mask').html('<div id="loading"><ul><img src="/ad/img/loading8.gif" /></ul><ul>'+msg+'</ul></div>').show();
		}
	}

}

function showPopup(w, h, mode){
	var spWidth = ($(window).width() - (w*1))/2;
	var spHeight = ($(window).height() - (h*1))/2;
	$("#back_mask").fadeTo("300",1 ,function(){
		$('#popup_layer').css({'width':w+'px', 'height':h+'px', 'top':spHeight+'px', 'left':spWidth+'px'}).show();
		if( mode == "ajax" ){
			$('#popup_layer_body').show();
		}else{
			$('#popup_iframe').show();
			$('#popup_iframe').css({'width':w+'px', 'height':h+'px', 'top':spHeight+'px', 'left':spWidth+'px'});
		}
	});
}

function closedPopup(){
	$('#popup_iframe').hide();
	$('#popup_layer').hide();
	$("#back_mask").hide();
}

var pageingAjaxShow = function(){
	var pageingAjaxData = $("#hidden_pageing_ajax_data").html();
	$("#pageing_ajax_show").html(pageingAjaxData);
};

//AJAX 배열로 Explode할때 구분자 넣는 함수
function makeImplodeClassEach(word, classname, formtype){
	var returnValue = "";
	$("." + classname).each(function(index){
		if( index != 0 ) returnValue += word;
		if( formtype == "checkbox"){
			if( $(this).is(":checked") == true ){
				returnValue += $(this).val();
			}else{
				returnValue += "";
			}
		}else{
			returnValue += $(this).val();
		}
	});
	return returnValue;
}

//RGB 박스
function rgbBox(){
	$('.demo').each( function() {
		$(this).minicolors({
			control: $(this).attr('data-control') || 'hue',
			defaultValue: $(this).attr('data-defaultValue') || '',
			format: $(this).attr('data-format') || 'hex',
			keywords: $(this).attr('data-keywords') || '',
			inline: $(this).attr('data-inline') === 'true',
			letterCase: $(this).attr('data-letterCase') || 'lowercase',
			opacity: $(this).attr('data-opacity'),
			position: $(this).attr('data-position') || 'bottom',
			swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
			change: function(value, opacity) {
				if( !value ) return;
				if( opacity ) value += ', ' + opacity;
				if( typeof console === 'object' ) {
					console.log(value);
				}
			},
			theme: 'bootstrap'
		});
	});
}

//토스트 메세지
function fillWidth(elem, timer, limit) {
	if (!timer) { timer = 3000; }	
	if (!limit) { limit = 100; }
	var width = 1;
	var id = setInterval(frame, timer / 100);
		function frame() {
		if (width >= limit) {
			clearInterval(id);
		} else {
			width++;
			elem.style.width = width + '%';
		}
	}
};


function showAlert(title, msg, type, mode ) {

	if (!type) { type = "dialog"; }
	if (!mode) { mode = "warning"; }
	if (!title) { title = "warning"; }

	if( mode == "good" ){
		icon = "fas fa-check";
		color = "green";
	}else if( mode == "warning" ){
		icon = "fas fa-exclamation-triangle";
		color = "red";
	}

//theme: 'modern',

	if( type == "dialog" ){
		$.dialog({
			backgroundDismiss: true,
			icon: icon,
			title: title,
			content: msg,
			type: color,
		});
	}else if( type == "alert2" ){
		$.alert({
			backgroundDismiss: true,
			icon: icon,
			title: title,
			content: msg,
			type: color,
		});
	}else if( type == "alert" ){
		$.alert({
			backgroundDismiss: true,
			title: false,
			content: msg,
			type: color,
		});
	}

}

function toast(msg, timer) {
	if (!timer) { timer = 3000; }
	var $elem = $("<div class='toastWrap'><span class='toast'>" + msg + "<b></b><div class='timerWrap'><div class='timer'></div></div></span></div>");
	$("#toast").append($elem); //top = prepend, bottom = append
	$elem.slideToggle(100, function() {
		$('.timerWrap', this).first().outerWidth($elem.find('.toast').first().outerWidth() - 10);
		if (!isNaN(timer)) {
			fillWidth($elem.find('.timer').first()[0], timer);
			setTimeout(function() {
				$elem.fadeOut(function() {
					$(this).remove();
				});
			}, timer);			
		}
	});
}

$("#toast").on("click", "b", function() {
	$(this).closest('.toastWrap').remove();
})


function toast2(mode, title, msg, timer, position) {
	//https://codeseven.github.io/toastr/demo.html
	
	//success info warning error
	Command: toastr[mode](msg, title);

	if (!position) { position = "toast-top-right"; }
	if (!timer) { timer = 2000; }

	toastr.options = {
	  "closeButton": false,
	  "debug": true,
	  "newestOnTop": false,
	  "progressBar": true,
	  "positionClass": position,
	  "preventDuplicates": false,
	  "onclick": null,
	  "showDuration": "300",
	  "hideDuration": "1000",
	  "timeOut": timer,
	  "extendedTimeOut": "1000",
	  "showEasing": "swing",
	  "hideEasing": "linear",
	  "showMethod": "fadeIn",
	  "hideMethod": "fadeOut"
	}
	
/*
	if( mode == "success" ){
		toastr.success(title, msg);
	}else if( mode == "info" ){
		toastr.info(title, msg);
	}else if( mode == "warning" ){
		toastr.warning(title, msg);
	}else if( mode == "error" ){
		toastr.error(title, msg);
	}
*/
}

//GAME


$(function(){

	/*
	//툴팁
	$('[data-toggle="tooltip"]').tooltip();
	//달력
	*/

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}

	// 체크박스 전체선택
	$(document).on("click", ".check_box_all", function(){
		if( $(this).is(":checked")==true ){
			$(".checkSelect").each(function(){
				$(this).prop("checked", true);
				if( !$(this).closest('tr').hasClass('ckon') ){
					$(this).closest('tr').addClass('ckon');
				}
			});
		}else{
			$('.checkSelect').prop( 'checked', false ).closest('tr').removeClass('ckon');
		}
	});

});


