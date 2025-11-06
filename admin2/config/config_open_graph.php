<?
$pageGroup = "config";
$pageName = "config_open_graph";

include "../lib/inc_common.php";

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
</STYLE>
<div id="contents_head">
	<h1>개발자 설정</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">
			<form name='developerForm' id='developerForm' action='<?=_A_PATH_CONFIG_DEVELOPER_OK?>' method='post'>
			<input type="hidden" name="action_mode" value="openGraph">
			<table cellspacing="1" cellpadding="0" class="table-style">

				<tr>
					<th class="tds1">basics meta</th>
					<td class="tds2">
						<table cellspacing="1" cellpadding="0" class="table-style">
							<tr>
								<th class="tds1">Title</th>
								<td class="tds2">
									<input type='text' name='title' value="<?=_OPEN_GRAPH_TITLE?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">subject</th>
								<td class="tds2">
									<input type='text' name='subject' value="<?=_OPEN_GRAPH_SUBJECT?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">description</th>
								<td class="tds2">
									<input type='text' name='description' value="<?=_OPEN_GRAPH_DESCRIPTION?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">keywords</th>
								<td class="tds2">
									<input type='text' name='keywords' value="<?=_OPEN_GRAPH_KEYWORDS ?>" >
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<th class="tds1">og:meta</th>
					<td class="tds2">
						<table cellspacing="1" cellpadding="0" class="table-style">
							<tr>
								<th class="tds1">Title</th>
								<td class="tds2">
									<input type='text' name='og_site_name' value="<?=_OPEN_GRAPH_OG_SITE_NAME?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">site_name</th>
								<td class="tds2">
									<input type='text' name='og_title' value="<?=_OPEN_GRAPH_OG_TITLE?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">type</th>
								<td class="tds2">
									<input type='text' name='og_type' value="<?=_OPEN_GRAPH_OG_TYPE?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">description</th>
								<td class="tds2">
									<input type='text' name='og_description' value="<?=_OPEN_GRAPH_OG_DESCRIPTION?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">image</th>
								<td class="tds2">
									<input type='text' name='og_image' value="<?=_OPEN_GRAPH_OG_IMAGE?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">URL</th>
								<td class="tds2">
									<input type='text' name='og_url' value="<?=_OPEN_GRAPH_OG_URL?>" >
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<th class="tds1">tw:meta</th>
					<td class="tds2">
						<table cellspacing="1" cellpadding="0" class="table-style">
							<tr>
								<th class="tds1">Title</th>
								<td class="tds2">
									<input type='text' name='tw_title' value="<?=_OPEN_GRAPH_TW_TITLE?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">card</th>
								<td class="tds2">
									<input type='text' name='tw_card' value="<?=_OPEN_GRAPH_TW_CARD?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">description</th>
								<td class="tds2">
									<input type='text' name='tw_description' value="<?=_OPEN_GRAPH_TW_DESCRIPTION?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">image</th>
								<td class="tds2">
									<input type='text' name='tw_image' value="<?=_OPEN_GRAPH_TW_IMAGE?>" >
								</td>
							</tr>
							<tr>
								<th class="tds1">domain</th>
								<td class="tds2">
									<input type='text' name='tw_domain' value="<?=_OPEN_GRAPH_TW_DOMAIN?>" >
								</td>
							</tr>
						</table>
					</td>
				</tr>

				
			</table>
			</form>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">
<!-- 
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_MEMBER_A_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
 -->
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doSubmit();" > 
						<i class="far fa-check-circle"></i>
						수정하기
					</button>
				</ul>
			</div>
		
		</div>

	</div>
</div>

<script type="text/javascript"> 
<!-- 
// Submit
function doSubmit(){
	$("#developerForm").submit();
}
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>