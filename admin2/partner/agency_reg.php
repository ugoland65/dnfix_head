<?
$pageGroup = "partner";
$pageName = "agency_reg";

include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_ag_idx = securityVal($key);

	if( $_mode == "modify" ){
		$agency_data = wepix_fetch_array(wepix_query_error("select * from "._DB_AGENCY." where AG_IDX = '".$_ag_idx."' "));

		$page_title_text = "에이전시 수정";
		$submit_btn_text = "에이전시 수정";
	}else{
		$page_title_text = "에이전시 등록";
		$submit_btn_text = "에이전시 등록";
	}

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
</STYLE>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">

			<form name='agencyForm' id='agencyForm' action='<?=_A_PATH_PARTNER_OK?>' method='post'>
			<? if( $_mode == "modify" ){ ?>
				<input type="hidden" name="a_mode" value="agencyModify">
				<input type="hidden" name="ag_idx" value="<?=$agency_data[AG_IDX]?>">
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="agencyNew">
			<? } ?>

			<table cellspacing="1" cellpadding="0" class="table-style">

				<tr>
					<th class="tds1">분류</th>
					<td class="tds2">
					<? if( $_mode == "modify" ){ ?>
						<input type="hidden" name="ag_kind" value="<?=$agency_data[AG_KIND]?>">
						<?  if( $agency_data[AG_KIND]=="A" ){ ?>
							본사
						<? }else{?>
							지사
						<? } ?>
					<? }else{ ?>
						<label><input type="radio" name="ag_kind" onclick="changeKind('A');" value="A" <? if( $agency_data[AG_KIND]=="A" OR $agency_data[AG_KIND] =="") echo "checked"; ?>> 본사</label>
						<label><input type="radio" name="ag_kind" onclick="changeKind('B');" value="B" <? if( $agency_data[AG_KIND]=="B"  ) echo "checked"; ?>> 지사</label>
					<? } ?>
					</td>
				</tr>

				<tr id="ag_kind_tr" <? if( $agency_data[AG_KIND] != "B" ){?>style="display:none;"<? } ?>>
					<th class="tds1">본사</th>
					<td class="tds2">
<select name='ag_co_idx' id='ag_co_idx' >
<?
$agency_result = wepix_query_error("select AG_IDX, AG_COMPANY from "._DB_AGENCY." where AG_DEL_YN='N' and AG_KIND='A' order by AG_IDX desc");
while($agency_list = wepix_fetch_array($agency_result)){
?>
<option value="<?=$agency_list[AG_IDX]?>" <? if( $agency_list[AG_IDX] == $agency_data[AG_CO_IDX] ) echo "selected"; ?>><?=$agency_list[AG_COMPANY]?></option>
<? } ?>
</select>
					</td>
				</tr>

				<tr>
					<th class="tds1">회사명</th>
					<td class="tds2"><input type='text' name='ag_company' id='ag_company' value="<?=$agency_data[AG_COMPANY]?>" ></td>
				</tr>

				<tr>
					<th class="tds1">메모</th>
					<td class="tds2">
						<textarea name='ag_memo' id='ag_memo' ><?=$agency_data[AG_MEMO]?></textarea>
					</td>
				</tr>

				<tr>
					<th class="tds1">노출</th>
					<td class="tds2">
						<label><input type="radio" name="ag_view"  value="Y" <? if( $agency_data[AG_VIEW]=="Y" OR $agency_data[AG_VIEW] =="" ) echo "checked"; ?>> 노출</label>
						<label><input type="radio" name="ag_view"  value="N" <? if( $agency_data[AG_VIEW]=="N" ) echo "checked"; ?>> 비노출</label>
					</td>
				</tr>

			</table>
			</form>

			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_PARTNER_AG_LIST?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록으로
					</button>
				</ul>
				<ul class="page-btn-right">
					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="doAgencySubmit();" > 
						<i class="far fa-check-circle"></i>
						<?=$submit_btn_text?>
					</button>
				</ul>
			</div>
		
		</div>

		<div style="height:60px;"></div>
	</div>
</div>

<script type="text/javascript"> 
<!-- 
//분류선택
function changeKind(kind){
	if(kind=="A"){
		$("#ag_kind_tr").hide();
	}else{
		$("#ag_kind_tr").show();
	}
}

// Submit
function doAgencySubmit(){
	var form = document.agencyForm;
	form.submit();
}

// 유효성 검사
  function sand(){

	    var form1 = document.agencyForm;
        //아이디 입력여부
  	    if (form1.ad_id.value == "") {
			alert("아이디를 입력하지 않았습니다.");
			form1.ad_id.focus();
			return false;
		}

		if(form1.new_pw.checked == true){
			//비밀번호 입력여부 체크
			if (form1.a_pw.value == "") {
				alert("비밀번호를 입력하지 않았습니다.");
				form1.a_pw.focus();
				return false;
			}
	 
			//비밀번호와 비밀번호 확인 일치여부 체크
			if (form1.a_pw.value != form1.a_pw2.value) {
				alert("비밀번호가 일치하지 않습니다")
				form1.a_pw.value = "";
				form1.a_pw.focus();
			   return false;
			} 
		}

		/*****이름 유효성 검사 *****/
        if (form1.ad_name.value == "") {
            alert("이름을 입력하지 않았습니다.");
            form1.ad_name.focus();
            return false;
        }

        if (form1.ad_name_eg.value == "") {
            alert("영문이름을 입력하지 않았습니다.");
            form1.ad_name_eg.focus();
            return false;
        }


        if (form1.ad_nick.value == "") {
            alert("닉네임을 입력하지 않았습니다.");
            form1.ad_nick.focus();
            return false;
        }

	return true;

  }

//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>