<div class="left-menu-title">
	<ul>AI 룰북</ul>
</div>
<div class="left-menu-mid-title">
	<ul>룰북 목록</ul>
</div>
<div class="left-menu-wrap">
	<ul <?php if( $pageNameCode == "rulebook_detail" && $idx == 1 ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/ai/rulebook/detail/1'">
		<li>상품상세 (일본수입)</li>
	</ul>
	<ul <?php if( $pageNameCode == "rulebook_detail" && $idx == 4 ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/ai/rulebook/detail/4'">
		<li>상품상세 (중국수입)</li>
	</ul>
	<ul <?php if( $pageNameCode == "rulebook_detail" && $idx == 6 ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/ai/rulebook/detail/6'">
		<li>상품상세 (공급사상품)</li>
	</ul>	
    <ul <?php if( $pageNameCode == "rulebook_detail" && $idx == 5 ) echo "class='leftMenuNow' "; ?> onclick="location.href='/admin/ai/rulebook/detail/5'">
		<li>고객센터 답변</li>
	</ul>
</div>