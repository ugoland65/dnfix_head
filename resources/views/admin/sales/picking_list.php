<style>
	.table-list tr td{
		padding: 5px !important;
	}
</style>
<div class="print-wrap">
    <table class="table-list" id="">
        <thead>
            <tr>
                <th>재고<br>코드</th>
                <th>이미지</th>
                <th>상품명</th>
                <th>렉코드<br>볼륨</th>
                <th>패킹<br>재거</th>
                <th>단일<br>상품</th>
                <th>세트<br>상품</th>
                <th>금일<br>출고</th>
                <th>현재<br>재고</th>
                <th>남는<br>재고</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pickingList as $row) { 

                $img_path = '/data/comparion/'.$row['CD_IMG'];
                $bar_code = substr($row['CD_CODE'], 0, -5);
                $bar_code_point = substr($row['CD_CODE'], -5);

            ?>
                <tr>
                    <td><?=$row['ps_idx']?></td>
                    <td width="90px"><img src="<?=$img_path?>" style="width:80px; "></td>
                    <td class="text-left">
                        <p><?=$row['brand_name']?></p>
                        <p>( <span><?=$bar_code?> <b style="color:#ff0000; font-size:16px;"><?=$bar_code_point?></b></span> )</p>
                        <p class="m-t-5" style="cursor:pointer;" onclick="onlyAD.prdView('<?=$row['ps_prd_idx']?>','info');"><b style="font-size:14px"><?=$row['CD_NAME']?></b></p>
                    </td>
                    <td style="width:80px;">
                        <b style="font-size:16px"><?=$row['ps_rack_code']?></b>
                        <?php if( $row['package_volume_level'] > 0 ){ ?>
                            <div style="font-size:15px; color:#0036ff; text-align:center;" class="m-t-3">v·<b><?=$row['package_volume_level']?></b></div>
                        <?php } ?>
                    </td>
                    <td style="width:40px; background-color:#f5f5f5;"><? if( $row['packageOut'] > 0 ){ ?><?=$row['packageOut']?><? } ?></td>
                    <td style="width:40px;"><?=$row['one']['qty']?></td>
                    <td style="width:40px;"><?=$row['set']['qty']?></td>
                    <td style="width:40px; background-color:#f5f5f5;"><b style="font-size:14px; color:#ff0000;"><?=$row['qty']?></b></td>
                    <td style="width:70px;"><?=number_format($row['ps_stock'])?></td>
                    <td style="width:70px; background-color:#f5f5f5;">
                        <b style="font-size:14px;"><?=number_format($row['ps_stock_sum'])?></b>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if( count($error) > 0 ){ ?>
        <div>
            <?php foreach($error as $row){ ?>
                <ul><?=$row?></ul>
            <?php } ?>
        </div>
    <?php } ?>

</div>

<style>
@media print {
	@page {
		margin-top: 30px;
		margin-bottom: 30px;
		margin-left: 10px;
		margin-right: 10px;
	}
	
	.print-wrap {
		position: relative;
		margin: 0;
		padding: 0;
	}
	
	.table-list {
		width: 100%;
		border-collapse: collapse;
	}
	
	.table-list thead {
		display: table-header-group;
	}
	
	.table-list tbody {
		display: table-row-group;
	}
	
	.table-list td img {
		width: 80px !important;
		max-width: 80px !important;
		height: auto !important;
	}
	
	/* 프린트 시에도 빨간색 유지 */
	b[style*="color:#ff0000"],
	b[style*="color: #ff0000"] {
		color: #ff0000 !important;
		-webkit-print-color-adjust: exact !important;
		print-color-adjust: exact !important;
	}
}
</style>

<script src="/admin2/js/common.js?ver=<?=time()?>"></script>