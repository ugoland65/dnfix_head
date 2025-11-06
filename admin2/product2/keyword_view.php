<?
$pageGroup = "product2";
$pageName = "keyword_view";

include "../lib/inc_common.php";

	
	$_mode = $mode ;
	$_serch_query = "where kw_id > 0";
	

	if(!$sort_mode){
		$sort_mode = "kw_id";
		
	}
	if($sort_mode == "kw_id"){
		$sort_word_color = "btnstyle1-success";
		$sort_id_color = "btnstyle1-primary";		
	}else{

		$sort_word_color = "btnstyle1-primary";
		$sort_id_color = "btnstyle1-success";
	}
	if(!$sort_updown){
		$sort_updown = "desc";
	}

	$total_count = wepix_counter("keyword", $_serch_query);

	$list_num = 120;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$query = "select * from keyword ".$_serch_query." order by ".$sort_mode." ".$sort_updown." limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	while($serch_list = wepix_fetch_array($result)){ 

		$array_keyword[] = $serch_list[kw_word];
		$array_id[] = $serch_list[kw_id];
	}

	$paging_url = _A_PATH_KEYWORD."?sort_mode=".$sort_mode."&sort_updown=".$sort_updown."&pn=";
	$_view_paging = publicPaging($_pn, $total_page, $list_num, $page_num, $paging_url);

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.ag-kind{ width:50px !important; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }
.save-btn-wrap{ z-index:300; padding:10px 10px; position:fixed; bottom:30px; right:30px; background-color:rgba(0,0,0,0.4); border:1px solid #000000; text-align:center; vertical-align:middle; }
.save-btn-wrap button{ }
</STYLE>
<script type='text/javascript'>
	function changeSort(value,kind){
		var sort_mode = "<?php echo $sort_mode; ?>";
		var sort_updown = "<?php echo $sort_updown; ?>";
		var sort = "";
		if(kind == 'A'){
			sort = "sort_updown="+value+"&sort_mode="+sort_mode;
		}else{
			sort = "sort_updown="+sort_updown+"&sort_mode="+value;
		}

		location.href='<?=_A_PATH_KEYWORD?>?'+sort;
		
	}
</script>

<div id="contents_head">
	<h1>키워드</h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
		<span class="count">Total <b><?=number_format($total_count)?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">

					<table class="table-list" style='width:1000px !important ; float:left; margin-right:60px;'>
						<tr>
							<th colspan='8'>키워드
							<?if($sort_updown == 'asc'){?>
							
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-xs" onclick="changeSort('desc','A');"><i class="fas fa-sort-up"></i></button>
							<?}else{?>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-xs" onclick="changeSort('asc','A');"><i class="fas fa-sort-down"></i></button>
							
							<?}?>
							
							<button type="button" id="show_type_all" class="btnstyle1 <?=$sort_word_color?> btnstyle1-xs" onclick="changeSort('kw_id','B');"><i class="fas fa-ad"></i></button>
							<button type="button" id="show_type_all" class="btnstyle1 <?=$sort_id_color?> btnstyle1-xs" onclick="changeSort('kw_word','B')"><i class="fas fa-list-ol"></i></button>
					

						</tr>

						<?for($i=0;$i<count($array_keyword);$i+=3){
							$a = $i; $b = $i+1; $c = $i+2;	$d = $i+3;
						?>
							<tr>
								<td style='width:21%;'><?=$array_keyword[$a]?></td>
								<td style='width:4%;'><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$array_id[$a]?>');"><i class="far fa-trash-alt"></i></button></td>
								<td style='width:21%;'><?=$array_keyword[$b]?></td>
								<td style='width:4%;'><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$array_id[$b]?>');"><i class="far fa-trash-alt"></i></button></td>
								<td style='width:21%;'><?=$array_keyword[$c]?></td>	
								<td style='width:4%;'><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$array_id[$c]?>');"><i class="far fa-trash-alt"></i></button></td>
								<td style='width:21%;'><?=$array_keyword[$d]?></td>	
								<td style='width:4%;'><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$array_id[$d]?>');"><i class="far fa-trash-alt"></i></button></td>
							</tr>
						<?}?>
					
					</table>

					<table class="table-list" style='width:400px !important;'>
						<tr>
							<th colspan='2'>키워드 등록</th>
						</tr>
						<tr>
							<th>키워드</th>
							<td><input name='keyword' id='keyword' value=''></td>
						</tr>
						<tr>
							<th colspan='2'><input type='button' onclick="regKeyword();" value='등록'/></th>
						</tr>
					
					</table>
				</div>
			</ul>
			
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>
	</div>
 </div>
<script type='text/javascript'>
function regKeyword(){


	var keyword = $("#keyword").val();

	$.ajax({
		type: "post",
		url : "<?=_A_PATH_PD_OK?>",
		data : { 
			a_mode : "newKeyWord",
			keyword : keyword
		},
		success: function(getdata) {
			makedata = getdata.split('|');
			ckcode = makedata[1];
			if(ckcode=="Processing_Complete"){
				alert('등록 완료');
				location.reload();
			}else if(ckcode=="Processing_cancel"){
				alert('키워드 중복');
			}
		}
	});
}

function goDel(key){


	$.ajax({
		type: "post",
		url : "<?=_A_PATH_PD_OK?>",
		data : { 
			a_mode : "delKeyWord",
			key : key
		},
		success: function(getdata) {
			makedata = getdata.split('|');
			ckcode = makedata[1];
			if(ckcode=="Processing_Complete"){
				alert('삭제 완료');
				location.reload();
			}
		}
	});
}
</script>

<?
include "../layout/footer.php";
exit;
?>