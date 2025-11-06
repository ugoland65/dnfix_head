<?
$pageGroup = "booking";
$pageName = "booking_land_fee";

include "../lib/inc_common.php";    
    
    $search_sql = "where BKP_IDX > 0 ";
	$defult_sql = " and BKP_KIND !='CANCEL' ";
    $st_time = date("Y-m-d");
    $end_time = date("Y-m-d",strtotime ("+7 days"));
if($search_mode == 'ok'){
    $kind_num=0;
    $type_num=0;
    $area_num=0;
    for( $i=0; $i<count($booking_kind_array); $i++ ){
        if(${"search_check_box_".$booking_kind_array[$i]} == 'on'){
            if($kind_num == 0){
                $search_sql .= " and ( BKP_KIND = '".$booking_kind_array[$i]."'";
            }elseif($kind_num != 0){
                $search_sql .= " or BKP_KIND = '".$booking_kind_array[$i]."'";
            }
            $kind_num++;

            $search_box .= "&search_check_box_".$booking_kind_array[$i]."=on";
        }
        $kind_count = count($booking_kind_array)-1;
        if($kind_count == $i){
            $search_sql .= ")";
        }
    }
    
    
    for( $i=0; $i<count($booking_type_array2); $i++ ){
        if(${"search_check_box_".$booking_type_array2[$i]} == 'on'){
            if($type_num == 0){
                $search_sql .= " and ( BKP_TYPE = '".$booking_type_array2[$i]."'";
            }elseif($type_num != 0){
                $search_sql .= " or BKP_TYPE = '".$booking_type_array2[$i]."'";
            }
            $type_num++;
            $search_box .= "&search_check_box_".$booking_type_array2[$i]."=on";
        }
        $type_count = count($booking_type_array2)-1;
        if($type_count == $i){
            $search_sql .= ")";
        }
    }
    
    for( $i=0; $i<count($booking_area_array); $i++ ){
        $value = $booking_area_array[$i];
        if($i == 5){$value = "K-P";}
        if(${"search_check_box_".$booking_area_array[$i]} == 'on'){
            if($area_num == 0){
                $search_sql .= " and ( BKP_AREA = '".$value."'";
            }elseif($area_num != 0){
                $search_sql .= " or BKP_AREA = '".$value."'";
            }
            $area_num++;
            $search_box .= "&search_check_box_".$booking_area_array[$i]."=on";
        }
        $area_count = count($booking_area_array)-1;
        if($area_count == $i){
            $search_sql .= ")";
        }
    }

    if($kind_num == 0 || $area_num == 0 || $type_num == 0){
        $search_sql = "where BKP_IDX > 0 ";
    }

}

 	if( $search_st && $search_st !='') { 
        $st_date = strtotime($search_st);
        $dend2 = explode("-",$search_et);
        $end_date = mktime(23,59,59,$dend2[1],$dend2[2],$dend2[0]);
         
        $st_time = $search_st;
        $end_time = $search_et;
        if($search_date_kind == 'airport'){
            $search_sql .= " and BKP_START_DATE >= ".$st_date." and BKP_START_DATE <= ".$end_date;    
        }
        //." and BKP_START_DATE < ".$wepix_now_time
    }
    $sort_sql = " BKP_START_DATE asc ";
    
    $sort_agn_icon='▲';

    
    if($sort_kind == 'kind' || $sort_kind == 'loc'){
        if($order_by == '' || $order_by == 'desc'){
            $order_by ='asc';
        }else{
            $order_by ='desc';
        }
    }else{
        if($order_by == '' || $order_by == 'asc'){
            $order_by ='desc';
        }else{
            $order_by ='asc';
        }
    }
    
    if($sort_kind == 'cat'){
        $sort_sql = " BKP_TYPE ".$order_by;
        if($order_by == 'desc'){
            $sort_cat_icon='▲';
        }else{
            $sort_cat_icon='▼';
        }
            $sort_agn_icon=''; $sort_in_icon='';
    }elseif($sort_kind == 'agency'){
        $sort_sql = " BKP_AGENCY ".$order_by;
        if($order_by == 'desc'){
            $sort_agn_icon='▲';
        }else{
            $sort_agn_icon='▼';
        }
           $sort_cat_icon=''; $sort_in_icon='';
	}elseif($sort_kind == 'in'){
		$sort_sql = " BKP_START_DATE ".$order_by;
		if($order_by == 'desc'){
			$sort_in_icon='▲';
		}else{
			$sort_in_icon='▼';
		}
		 $sort_agn_icon=''; $sort_cat_icon='';
	}
	
    
	if( $land_fee_kind && $land_fee_kind !='') { 
		if($land_fee_kind == 'all'){

		}elseif($land_fee_kind == 'unpaid'){

		}elseif($land_fee_kind == 'rec'){
			$search_sql .= " and BKP_LAND_FEE_NOW > BKP_LAND_FEE";
		}else{
			$search_sql .= " and BKP_LAND_FEE_YN ='".$land_fee_kind."'";
		}
	}else{
		$land_fee_kind = "N";
		$search_sql .= " and BKP_LAND_FEE_YN ='".$land_fee_kind."'";
    
	}
    if( $search_area && $search_area !='') { 
        $search_sql .= " and BKP_AREA ='".$search_area."'";
	}
	if( $search_kind && $search_kind !='') { 
        if($search_kind == 'guest'){
            $search_sql .= " and BKP_GUEST like '%".$search_text."%'";
        }else if($search_kind == 'hotel'){
            $search_sql .= " and BKP_HOTEL like '%".$search_text."%'";
        }else if($search_kind == 'agency'){
            $search_sql .= " and BKP_AGNCY_TEXT like '%".$search_text."%'";
        }else if($search_kind == 'business'){
            $search_sql .= " and BKP_BUSINESS like '%".$search_text."%'";
        }else if($search_kind == 'manager'){
            $search_sql .= " and BKP_RESERVER like '%".$search_text."%'";
        }
    }
    //다중 ORDER BY = ORDER BY 칼럼명1 DESC , 칼럼명2 ASC ;

    $page_get = "search_area=".$search_area."&land_fee_kind=".$land_fee_kind."&search_bkp_type=".$search_bkp_type."&search_kind=".$search_kind."&search_st=".$search_st."&search_et=".$search_et."&search_date_kind=".$search_date_kind;

    
    $total_count = wepix_counter($db_t_BOOKING_PARENT, $search_sql." and BKP_KIND !='CANCEL'");
	$list_num = 70;
	if($search_mode == 'ok'){$list_num = 500;}
    $page_num = 10;
    

	// 전체 페이지 계산
	$total_page  = @ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

    
	

    $query = "select @ROWNUM := @ROWNUM + 1 AS RNUM,".$db_t_BOOKING_PARENT.".* from ".$db_t_BOOKING_PARENT.",(SELECT @ROWNUM:= 0) R ".$search_sql." and BKP_KIND !='CANCEL' order by ".$sort_sql." limit ".$from_record.", ".$list_num;

    //echo $query;

	$result = wepix_query_error($query);

	$page_link_text = "booking_land_fee_list.php?akind=".$akind."&".$page_get."&pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);
	/**                                Paging    End                       **/

