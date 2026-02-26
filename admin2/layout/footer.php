		</div>
	</div><!-- #wrap_table -->
</div><!-- #wrap -->

<div id="footer">
	Copyright ⓒ <b style="color:#247eff;"><?=defined('_A_GLOB_SITENAME') ? _A_GLOB_SITENAME : ''?></b> Corp. All Rights Reserved. <?=defined('_A_GLOB_COPYRIGHT') ? _A_GLOB_COPYRIGHT : ''?> (<?=defined('WS_VERSION_NUM') ? WS_VERSION_NUM : ''?>)
</div><!-- #footer  -->

<script src="/admin2/js/admin_footer.js?ver=<?=time()?>"></script>
<script type="text/javascript"> 



var koegAd = function() {

	return {
		init : function() {

		},
		brandModify : function(idx) {
			window.open("/admin2/product2/popup.brand_view.php?idx="+idx, "brand_view_"+idx, "width=1000,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
		},
	};

}();

const footerGlobal = (function() {

	/*
	const API_ENDPOINTS = {
		wishListDel: "/user2/proc/WishList/delWishlist",
	};
	*/

	return {
		// 초기화
		init() {
			console.log('wishList module initialized.');
		},
		comment(mode="", idx="", dayCode="") {
			var width = "1000px";
			openDialog("/ad/ajax/comment_main",{ mode, idx, dayCode  },"Comment",width); 
		},
	}

})();	

$(function(){
	
	$("#admin_language").mouseover(function(e) {
		$("#flag").show();
	}).mouseout(function(e) {
        $("#flag").hide();
	});

});
</script> 
<script src="/assets/js/common.js?ver=<?=$wepix_now_time?>"></script>

</body>
</html>