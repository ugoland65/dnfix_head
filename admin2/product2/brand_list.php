<?
$pageGroup = "product2";
$pageName = "brand_list";

include "../lib/inc_common.php";

	
	$_mode = $mode ;
	//$_serch_query = "where BD_KIND_CODE = '".$_mode."'";
	$total_count = wepix_counter(_DB_BRAND, $_serch_query);
	
	$list_num = 300;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$query = "select * from "._DB_BRAND." ".$_serch_query." order by BD_NAME asc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$page_link_text = _A_PATH_BRAND_LIST."?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);



include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.ag-kind{ width:50px !important; }
.ag-name{ width:200px !important; }
.ag-sub-count{ width:40px !important; }
.ag-sub-view{ width:50px !important; }
.save-btn-wrap{ z-index:300; padding:10px 10px; position:fixed; bottom:30px; right:30px; background-color:rgba(0,0,0,0.4); border:1px solid #000000; text-align:center; vertical-align:middle; }
.save-btn-wrap button{ }
.brand-img{}
.brand-img img{ width:50px; }
</STYLE>
<script type='text/javascript'>
	function goDel(idx){
		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PD_OK?>",
			data : { 
				a_mode : "brandDel",
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

function goSortList(mode){
	location.href='<?=_A_PATH_BRAND_LIST?>?mode='+mode;
}
</script>
<div id="contents_head">
	<h1>브랜드 목록</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_BRAND_REG?>?bd_kind_code=<?=$_mode?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div class="list-top-btn-wrap">
			<button type="button" id="" class="btnstyle1 btnstyle1-sm" style="margin-left:15px;width:120px;" onclick="goSortList('ONAHOLE')">ONAHOLE</button>
			<button type="button" id="" class="btnstyle1 btnstyle1-sm" style="margin-left:5px;width:80px;" onclick="goSortList('REALDOLL')">Real Doll</button>
			<button type="button" id="" class="btnstyle1 btnstyle1-sm" style="margin-left:5px;width:80px;" onclick="goSortList('WOMAN')">WOMAN</button>

			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm" style="width:150px; margin-left:250px;" onclick="changeSort()"><i class="fas fa-sort"></i> 전체 순서변경</button>
		</div>

		<div class="table-wrap">
			<div class="btn-wrap">
				<div class="save-btn-wrap">
					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveTop2()" style="width:90px" > <i class="fas fa-chevron-circle-up"></i> 맨위</button>
					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveUpItem2()" style="width:90px" > <i class="fas fa-chevron-circle-up"></i> UP</button>
					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveDownItem2()" style="width:90px" > <i class="fas fa-chevron-circle-down"></i> DOWN</button>
					<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveBottom2()" style="width:90px" > <i class="fas fa-chevron-circle-down"></i> 맨아래</button>
				</div> 
			</div>
			<form name='sortFrom' action='<?=_A_PATH_PD_OK?>' method='post' style= 'margin-left:15px;'>
				<input type="hidden" name="a_mode" value="changeSortBrand">
				<input type='hidden' name='sort_mode' value='<?=$_mode?>'>
				
				<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr  id="<?=$list[BD_SORT]?>_<?=$list['BD_IDX']?>">
						<th class="list-checkbox tds1"><input type="checkbox" name="" onclick="select_all()"></th>
						<th>고유번호</th>
						<th>순서</th>
						<th>로고</th>
						<th>이름(국문)</th>
						<th>이름(영문)</th>
						<th>그룹</th>
						<th>매칭코드</th>
						<th>코드</th>
						<th>활성</th>
						<th>리스트</th>
						<th>사이트</th>
						<th>관리</th>
					</tr>
				<?
			
				while($list = wepix_fetch_array($result)){
			
				?>
					<tr id="<?=$list[BD_SORT]?>">
						<td class="list-checkbox tds2">
							<input type='hidden' name='bd_idx[]' value='<?=$list['BD_IDX']?>'>
							<input type="radio" name="chk" id="radio_<?=$list[BD_SORT]?>" value="<?=$list[BD_SORT]?>" onclick="chkSelect('<?=$list[BD_SORT]?>')" />
						</td>
						<td><?=$list['BD_IDX']?></td>
						<td><?=$list[BD_SORT]?></td>
						<td class="brand-img"><img src="/data/brand_logo/<?=$list[BD_LOGO]?>" alt=""></td>
						<td><B><?=$list['BD_NAME']?></B></td>
						<td><?=$list['BD_NAME_EN']?></td>
						<td><?=$list['BD_NAME_GROUP']?> / <?=$list['BD_NAME_EN_GROUP']?></td>
						<td><?=$list['bd_cate_no']?></td>
						<td><?=$list[BD_KIND_CODE]?></td>
						<td><B><?=$list[BD_ACTIVE]?><B/></td>
						<td><B><?=$list[BD_LIST_ACTIVE]?><B/></td>
						<td><B><a href="http://<?=$list[BD_DOMAIN]?>" target="_blank"><?=$list[BD_DOMAIN]?></a><B/></td>
						<td>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="location.href='<?=_A_PATH_BRAND_REG?>?mode=modify&key=<?=$list['BD_IDX']?>'">수정</button>
							<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="goDel('<?=$list['BD_IDX']?>');"><i class="far fa-trash-alt"></i> 삭제</button>
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

function changeSort(){
	var form = document.sortFrom;
	form.submit();
}
function moveUpItem(obj) {     
    var idStr = '#' + obj;
    var prevHtml = $(idStr).prev().html();
    if( prevHtml  == null) {
        alert("최상위 리스트입니다!");
        return;
    }
    var prevobj = $(idStr).prev().attr("id");
    var currobj = $(idStr).attr("id");
    var currHtml = $(idStr).html();
    $(idStr).html(prevHtml);//값 변경 
    $(idStr).prev().html(currHtml);
    $(idStr).prev().attr("id","TEMP_TR");//id 값도 변경
    $(idStr).attr("id",prevobj);
    $("#TEMP_TR").attr("id",currobj);
	$("#radio_"+ obj).attr("checked",true);
	chkSelect(obj);
}

 

function moveDownItem(obj) {     
    var idStr = '#' + obj;
    var nextHtml = $(idStr).next().html();
    if( nextHtml  ==  null) {
        alert("최하위 리스트입니다!");
        return;
    }
    var nextobj = $(idStr).next().attr("id");
    var currobj = $(idStr).attr("id");
    var currHtml = $(idStr).html();
    $(idStr).next().html(currHtml);
    $(idStr).html(nextHtml);//값 변경 
    $(idStr).next().attr("id","TEMP_TR");//id 값도 변경
    $(idStr).attr("id",nextobj);
    $("#TEMP_TR").attr("id",currobj);
	$("#radio_"+ obj).attr("checked",true);
	chkSelect(obj);
}

function moveTop2() {
	var obj = $('input:radio[name=chk]:checked').val(); 
    var idStr = '#' + obj;
	$(idStr).closest('table').find('tr:eq(1)').before($(idStr));
}

function moveBottom2() {
	var obj = $('input:radio[name=chk]:checked').val(); 
    var idStr = '#' + obj;
	$(idStr).closest('table').find('tr:last').after($(idStr));
}

function moveUpItem2() {
	var obj = $('input:radio[name=chk]:checked').val(); 
    var idStr = '#' + obj;
    var prevHtml = $(idStr).prev().html();
    if( prevHtml  ==  null) {
        alert("최상위 리스트입니다!");
        return;
    }
    var prevobj = $(idStr).prev().attr("id");
    var currobj = $(idStr).attr("id");
    var currHtml = $(idStr).html();
    $(idStr).html(prevHtml);//값 변경 
    $(idStr).prev().html(currHtml);
    $(idStr).prev().attr("id","TEMP_TR");//id 값도 변경
    $(idStr).attr("id",prevobj);
    $("#TEMP_TR").attr("id",currobj);
	$("#radio_"+ obj).attr("checked",true);
}


function moveDownItem2() {   
	var obj = $('input:radio[name=chk]:checked').val(); 
    var idStr = '#' + obj;
    var nextHtml = $(idStr).next().html();
    if( nextHtml  ==  null) {
        alert("최하위 리스트입니다!");
        return;
    }
    var nextobj = $(idStr).next().attr("id");
    var currobj = $(idStr).attr("id");
    var currHtml = $(idStr).html();
    $(idStr).next().html(currHtml);
    $(idStr).html(nextHtml);//값 변경 
    $(idStr).next().attr("id","TEMP_TR");//id 값도 변경
    $(idStr).attr("id",nextobj);
    $("#TEMP_TR").attr("id",currobj);
	$("#radio_"+ obj).attr("checked",true);
}

function pdSelctFinal(){
	var finalPdKeyArray = finalPdKeyCheck.join("/");

	if(mpsPd != ""){
		mpsPd += "/"+finalPdKeyArray;
	}else{
		mpsPd += finalPdKeyArray;
	}

	//alert(mpsPd);
	closedPopup();
	showMpsPdList();
	//alert(finalPdKeyArray);
}



$(function(){

	var content22 = '이페이지 (브랜드 리스트)는 곧 폐기될 예정입니다.<br>신규 브랜드 생성 및 관리는 상품관리 v.3의 브랜드 관리를 이용해주세요.'
		+ '<br>이페이지는 v.3에서 오류 발견으로 인하여 업무처리 문제시에만 사용해 주세요.'
		+ '<br>v.3 오류 발견 시 별도로 보고해 주세요.';

	$.confirm({
		boxWidth : "500px",
		useBootstrap : false,
		icon: 'fas fa-exclamation-triangle',
		title: '공지',
		content: content22,
		type: 'red',
		typeAnimated: true,
		closeIcon: true,
		buttons: {
			somethingElse: {
				text: '상품관리 v.3 브랜드 관리로 이동',
				btnClass: 'btn-red',
				action: function(){
					location.href='/ad/prd/brand';
				}
			},
			cencle: {
				text: '이 페이지 사용',
				action: function(){
				}
			}
		}
	});

});
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>