//셀렉트  : 날짜 
//텍스트  : 게스트이름 , 호텔별 , 호텔 룸타입 , 에이전트 , 거래처


include "../layout/header.php";
?>

<script type="text/javascript"> 
<!-- 
bklistSave=function(){

};

bklistOpen=function(mode){
	var bklistOpenstate = $("#bklist_open").attr("state");
	//alert(bklistOpenstate);
	if( bklistOpenstate == "cloesd" ){
		$("#list_config_box").show();
		$("#bklist_open").attr("state","open");
		$("#bklist_open").attr("class","btnstyle1 btnstyle1-primary btnstyle1-sm");
	}else{
		$("#list_config_box").hide();
		$("#bklist_open").attr("state","cloesd");
		$("#bklist_open").attr("class","btnstyle1 btnstyle1-gary btnstyle1-sm");
	}

};

function autoHypendate(e, oThis ){
    var num_arr = [ 
            97, 98, 99, 100, 101, 102, 103, 104, 105, 96,
            48, 49, 50, 51, 52, 53, 54, 55, 56, 57
        ]
        
        var key_code = ( e.which ) ? e.which : e.keyCode;
        if( num_arr.indexOf( Number( key_code ) ) != -1 ){
        
            var len = oThis.value.length;
            if( len == 2 ) oThis.value += "-";
            if( len == 5 ) oThis.value += "-";
        
        }
  

}


