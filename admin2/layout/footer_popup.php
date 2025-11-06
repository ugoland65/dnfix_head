<script type="text/javascript"> 
<!-- 
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
//--> 
</script> 

<script src="/assets/js/common.js?ver=<?=$wepix_now_time?>"></script>

</body>
</html>