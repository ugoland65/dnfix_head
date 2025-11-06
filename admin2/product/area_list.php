<?
$pageGroup = "product";
$pageName = "area_list";

include "../lib/inc_common.php";

	$_key = securityVal($key);

	$area_where = "where AREA_NATION_ISO = 'TH' ";
	$area_query = "select * from "._DB_AREA." ".$area_where." and AREA_KIND='L' order by AREA_IDX desc ";
	$area_result = wepix_query_error($area_query);

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>여행지역 관리</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<table cellspacing="0" cellpadding="0" border="0" class="table-style2 treewrap">	
			<tr>
				<td class="treewrap-menu">
					
					<div class="tree-left-wrap">
						<ul class="tree-big-menu" onclick="showLocation('add', 'TH', '')">태국 TH (신규등록)</ul>
						<ul class="tree-mid-wrap">
							<?
							while($area_list = wepix_fetch_array($area_result)){
							?>
							<li id="location_<?=$area_list[AREA_IDX]?>" class="tree-mid-menu" onclick="showLocation('view', '<?=$area_list[AREA_NATION_ISO]?>', '<?=$area_list[AREA_IDX]?>')"><?=$area_list[AREA_NAME]?> (<?=$area_list[AREA_CODE]?>)</li>
							<? } ?>
						</ul>
					</div>

				</td>
				<td class="treewrap-margin" style="border:none;"></td>
				<td class="treewrap-body">
					<div id="ajax_show">
					</div>
				</td>
			</tr>
		</table>

	</div>
</div>

<script type="text/javascript"> 
<!-- 
	var showLocation = function(wmode, isocode, idx){
       
		$(".tree-mid-menu").each(function(i){
			$(this).css({'font-weight':'','color':'','background-color':'' }); 
		});
		if( wmode == "view" && idx ){
			$("#location_"+idx).css({'font-weight':'bold', 'color':'#2070db' }); 
		}

		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PRODUCT_AREA_FORM?>",
			data : { wmode : wmode, isocode : isocode, idx : idx },
			success: function(oHtml) {
				$('#ajax_show').html(oHtml);
			}
		});
	};

<? if( $_key ){ ?>
	showLocation('view', '', '<?=$_key?>');
<? }else{ ?>
	showLocation('add', 'TH', '');
<? } ?>
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>