bklistShow=function(mode){
	
	var bklistShowstate = $("#bklistshow_"+mode).attr("state");
	if( bklistShowstate == "checked" ){
		//활성시
		$(".bls_"+mode).each(function(){
			$(this).hide();
		});
		$("#bklistshow_"+mode).attr("state","NOT");
		$("#iclass_"+mode).attr("class","fa fa-square-o");
		setCookie('booking_list_show_'+mode, 'Off',1 ,'/', '<?=$_SERVER["SERVER_NAME"]?>');
	}else{
		//비활성화시
		$(".bls_"+mode).each(function(){
			$(this).show();
		});
		$("#bklistshow_"+mode).attr("state","checked");
		$("#iclass_"+mode).attr("class","fa fa-check-square-o");
		setCookie('booking_list_show_'+mode, 'ON',1 ,'/', '<?=$_SERVER["SERVER_NAME"]?>');
	}
	

};



searchSelect=function(kind){
	var searchUrl = "";
	var searchVal =  $("#"+ kind +" option:selected").val();

	if( kind == "booking_kind" ){
		searchUrl = "booking_land_fee_list.php?akind="+ searchVal +"&pn=<?=$pn?>";
    }
	document.location.href = searchUrl;
};
//--> 

<!-- 
	function doMatching(idx,num){
		window.open("booking_check_id.php?idx="+idx+"&num="+num, "childForm", "width=850, height=350, resizable = no, scrollbars = no");
	}
//--> 

function cf_search(value){
    location.href='/admin/booking/booking_land_fee_list.php?cf_search='+value;
}

checkboxOn=function(num){
   if($("#bkp_idx_" + num).is(':checked')){
		$("#trid_" + num).attr("bgcolor", "#ebf0f9");
   }else{
		$("#trid_" + num).attr("bgcolor", "#ffffff");
   }
};

checkboxOn2=function(num){
   if( $("#bkp_idx_" + num).is(':checked') ) {
	   $("#bkp_idx_" + num).attr("checked",false);
	   $("#trid_" + num).attr("bgcolor", "#ffffff");
   } else {
	   $("#bkp_idx_" + num).attr("checked",true);
	   $("#trid_" + num).attr("bgcolor", "#ebf0f9");
   }
};
    
   

 $( document ).ready( function() {
	$( '.check_box_all' ).click( function() {
		$( '.checkSelect' ).prop( 'checked', this.checked );
	} );
	$( '.checkSearchSt_all' ).click( function() {
		$( '.checkSearchSt' ).prop( 'checked', this.checked );
	});
	$( '.checkSearchAr_all' ).click( function() {
		$( '.checkSearchAr' ).prop( 'checked', this.checked );
	});
	$( '.checkSearchTy_all' ).click( function() {
		$( '.checkSearchTy' ).prop( 'checked', this.checked );
	});
} );


