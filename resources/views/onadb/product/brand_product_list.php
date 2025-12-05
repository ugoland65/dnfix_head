<div class="brand-title">

	<div class="brand-logo">
		<?php if( !empty($brandInfo['BD_LOGO']) ){ ?>
			<img src="/data/brand_logo/<?=$brandInfo['BD_LOGO']?>" />
		<?php } ?>
	</div>
	<div class="brand-info">
		<p class="brand-name-kr"><?=$brandInfo['BD_NAME'] ?? ''?></p>
		<p class="brand-name-en"><?=$brandInfo['BD_NAME_EN'] ?? ''?></p>
	</div>

</div>

<div class="top-wrap">
	<div class="total-count">
		<ul>
				Total <span><?=number_format($productList['total'])?></span>
		</ul>
	</div>
</div>

<div class="prd-list-wrap">
    <?php
        foreach($productList['data'] as $product){
            
            if( $product['CD_IMG2'] ){
                $img_path = '../../data/comparion/'.$product['CD_IMG2'];
                $img_class = "thum-icon";
            }else{
                $img_path = '../../data/comparion/'.$product['CD_IMG'];
                $img_class = "thum-no-icon img-blur";
            }

            $_brand_link = "/brand/".($product['CD_BRAND_IDX'] ?? '');
            
    ?>
        <ul>
            <div class="prd-list-box">
                <ul class="img">
                    <div class="thum"><a href="/pv/<?=$product['CD_IDX']?>" ><img src="<?=$img_path?>" class="<?=$img_class?>"></a></div>
                    <? if( $product['cd_tier'] == "1" || $product['cd_tier'] == "2" ){ ?>
                    <div class="tier-icon"><img src="/public/onadb/img/tier_<?=$product['cd_tier']?>.png" /></div>
                    <? } ?>
                </ul>
                <ul class="info">
                    <div class="pd-info">
                        <ul class="pd-name"><a href="/pv/<?=$product['CD_IDX']?>" ><?=$product['CD_NAME']?></a></ul>
                        <ul class="pd-brand-info"><a href="<?=$_brand_link?>"  ><?=$product['brand_name'] ?? ''?></a></ul>
                    </div>
                </ul>
            </div>
        </ul>
    <?php } ?>
</div>
<div class="pageing-wrap"><?=$paginationHtml?></div>