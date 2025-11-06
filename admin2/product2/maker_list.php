<?
$pageGroup = "product2";
$pageName = "maker_list";

include "../lib/inc_common.php";

	$_serch_query = " ";

	$total_count = wepix_counter(_DB_MAKER, $_serch_query);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$query = "select * from "._DB_MAKER." ".$_serch_query." order by MD_NAME asc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$page_link_text = _A_PATH_MAKER_LIST."?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.ag-kind{ width:50px !important; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }
</STYLE>
<script type='text/javascript'>
	function goDel(idx){
		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PD_OK?>",
			data : { 
				a_mode : "makerDel",
				idx : idx
			},
			success: function(getdata) {
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					alert('삭제완료');
					location.reload();
				}
			}
		});
	}
</script>
<div id="contents_head">
	<h1>제조사 목록</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_MAKER_REG?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div class="table-wrap">
			<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr>
						<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
						<th class="tds1">고유번호</th>
						<th class="tds1">이름</th>
						<th class="tds1">코드</th>
						<th class="tds1">관리</th>
					</tr>
				<?
				while($list = wepix_fetch_array($result)){

				
				?>
					<tr>
						<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$list[MD_IDX]?>" ></td>
						<td class="tds2"><?=$list[MD_IDX]?></td>
						<td class="tds2"><?=$list[MD_NAME]?></td>
						<td class="tds2" style="text-align:left !important;"><B><?=$list[MD_CODE]?><B/></td>
						<td class="tds2">
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="location.href='<?=_A_PATH_MAKER_REG?>?mode=modify&key=<?=$list[MD_IDX]?>'">Modify</button>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$list[MD_IDX]?>');"><i class="far fa-trash-alt"></i> DEL</button>
						</td>
					</tr>
				<? } ?>
			</table>
		</div>
		<div class="footer-padding"></div>
	</div>
</div>
<script type="text/javascript"> 
<!-- 

//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>