function land_array(kind){
    if( $(".checkSelect:checked").length == 0){
		if(kind == 'release'){
			alert('입금해제할 부킹을 선택해주세요.');
		}else{
			alert('지상비를 입금할 부킹을 선택해주세요.');
		}
		return false;
	}

	
	var send_array2 = "";
	$(".checkSelect:checked").each(function(index){
		if(index!=0){ send_array2 += ","; }
		send_array2 += $(this).val();
	});
         var form = document.form1;
         form.send_array.value=send_array2;
		 form.land_kind.value=kind;
         form.submit();

	
}
function search_detail(){
	var bklistOpenstate = $("#search_list").attr("state");
	if( bklistOpenstate == "cloesd" ){
		$("#search_config_box").show();
		$("#search_list").attr("state","open");
		$("#search_list").attr("class","btnstyle1 btnstyle1-primary btnstyle1-sm");
	}else{
		$("#search_config_box").hide();
		$("#search_list").attr("state","cloesd");
		$("#search_list").attr("class","btnstyle1 btnstyle1-gary btnstyle1-sm");
	}
}
</script> 
<STYLE TYPE="text/css">
#contents_body { overflow-y:auto !important; }
.booking-search{ position:relative;  }
.booking-search li.m-b-5 { margin-bottom:5px; }
.booking-search li select { height:28px; box-sizing:border-box; border:1px solid #999; background-color:#dee7f9; }
.booking-search li input[type="text"]{ height:28px; line-height:28px; padding:5px; box-sizing:border-box;  border:1px solid #999; background-color:#dee7f9; color:#000; }
#search_config_box{ position:absolute; top:-2px; left:133px; width:300px; height:270px; padding:5px; background-color:#ffffff; border:2px solid #444444; display:none;}
#search_config_box table { width:100%; 	border-spacing:0;border-collapse:collapse;padding:0;margin:0; box-sizing:border-box; }
#search_config_box table td{ border:1px dashed #999; width:33%; box-sizing:border-box; padding:4px; cursor:pointer;}
#list-box{ overflow-x:scroll !important; }
.table-style{ width:110%; }

.bls_checkbox{ width:25px !important; }
.bls_no{ width:40px !important; }
.bls_code{ width:70px !important; }
.bls_modify{ width:60px !important; }
.bls_status{ width:50px !important; }
.bls_location{ width:50px !important; }
.bls_category{ width:50px !important; }
.bls_group{ width:70px !important; }
.bls_guide{ width:90px !important; }
.bls_flight{ width:80px !important; }
.bls_hotel{ width:350px !important; }
.bls_guest{ width:250px !important;  }
.bls_option{ width:80px !important; }
.bls_agency{ width:80px !important; }
.bls_tourcost{ width:80px !important; }
.bls_booking_day{ width:80px !important; }
.bls_change_day{ width:80px !important; }
.bls_progress{ width:50px !important; }

.bl_tr{ background:#ffffff; cursor:pointer; }
.bl_tr2{ background:#f5f3f4; cursor:pointer; }
.bl_tr:hover, .bl_tr2:hover{ background:#dee7f9; }

</STYLE>
<iframe style="display:none;" name="HIddenActionFrame" id="HIddenActionFrame"></iframe>

<div id="contents_head">
	<h1><?=$title_booking_list?></h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">
   
   		<form name='search' method='post' action="<?=_A_PATH_BOOKING_LAND_FEE_LIST?>">
		<input type='hidden' name='search_mode' value='ok'>
		<input type='hidden' name='today' value='<?=$today?>'>

		<div class="search-wrap">
			<ul class="td search-img"></ul>
			<ul class="td search-body">

				<div class="booking-search">
					
					<li class="m-b-5"><button type="button" id="search_list" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="search_detail()"><i class="fa fa-plus"></i> Search Area Setting</button></li>
					<li>
					<select name="search_kind" id="search_kind" >
							<option value=''>Search type</option>
							<option value='guest' <? if( $search_kind == 'guest')  echo "selected"; ?>>Guest</option>
							<option value='hotel' <? if( $search_kind == 'hotel')  echo "selected"; ?>>Hotel/Room Type</option>
							<option value='agency'<? if( $search_kind == 'agency')  echo "selected"; ?>>Agency/Agenet</option>
							<option value='manager'<? if( $search_kind == 'manager')  echo "selected"; ?>>OP name</option>
						</select>
						<input type='text' style='width:150px;' id='search_text' name='search_text' value='<?=$search_text?>' placeholder="Please enter a search">
						<select name="land_fee_kind" id="land_fee_kind" >
							<option value=''>- 입금 현황 -</option>
							<option value='all' <? if( $land_fee_kind == 'all')  echo "selected"; ?>>전체</option>
							<option value='Y' <? if( $land_fee_kind == 'Y')  echo "selected"; ?>>완료</option>
							<option value='N' <? if( $land_fee_kind == 'N')  echo "selected"; ?>>미완료</option>
							<option value='rec' <? if( $land_fee_kind == 'rec')  echo "selected"; ?>>재확인</option>
							<option value='unpaid' <? if( $land_fee_kind == 'unpaid')  echo "selected"; ?>>미납</option>
						</select>
					</li>
					<li>
						<select name='search_date_kind' >
							<option value='airport' <? if( $search_date_kind == 'airport')  echo "selected"; ?>>Filgt</option>
						</select>
						<input type="text" id="search_st" name="search_st" value="<?=$search_st?>" class="date-input" style="padding:5px 5px 5px 27px; width:110px; background-position: 5px 5px; cursor:pointer;" readonly /> - <input type="text" id="search_et" name="search_et" value="<?=$search_et?>" class="date-input"  style="padding:5px 5px 5px 27px; width:110px; background-position: 5px 5px;   cursor:pointer;" readonly />
						&nbsp;
                    </li>

						<div id="search_config_box">
							<table>
								<tr>
									<th>Stauts</th>
									<th>Area</th>
									<th>Category</th>
								<tr>
								<tr>
								    <td><label><input type='checkbox' class="checkSearchSt" name='search_check_box_NEW' <?if($search_check_box_NEW == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>NEW</label></td>
									<td><label><input type='checkbox' class="checkSearchAr" name='search_check_box_KL' <?if($search_check_box_KL == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>> KL</label></td>
									<td><label><input type='checkbox' class="checkSearchTy" name='search_check_box_HM' <?if($search_check_box_HM == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>> HM</label></td>
								</tr>
								<tr>
									<td><label><input type='checkbox' class="checkSearchSt" name='search_check_box_AMEND' <?if($search_check_box_AMEND == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>AMEND</label></td>
									<td><label><input type='checkbox' class="checkSearchAr" name='search_check_box_KY' <?if($search_check_box_KY == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>KY</label></td>
									<td><label><input type='checkbox' class="checkSearchTy" name='search_check_box_FA' <?if($search_check_box_FA == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>FA</label></td>
								</tr>
								<tr>
								    <td><label><input type='checkbox' class="checkSearchSt" name='search_check_box_BLOCK' <?if($search_check_box_BLOCK == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>BLOCK</label></td>
									<td><label><input type='checkbox' class="checkSearchAr" name='search_check_box_K_P' <?if($search_check_box_K_P == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>K-P</label></td>
									<td><label><input type='checkbox' class="checkSearchTy" name='search_check_box_GF' <?if($search_check_box_GF == 'on'  || $search_mode != 'ok'){  echo "checked='true'";}?>>GF</label></td>
								</tr>
								<tr>
									<td><label><input type='checkbox' class="checkSearchSt" name='search_check_box_CANCEL' <?if($search_check_box_CANCEL == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>> CANCEL</td>
									<td><label><input type='checkbox' class="checkSearchAr" name='search_check_box_PK' <?if($search_check_box_PK == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>PK</label></td>
									<td><label><input type='checkbox' class="checkSearchTy" name='search_check_box_RO' <?if($search_check_box_RO == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>RO</label></td>
								</tr>
								<tr>
									<td><label><input type='checkbox' class="checkSearchSt" name='search_check_box_DUPE' <?if($search_check_box_DUPE == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>DUPE</label></td>
									<td><label><input type='checkbox' class="checkSearchAr" name='search_check_box_KR' <?if($search_check_box_KR == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>KR</label></td>
									<td><label><input type='checkbox' class="checkSearchTy" name='search_check_box_GP'<?if($search_check_box_GP == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>GP</label></td>
								</tr>
								<tr>
									<td></td>
									<td><label><input type='checkbox' class="checkSearchAr" name='search_check_box_BK' <?if($search_check_box_BK == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>BK</label></td>
									<td><label><input type='checkbox' class="checkSearchTy" name='search_check_box_PKG' <?if($search_check_box_PKG == 'on'  || $search_mode != 'ok'){  echo "checked='true'";}?>>PKG</label></td>
								</tr>

								<tr>
									<td></td>
									<td></td>
									<td><label><input type='checkbox' class="checkSearchTy" name='search_check_box_ICT' <?if($search_check_box_ICT == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>ICT</label></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td><label><input type='checkbox' class="checkSearchTy" name='search_check_box_INS' <?if($search_check_box_INS == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>INS</label></td>
								</tr>
							</table>
						</div><!-- #search_config_box -->
				</div>
			</ul>
            <ul class="td search-button">
				<input type="submit" value="Searching">
			</ul>
		</div>

   		</form>
		<div class="page-btn-wrap">
			<ul class="float-left">
				<li><span class="count">Total : <b><?=$total_count?></b></span></li>
			</ul>
			<ul class="float-left">
                <li class="m-l-10 float-left"><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="land_array('individual')"><i class="fa fa-credit-card"> 개 인통장</i></button></li>
                <li class="m-l-3 float-left"><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="land_array('corporate')"><i class="fa fa-credit-card-alt"> 법인통장</i></button></li>
                <li class="m-l-3 float-left"><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="land_array('money')"><i class="fa fa-money"></i> 현찰</li>
				<li class="m-l-3 float-left"><button type="button" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="land_array('release')"><i class="fa fa-times"></i> 컨펌해제</button></li>
			</ul>
		</div>



<div id="list-box">


	<div class="table-wrap">
		<form name='form1' method='post' action="<?=_A_PATH_BOOKING_OK?>">
		<input type='hidden' name='a_mode' value='land_array'>
       	<input type='hidden' name='send_array'>
		<input type='hidden' name='land_kind'>
		<input type='hidden' name='page_get' value='<?=$page_get?>'>
		   

        
<table cellspacing="1px" cellpadding="0" border="0" class="table-style booking-list">
                <tr>
                    <th width="25px"><input type="checkbox" name="check_box_all" class="check_box_all" onclick="select_all()"></th>
                    <th width="60px" >NO<br/>고유번호</th>
                    <th width="70px"><a href="?sort_kind=cat&order_by=<?php echo $order_by?>&<?php echo $page_get."&pn=".$pn?>" style="text-decoration:none">Category <?=$sort_cat_icon?></a></th>
                    <th width="110px"><a href="?sort_kind=in&order_by=<?php echo $order_by?>&<?php echo $page_get."&pn=".$pn?>" style="text-decoration:none"><i class="fa fa-plane" aria-hidden="true"></i> IN <?=$sort_in_icon?></a></th>
					<th width="300px">Hotel/State </th>
					<th width="80px" >Option</i></th>
					<th width="250px">Guest</th>
                    <th width="80px"><a href="?sort_kind=agncy&order_by=<?php echo $order_by?>&<?php echo $page_get?>" style="text-decoration:none">Agency <?=$sort_agn_icon?></a></th>
					<th width="80px" >지상비</i></th>
					<th width="80px" >지상비 입금액</i></th>
                    <th width="80px" >입금시간</i></th>
					<th width="80px">현재 입금상황</th>
					<th width="60px">관리</th>
					<th></th>
				</tr>
            
<tr>
	<td colspan="20" style="padding:0; border:none; height:3px;"></td>
</tr>
<?if($total_count != 0 ){?>
<?

while($list = wepix_fetch_array($result)){

    $st_lend_date = date("y-m-d",$wepix_now_time);
    if($list[BKP_LAND_FEE_CONFIRM_DATE] != 0){
        $st_lend_date =  date("y-m-d",$list[BKP_LAND_FEE_CONFIRM_DATE]);
    }
    //호텔 , 박수 , 룸 타입, 손님인스턴스를 가져와 '|' 기준으로 잘라서 배열로 저장
	$bkp_hoter_array = explode("│",$list[BKP_HOTEL]);
	$bkp_schedule_day = explode("│",$list[BKP_SCHEDULE_DAY]);
    $bkp_guest_instant = explode("│",$list[BKP_GUEST]);
    $bkp_start_date = date("d-M-y", $list[BKP_START_DATE]);

	$hoter_data = wepix_fetch_array(wepix_query_error("select * from "._DB_HOTEL."  where HOT_IDX = '".$bkp_hoter_array[0]."' "));
	$room_data = wepix_fetch_array(wepix_query_error("select * from "._DB_HOTEL_ROOM_TYPE."  where ROC_IDX = '".$bkp_room_type[0]."' "));
    $agency_data = wepix_fetch_array(wepix_query_error("select AG_COMPANY,AG_KIND,AG_BRANCH from "._DB_AGENCY."  where AG_IDX = '".$list[BKP_AGENCY]."' "));
    $agency_data2 = wepix_fetch_array(wepix_query_error("select AG_COMPANY,AG_KIND,AG_BRANCH from "._DB_AGENCY."  where AG_IDX = '".$list[BKP_BUSINESS]."' "));
	$hotel_data = explode("│",$list[BKP_HOTEL]);
	$bkp_hot_kind = explode("│",$list[BKP_HOT_BOOKING_STATE]);

    $schedata_count = wepix_counter($db_t_SCHEDULE, "where SC_BK_IDX = '".$list[BKP_IDX]."' ");
	

	if($list[BKP_LAND_FEE] == 0 || $list[BKP_LAND_FEE] == 1){
		$_ary_bkp_land_fee_text = explode("│",$list[BKP_LAND_FEE_TEXT]);
		$_view_land_fee = 0;
		for($a=0;$a<count($_ary_bkp_land_fee_text);$a++){
			$_ary2_bkp_land_fee_text = explode("/",$_ary_bkp_land_fee_text[$a]);

			if($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] != 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[2] *($_ary2_bkp_land_fee_text[3] * $_ary2_bkp_land_fee_text[1]);
			}elseif($_ary2_bkp_land_fee_text[2] != 0 && $_ary2_bkp_land_fee_text[3] == 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[2];
			}elseif($_ary2_bkp_land_fee_text[2] == 0 && $_ary2_bkp_land_fee_text[3] != 0){
				$_view_land_fee += $_ary2_bkp_land_fee_text[1] * $_ary2_bkp_land_fee_text[3];
			}
		}
	}else{
		$_view_land_fee = $list[BKP_LAND_FEE];
	}
?>
<input type='hidden' name='bkp_bkg_code[]' id="bkp_bkg_code_<?=$list[BKP_IDX]?>" value="<?=$list[BKP_BKG_CODE]?>">


<tr align="center" id="trid_<?=$list[BKP_IDX]?>" class="bl_tr" >

	<td ><input type="checkbox" name="key_check[]"  id="bkp_idx_<?=$list[BKP_IDX]?>" class="checkSelect" value="<?=$list[BKP_IDX]?>" onclick="checkboxOn('<?=$list[BKP_IDX]?>')"></td>
	<td class="bls_no"><?=$list[RNUM]?><br/><b><?=$list[BKP_IDX]?></b></td> 

    <td><b><?=$list[BKP_TYPE]?></b></td>
	<td class="bls_flight"><?=$bkp_start_date?><br/><b><?=strtoupper($list[BKP_START_FLIGHT])?></b></td>
	<td class="bls_hotel">
		<table cellspacing="1" cellpadding="0" class=" table-none-new booking-list-hotel">
		<?
			for( $i=0; $i<count($hotel_data); $i++ ){
                if($i < 2){
                $hotel_data2 = explode(":",$hotel_data[$i]);
                $hotel_data3 = explode("/",$bkp_schedule_day[$i]);
                
				if( $bkp_hot_kind[$i] == 0 ){
					$show_hotel_sate_style = "btnstyle1-gary";
					$show_hotel_sate_icon = "fa fa-angle-double-right";
				}elseif( $bkp_hot_kind[$i] == 1 ){
					$show_hotel_sate_style = "btnstyle1-success";
					$show_hotel_sate_icon = "fas fa-angle-double-right";
				}elseif( $bkp_hot_kind[$i] == 2 ){
					$show_hotel_sate_style = "btnstyle1-primary";
					$show_hotel_sate_icon = "fa fa-check-square-o";
				}elseif( $bkp_hot_kind[$i] == 3 ){
					$show_hotel_sate_style = "btnstyle1-danger";
					$show_hotel_sate_icon = "fas fa-ban";
				}else{
					$show_hotel_sate_style = "btnstyle1-danger";
				}
		?>
        	<tr>
                <?if($hotel_data2[1] != 'none'){?>
				<td class="p-3"><b><?=$hotel_data2[1]?></b> (<?=$hotel_data2[3]?>)</td>
                <td class="p-3 text-center"><?=$hotel_data2[4]?>RM <?=$hotel_data3[0]?>N</td>
				<?}?>
			</tr>
            <?}?>
		<?}?>
		</table>
	</td>
	
    <td class="bls_option">
    <?
    for($ho=0;$ho<count($bkp_hot_option);$ho++){
        $bkp_hot_option_array = explode(",",$bkp_hot_option[$ho]);
        for($h2=0;$h2<count($bkp_hot_option_array);$h2++){
            if($bkp_hot_option_array[$h2] != 'none'){?>
                <?=$bkp_hot_option_array[$h2]?><br/>
            <?}?>
        <?}?>
    <?}?>
    </td>
	<td class="bls_guest">
		<table cellspacing="1" cellpadding="0" class=" table-none-new booking-list-hotel">
<?
    for( $i=0; $i<2; $i++ ){
        $agi = explode("/",$bkp_guest_instant[$i]);
        $guest_num = $i+1;
        if($agi[0]=="Mr"){
            $guest_icon = "fas fa-male";
        }elseif($agi[0]=="Miss" OR $agi[0]=="Ms"){
            $guest_icon = "fas fa-female";
        }elseif($agi[0]=="CHD"){
            $guest_icon = "fas fa-child";
        }else{
            $guest_icon = "far fa-user";
        }

?>
			<tr>
                <?if($agi[1] != ''){?>
				<td width="30px" class="p-3 text-right f-s-14"><?=$agi[0]?></td>
				<td width="50px" class="p-3 text-center"><b><?=$agi[1]?></b></td>
				<td class="p-3"><?=strtoupper($agi[2])?></td>
                <?}?>
			</tr>
<? } ?>
            <?$total_haed = count($bkp_guest_instant) - 2;?>
            <?if($total_haed >= 1){?>
            <tr>
            <td class='p-3 text-right' colspan='3'>외 <?=$total_haed?>명</td>
			</tr>
            <?}?>
		</table>
	</td>
	<td class="bls_agency">
		<B><?=$agency_data[AG_COMPANY]?></B><br><?=$agency_data2[AG_COMPANY]?>
	</td>
    <td class="bls_tourcost"><?=number_format($_view_land_fee)?><br/> 
	<span style='color:red; font-size:11px;'><?=number_format($_view_land_fee - $list[BKP_LAND_FEE_NOW])?><br/>
	<?if($list[BKP_LAND_FEE_NOW] != 0){?>
	<span style='color:blue; font-size:11px;'><?=number_format($list[BKP_LAND_FEE_NOW])?></span>
	<?}?>
	</span></td>
	<td><input type='text' name='land_fee_now_<?=$list[BKP_IDX]?>'  value='<?=number_format($_view_land_fee - $list[BKP_LAND_FEE_NOW])?>' onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);"> </td>
    <td><input type="text" name="land_fee_date_<?=$list[BKP_IDX]?>" value="<?=$st_lend_date?>" onkeyup="autoHypendate(event, this)" onkeypress="autoHypendate(event, this)"  maxlength='8'/></td>
	<td><?=$list[BKP_LAND_FEE_YN]?></td>
	<td><input type="button" value="Modify" style="margin-bottom:3px;" onclick="bookingModify('<?=$list[BKP_IDX]?>');"></td>
	<td></td>

</tr>

<? } ?>
<?}else{ // total_count 숫자 0이하 ?>
    <tr>
        <td align='center' colspan='14'><b>등록된 부킹이 없습니다.</b></td>
    </tr>
<?}?>
			</table>
			</form>
		</div>

		</div><!-- #booking-list-box -->

<div class="paging-wrap"><?=$view_paging?></div>

</div>

</div>

<?
include "../layout/footer.php";
?>

<script type="text/javascript"> 
<!-- 

function bookingModify(key){
	location.href = "<?=_A_PATH_BOOKING_MODIFY_POPUP?>?key="+key+"&mode=modify";
}

function agency_cf(key, state) {

	$.ajax({
		url: "<?=_A_PATH_BOOKING_OK?>",
		data: {
			"action_mode":"agency_cf",
			"submit_mode":"bkt-list",
			"mokey":key,
			"state":state
		},
		type: "POST",
		dataType: "text",
		success: function(getdata){
			if (getdata){
				redatawa = getdata.split('|');
				ckcode = redatawa[1];
				ckmemo = redatawa[2];
				ckresult = redatawa[3];
				if(ckcode == "completion"){
					if(ckresult=="N"){
						$("#agency_confirm_"+key).html("<button type=\"button\" class=\"btnstyle1 btnstyle1-gary btnstyle1-xs m-t-3 width-50\" onclick=\"agency_cf('"+key+"', 'N');\"><i class=\"fas fa-angle-right\"></i> 대기</button>");
					}else{
						$("#agency_confirm_"+key).html("<button type=\"button\" class=\"btnstyle1 btnstyle1-primary btnstyle1-xs m-t-3 width-50\" onclick=\"agency_cf('"+key+"', 'N');\"><i class=\"fas fa-check-circle\"></i> 컨폼</button>");
					}
				}else{
					//에러
				}
			}
		},
		error: function(){
			//에러
		}
	});

}

//--> 
</script>