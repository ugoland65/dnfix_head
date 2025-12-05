<div class="page-title">
	<h1>브랜드</h1>
</div>
<div class="brand-list-wrap">
    <?php 
        foreach($brandList as $brand){

            if( $brand['BD_LOGO'] ){
                $img_path = '/data/brand_logo/'.$brand['BD_LOGO'];
            }
        
            $_brand_link = "/brand/".$brand['BD_IDX'];

    ?>
        <ul>
            <div class="brand-list-box">
                <ul class="img">
                    <div class="thum"><a href="<?=$_brand_link?>" ><img src="<?=$img_path?>" class="<?=$img_class?>"></a></div>
                </ul>
                <ul class="info">
                    <div class="brand-info">
                        <ul class="brand-name-kr"><a href="<?=$_brand_link?>" ><?=$brand['BD_NAME']?></a></ul>
                        <ul class="brand-name-en"><a href="<?=$_brand_link?>" ><?=$brand['BD_NAME_EN']?></a></ul>
                    </div>
                </ul>
            </div>
        </ul>
    <?php } ?>
</div>