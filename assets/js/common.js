var GC = function() {

	return {

		comma: function(str) {
			str = String(str);
			return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
		},
		uncomma: function(str) {
			str = String(str);
			return Number(str.replace(/[^\d]+/g, ''));
		},
		is_onlynumeric: function(v, o) {
			var chk = v.replace(/[^\,|^\.|^0(0)+|^0-9\.]/g, ''); //소수점 쉼표 가능
			if( v != chk ) { 
				$(o).val("");
				showAlert("warning", "숫자만 입력해주세요.", "alert2" );
				return false;
			}
		},
		commaInput: function(str, obj) {
			GC.is_onlynumeric(str, obj);
			str = String(GC.uncomma(str));
			//$(obj).val(str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,'));
			$(obj).val(str.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ','));
		},
		makeCommaInput: function(className) {
			$(className).keyup(function() {
				GC.commaInput($(this).val(), $(this));
			});
		},
		//GC.movePage
		movePage: function(url) {
			location.href=url;
		}

	};

}();


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
			alignMiddle: true,
			closeAnimation: 'zoom',
		});
	}else if( type == "alert2" ){
		$.alert({
			backgroundDismiss: true,
			icon: icon,
			title: title,
			content: msg,
			type: color,
			alignMiddle: true,
			closeAnimation: 'zoom',
		});
	}else if( type == "alert" ){
		$.alert({
			backgroundDismiss: true,
			title: false,
			content: msg,
			type: color,
			alignMiddle: true,	
			closeAnimation: 'zoom',
		});
	}

}

function toast(mode, title, msg, timer, position) {

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
	
}

function goGodoMall(code){
	window.open('https://showdang.co.kr/goods/goods_view.php?goodsNo='+code, '_blank');
}

function goSupplierProduct(site, code){
	if(site == 'mobe'){
		window.open('https://mobe.kr/product/view.asp?ref=1&seq='+code, '_blank');
	}else if(site == 'byedam'){
		window.open('https://www.bestoypn.co.kr/goods/goods_view.php?goodsNo='+code, '_blank');
	}else if(site == 'doradora'){
		window.open('https://doradora.kr/product/detail.html?product_no='+code, '_blank');
	}
}

function goSupplierProductEdit(id){
	window.open("https://dnetc01.mycafe24.com/api/supplierProductEdit?product_id="+id, "supplierProductEdit_"+id, "width=800,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

$(document).ready(function(){
	$('.comma-input').keyup(function(){
		GC.commaInput($(this).val(), $(this));
	});
});
