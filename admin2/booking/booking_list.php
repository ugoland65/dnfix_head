<?
$pageGroup = "booking";
$pageName = "booking_list";

include "../lib/inc_common.php";
	
	$_mode = securityVal($mode);
	$_search_mode = securityVal($search_mode);
	$_search_kind = securityVal($search_kind);
	$_search_st = securityVal($search_st);
	$_search_et = securityVal($search_et);
	$_search_date_kind = securityVal($search_date_kind);
	$_search_text = securityVal($search_text);
	$_pn = securityVal($pn);
	$_sort_kind = securityVal($sort_kind);
	$_order_by = securityVal($order_by);
	$_calendar_mode = securityVal($calendar_mode);
	

	$bk_where_sql = "where BKP_IDX > 0 ";
	$bk_sort_sql = " BKP_START_DATE asc ";



    if( $_search_st=="" AND $_search_et=="" ){

        $_search_st = date("Y-m-d",$wepix_now_time);
        $_search_et  = date("Y-m-d",strtotime ("+10 days"));
        $st_date = strtotime($_search_st);
        $dend2 = explode("-",$_search_et);
        $end_date = mktime(23,59,59,$dend2[1],$dend2[2],$dend2[0]);
		$bk_where_sql .= " and BKP_START_DATE >= ".$st_date." and BKP_START_DATE <= ".$end_date;

    }elseif( $_search_st > 0 ){

		$_show_start_date = strtotime($_search_st);
		if( $_search_et > 0 ){
			$_show_end_date = strtotime($_search_et);
		}else{
			$_show_end_date = $_show_start_date+((60*60*24)*10);
			$_search_et = date("Y-m-d",$_show_end_date);
		}
		$bk_where_sql .= " and BKP_START_DATE >= ".$_show_start_date." and BKP_START_DATE <= ".$_show_end_date;

		$bk_sort_sql = " BKP_START_DATE asc ";
	}
	
	$booking_type_array2 = array("HM", "FA", "GP", "RO", "GF", "PKG", "ICT", "INS");
	$search_box = "";
	//........................................................................................................................................................
	//검색박스 - 부킹 수동상태
	$ary_scb_booking_kind = "";
	for( $i=0; $i<count($booking_kind_array); $i++ ){
		if( ${"search_check_box_".$booking_kind_array[$i]} == 'on' ){
			$ary_scb_booking_kind[] = $booking_kind_array[$i];
			$search_box .= "&search_check_box_".$booking_kind_array[$i]."=on";
		}
    }
	if( $ary_scb_booking_kind !="" ){
		$show_scb_booking_kind = implode("','", $ary_scb_booking_kind);
		$bk_where_sql .= " and BKP_KIND in ('".$show_scb_booking_kind."') ";
    }
	//검색박스 - 지역
	$ary_scb_booking_area = "";
	for( $i=0; $i<count($booking_area_array); $i++ ){
		if( ${"search_check_box_".$booking_area_array[$i]} == 'on' ){
			$ary_scb_booking_area[] = str_replace('K_P','K-P',$booking_area_array[$i]);
			$search_box .= "&search_check_box_".$booking_area_array[$i]."=on";
		}
	}
	if( $ary_scb_booking_area !="" ){
		$show_scb_booking_area = implode("','", $ary_scb_booking_area);
		$bk_where_sql .= " and BKP_AREA in ('".$show_scb_booking_area."') ";
    }
	//부킹종류
	$ary_scb_booking_type = "";
	for( $i=0; $i<count($booking_type_array2); $i++ ){
		if( ${"search_check_box_".$booking_type_array2[$i]} == 'on' ){
			$ary_scb_booking_type[] = $booking_type_array2[$i];
			$search_box .= "&search_check_box_".$booking_type_array2[$i]."=on";
		}
	}
	if( $ary_scb_booking_type !="" ){
		$show_scb_booking_type = implode("','", $ary_scb_booking_type);
		$bk_where_sql .= " and BKP_TYPE in ('".$show_scb_booking_type."') ";
    }
	//........................................................................................................................................................



	// 검색이 있을경우
	if( $search_mode == "ok" ){

	}

	if( $_search_kind && $_search_text != "" ){
        if($_search_kind == 'guest'){
            $bk_where_sql .= " and BKP_GUEST like '%".$_search_text."%'";
        }else if($_search_kind == 'hotel'){
            $bk_where_sql .= " and BKP_HOTEL like '%".$_search_text."%'";
        }else if($_search_kind == 'agency'){
            $bk_where_sql .= " and BKP_AGNCY_TEXT like '%".$_search_text."%'";
        }else if($_search_kind == 'business'){
            $bk_where_sql .= " and BKP_BUSINESS like '%".$_search_text."%'";
        }else if($_search_kind == 'manager'){
            $bk_where_sql .= " and BKP_RESERVER like '%".$_search_text."%'";
        }
	}


	if( $_sort_kind == 'kind' || $_sort_kind == 'loc' ){
		if( $_order_by == '' || $_order_by == 'desc' ){
			$_order_by ='asc';
		}else{
			$_order_by ='desc';
		}
	}else{
		if( $_order_by == '' || $_order_by == 'asc' ){
			$_order_by ='desc';
		}else{
			$_order_by ='asc';
		}
	}

	//IDX 정렬
	if( $_sort_kind == "no" ) {
		$bk_sort_sql = " BKP_IDX ".$_order_by;
		if($_order_by == 'desc'){
			$sort_no_icon='▲';
		}else{
			$sort_no_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_out_icon=''; $sort_gui_icon='';
	
	// STATUS 수동상태  정렬
	}elseif( $_sort_kind == "kind" ){
		$bk_sort_sql = " BKP_KIND ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_kind_icon='▲';
		}else{
			$sort_kind_icon='▼';
		}
		$sort_no_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_out_icon=''; $sort_gui_icon='';

	// Area 지역 정렬
	}elseif( $_sort_kind == 'loc' ){
		$bk_sort_sql = " BKP_AREA ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_loc_icon='▲';
		}else{
			$sort_loc_icon='▼';
		}
		$sort_kind_icon=''; $sort_no_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_out_icon=''; $sort_gui_icon='';

	// 부킹종류 정렬
	}elseif( $_sort_kind == 'cat' ){
		$bk_sort_sql = " BKP_TYPE ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_cat_icon='▲';
		}else{
			$sort_cat_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_no_icon=''; $sort_in_icon=''; $sort_out_icon=''; $sort_gui_icon='';

	// 가이드 정렬
	}elseif( $_sort_kind == 'gui' ){
		$bk_sort_sql = " BKP_GUIDE_ID ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_gui_icon='▲';
		}else{
			$sort_gui_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_no_icon='';  $sort_out_icon='';

	// IN 정렬
	}elseif( $_sort_kind == 'in' ){
		$sort_sql = " BKP_START_DATE ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_in_icon='▲';
		}else{
			$sort_in_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_no_icon=''; $sort_out_icon=''; $sort_gui_icon='';

	// OUT 정렬
	}elseif( $_sort_kind == 'out' ){
		$sort_sql = " BKP_ARRIVE_DATE ".$_order_by;
		if( $_order_by == 'desc' ){
			$sort_out_icon='▲';
		}else{
			$sort_out_icon='▼';
		}
		$sort_kind_icon=''; $sort_loc_icon=''; $sort_cat_icon=''; $sort_in_icon=''; $sort_no_icon=''; $sort_gui_icon='';

	}

	if($_calendar_mode == 'on'){
		$_st_date = securityVal($_ca_st_date);
		$_type = securityVal($_ca_type);
		$_kind = securityVal($_ca_kind);
		$_c_mode = securityVal($_ca_c_mode);
		if($_kind == 'day'){
			$st_date = mktime(0,0,0,substr($_st_date, 4, 2),substr($_st_date, 6, 2),substr($_st_date, 0, 4));
			$end_date = mktime(23,59,59,substr($_st_date, 4, 2),substr($_st_date, 6, 2),substr($_st_date, 0, 4));
			if($_c_mode != 'reg'){
				$bk_where_sql = " where BKP_START_DATE >= ".$st_date." and BKP_START_DATE <= ".$end_date." and BKP_TYPE = '".$_type."'";
			}else{
				$bk_where_sql = " where (BKP_BOOKING_DATE >= ".$st_date." and BKP_BOOKING_DATE <= ".$end_date.") or (BKP_RE_DATE >= ".$st_date." and BKP_RE_DATE <= ".$end_date.") and BKP_TYPE = '".$_type."'";
			}
		}else{
			$search_et = date("Ymd",$_st_date);
			$m_st_date = date("Ymd", strtotime($_st_date." -7 day"));
			$end_date = mktime(23,59,59,substr($_st_date, 4, 2),substr($_st_date, 6, 2),substr($_st_date, 0, 4));
			$st_date  = mktime(0,0,0,substr($m_st_date, 4, 2),substr($m_st_date, 6, 2),substr($m_st_date, 0, 4));
			if($_c_mode != 'reg'){
				$bk_where_sql = " where BKP_START_DATE >= ".$st_date." and BKP_START_DATE <= ".$end_date." and BKP_TYPE = '".$_type."'";
			}else{
				$bk_where_sql = " where (BKP_BOOKING_DATE >= ".$st_date." and BKP_BOOKING_DATE <= ".$end_date.") or (BKP_RE_DATE >= ".$st_date." and BKP_RE_DATE <= ".$end_date.") and BKP_TYPE = '".$_type."'";
			}
		}
	}

	if($_mode == 'delList'){
		$pageName = "del_booking_list";
		$bk_where_sql= "where BKP_DEL_YN = 'Y'";
	}else{
		$bk_where_sql.= " and BKP_DEL_YN = 'N'";
	}

	$total_count = wepix_counter(_DB_BOOKING, $bk_where_sql);

	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	

    $bk_query = "select 
		@ROWNUM := @ROWNUM + 1 AS RNUM, "._DB_BOOKING.".
		* from "._DB_BOOKING."
		,(SELECT @ROWNUM:= 0) R
		".$bk_where_sql." order by ".$bk_sort_sql." limit ".$from_record.", ".$list_num;
	$bk_result = wepix_query_error($bk_query);


	$page_link_text = "?search_mode=".$_search_mode."&search_kind=".$_search_kind."&search_st=".$_search_st."&search_et=".$_search_et."&search_date_kind=".$_search_date_kind."&search_text=".$_search_text."".$search_box."&sort_kind=".$_sort_kind."&order_by=".$_order_by;

	$_show_get_url = $page_link_text."&pn=".$_pn;

	$view_paging = paging($pn, $total_page, $list_num, $page_num, $_show_get_url."&pn=");

include "../layout/header.php";
?>

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
.table-style{ }

.bls_checkbox{ width:25px !important; }
.bls_no{ width:40px !important; }
.bls_code{ width:70px !important; }
.bls_modify{ width:72px !important; }
.bls_status{ width:63px !important; }
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

<div id="contents_head">
	<h1>Booking list</h1>
</div>
<link rel="stylesheet" href="/admin2/css/daterangepicker.css" />
<script src="/admin2/js/moment.min.js"></script>
<script src="/admin2/js/jquery.daterangepicker.js"></script>
<script type='text/javascript'>

function chKind(num,value,mokey){

	$.ajax({
		url: "<?=_A_PATH_BOOKING_OK?>",
		data: {
			"a_mode":"htkindCh_list",
			"mokey":mokey,
			"kind":value,
            "num":num
		},
		type: "POST",
		dataType: "text",
		success: function(data){
            location.reload();
		},
		error: function(){
			//에러
		}
	});
			
}

function agency_cf(key, state) {

	$.ajax({
		url: "<?=_A_PATH_BOOKING_OK?>",
		data: {
			"a_mode":"agency_cf",
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
						$("#agency_confirm_"+key).html("<button type=\"button\" class=\"btnstyle1 btnstyle1-primary btnstyle1-xs m-t-3 width-50\" onclick=\"agency_cf('"+key+"', 'Y');\"><i class=\"fas fa-check-circle\"></i> 컨폼</button>");
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


doGroup=function(mode, w, h){

	if( $(".checkSelect:checked").length == 0){
		alert('Please specify a team to create the group.');
		return false;
	}
	var bkg_code = 0;
	var send_array = "";
	$(".checkSelect:checked").each(function(index){
		if(index!=0){ send_array += ","; }
		send_array += $(this).val();
		if( $("#bkp_bkg_code_"+$(this).val()).val() != "" ){
			bkg_code++;
		}
	});
	if( bkg_code > 0){
		alert('More than one of the selected teams already belongs to the Group. \n turn off  the group and set the group again.');
		return false;
	}
	showPopup(w,h);
	$('#popup_iframe').attr("src", "booking_group_form_popup.php?mode="+ mode +"&array="+ send_array).show();

};

function excelDownload(key){
	var search_st  = "<?php echo $_search_st;?>";
	var search_et  = "<?php echo $_search_et;?>";
    var total_count = "<?php echo $total_count;?>";
    var today = "<?php echo $today;?>";
    var search_bk_kind = "<?php echo $search_bk_kind;?>";
    var search_bkp_type = "<?php echo $search_bkp_type;?>";
    var search_kind  = "<?php echo $search_kind;?>";
    var search_st2  = "<?php echo $search_st;?>";
    var search_et2 = "<?php echo $search_et;?>";
    var search_text = "<?php echo $search_text;?>";
    var search_date_kind = "<?php echo $search_date_kind;?>";
	var search_box= "<?php echo $search_box;?>";
	var mode = "<?php echo $mode;?>";
	
	//alert(search_box);
	//alert("booking_apsi_excel_download.php?search_st2=2019-03-05&search_et2=2019-03-15&search_st=1551711600&search_et=1552661999&ex_mode=all");
	//alert("booking_agency_excel_download.php?search_bk_kind="+search_bk_kind+"&search_bkp_type="+search_bkp_type+"&search_kind="+search_kind+"&search_st2="+search_st2+"&search_et2="+search_et2+"&search_text="+search_text+"&today="+today+"&search_date_kind="+search_date_kind+"&search_st="+search_st+"&search_et="+search_et+"&ex_mode=all"+search_box+"&search_mode=ok");
    //alert("booking_agency_excel_download.php?search_bk_kind="+search_bk_kind+"&search_bkp_type="+search_bkp_type+"&search_kind="+search_kind+"&search_st2="+search_st2+"&search_et2="+search_et2+"&search_text="+search_text+"&today="+today+"&search_date_kind="+search_date_kind+"&search_st="+search_st+"&search_et="+search_et+"&ex_mode=all"+$search_box+"&search_mode=ok");
	
  if( $(".checkSelect:checked").length == 0){
		alert('Please select a Booking to print out.');
		return false;
	}
    var send_array = "";
	$(".checkSelect:checked").each(function(index){
		if(index!=0){ send_array += ","; }
		send_array += $(this).val();
	});
  /*
    if(total_count*1 >= 70){
        if(confirm("There are over 70 Booking cases. Do you want to print all?") == true){
            if(key == 'all'){
                $("#HIddenActionFrame").attr("src", "excel_booking_list.php?search_bk_kind="+search_bk_kind+"&search_bkp_type="+search_bkp_type+"&search_kind="+search_kind+"&search_st2="+search_st2+"&search_et2="+search_et2+"&search_text="+search_text+"&today="+today+"&search_date_kind="+search_date_kind+"&ex_mode=all"+search_box+"&search_mode=ok");
            }else if(key == 'hotel'){
                //alert('수정작업중이라 이용하실수 없습니다.');
                $("#HIddenActionFrame").attr("src", "excel_booking_hotel.php?search_bk_kind="+search_bk_kind+"&search_bkp_type="+search_bkp_type+"&search_kind="+search_kind+"&search_st2="+search_st2+"&search_et2="+search_et2+"&search_text="+search_text+"&today="+today+"&search_date_kind="+search_date_kind+"&search_st="+search_st+"&search_et="+search_et+"&ex_mode=all"+search_box+"&search_mode=ok");
            }else if(key == 'invoice'){
                $("#HIddenActionFrame").attr("src", "excel_booking_invoice.php?search_bk_kind="+search_bk_kind+"&search_bkp_type="+search_bkp_type+"&search_kind="+search_kind+"&search_st2="+search_st2+"&search_et2="+search_et2+"&search_text="+search_text+"&today="+today+"&search_date_kind="+search_date_kind+"&ex_mode=all"+search_box+"&search_mode=ok");
            }else if(key == 'agency'){
				$("#HIddenActionFrame").attr("src", "excel_booking_agency.php?search_bk_kind="+search_bk_kind+"&search_bkp_type="+search_bkp_type+"&search_kind="+search_kind+"&search_st2="+search_st2+"&search_et2="+search_et2+"&search_text="+search_text+"&today="+today+"&search_date_kind="+search_date_kind+"&search_st="+search_st+"&search_et="+search_et+"&ex_mode=all"+search_box+"&search_mode=ok");
			   //location.href="excel_booking_agency.php?search_bk_kind="+search_bk_kind+"&search_bkp_type="+search_bkp_type+"&search_kind="+search_kind+"&search_st2="+search_st2+"&search_et2="+search_et2+"&search_text="+search_text+"&today="+today+"&search_date_kind="+search_date_kind+"&search_st="+search_st+"&search_et="+search_et+"&ex_mode=all"+$search_box+"&search_mode=ok";
            }else if(key == 'apis'){
                $("#HIddenActionFrame").attr("src", "excel_booking_apis.php?search_bk_kind="+search_bk_kind+"&search_bkp_type="+search_bkp_type+"&search_kind="+search_kind+"&search_st2="+search_st2+"&search_et2="+search_et2+"&search_text="+search_text+"&today="+today+"&search_date_kind="+search_date_kind+"&search_st="+search_st+"&search_et="+search_et+"&ex_mode=all"+search_box+"&search_mode=ok");
            }
        }else{
            if(key == 'all'){
                $("#HIddenActionFrame").attr("src", "excel_booking_list.php?send_array="+send_array);
            }else if(key == 'hotel'){
                //alert('수정작업중이라 이용하실수 없습니다.');
                $("#HIddenActionFrame").attr("src", "excel_booking_hotel.php?send_array="+send_array+"&search_st="+search_st+"&search_et="+search_et);
            }else if(key == 'invoice'){
                
                $("#HIddenActionFrame").attr("src", "excel_booking_invoice.php?send_array="+send_array);
            }else if(key == 'agency'){
                $("#HIddenActionFrame").attr("src", "excel_booking_agency.php?send_array="+send_array+"&search_st="+search_st+"&search_et="+search_et);
            }else if(key == 'apis'){
                $("#HIddenActionFrame").attr("src", "excel_booking_apis.php?send_array="+send_array+"&search_st="+search_st+"&search_et="+search_et);
            }
        }
    }else{ */
        if(key == 'all'){
            $("#HIddenActionFrame").attr("src", "excel_booking_list.php?send_array="+send_array);
        }else if(key == 'hotel'){
            //alert('수정작업중 이용하실수 없습니다.');
            $("#HIddenActionFrame").attr("src", "excel_booking_hotel.php?send_array="+send_array+"&search_st="+search_st+"&search_et="+search_et);
        }else if(key == 'invoice'){
            $("#HIddenActionFrame").attr("src", "excel_booking_invoice.php?send_array="+send_array);
        }else if(key == 'agency'){
            $("#HIddenActionFrame").attr("src", "excel_booking_agency.php?send_array="+send_array+"&search_st="+search_st+"&search_et="+search_et);
        }else if(key == 'apis'){
            $("#HIddenActionFrame").attr("src", "excel_booking_apis.php?send_array="+send_array+"&search_st="+search_st+"&search_et="+search_et);
        }
    //}
}
$(function(){

	$('#s_day').dateRangePicker(
	{
		separator : ' to ',
		getValue: function()
		{
			if ($('#date-range200').val() && $('#date-range201').val() )
				return $('#date-range200').val() + ' to ' + $('#date-range201').val();
			else
				return '';
		},
		setValue: function(s,s1,s2)
		{
			$('#s_day').val(s1);
			$('#e_day').val(s2);
		}
	});
});

</script>
<iframe style="display:none;" name="HIddenActionFrame" id="HIddenActionFrame"></iframe>

<div id="contents_body">
	<div id="contents_body_wrap">

		<!-- 검색 -->
   		<form name='search' method='post' action="booking_list.php">
		<input type='hidden' name='search_mode' value='ok'>
		<input type='hidden' name='today' value='<?=$today?>'>

		<div class="search-wrap">
			<ul class="td search-img"></ul>
			<ul class="td search-body">

				<div class="booking-search">
					
					<li class="m-b-5"><button type="button" id="search_list" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="search_detail()"><i class="fa fa-plus"></i> Search Area Setting</button></li>
					<li class="m-b-5">
						<select name="search_kind" id="search_kind" >
							<option value=''>Search type</option>
							<option value='guest' <? if( $search_kind == 'guest')  echo "selected"; ?>>Guest</option>
							<option value='hotel' <? if( $search_kind == 'hotel')  echo "selected"; ?>>Hotel/Room Type</option>
							<option value='agency'<? if( $search_kind == 'agency')  echo "selected"; ?>>Agency/Agenet</option>
							<option value='manager'<? if( $search_kind == 'manager')  echo "selected"; ?>>OP name</option>
						</select>
						<input type='text' style='width:161px;' id='search_text' name='search_text' value='<?=$search_text?>' placeholder="Please enter a search">
					</li>
					<li >
						<select name='search_date_kind' >
							<option value='airport' <? if( $search_date_kind == 'airport')  echo "selected"; ?>>Filgt</option>
							<option value='req' <? if( $search_date_kind == 'req')  echo "selected"; ?>>Day</option>
						</select>
						<input type="text" id="s_day" name="search_st" value="<?=$search_st?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="시작일" readonly /> ~
						<input type="text" id="e_day" name="search_et" value="<?=$search_et?>" style="width:80px; cursor:pointer;" class="day-section text-center" placeholder="종료일" readonly />
						
					</li>

						<div id="search_config_box">
							<table>
								<tr>
									<th>Stauts</th>
									<th>Area</th>
									<th>Category</th>
								<tr>
								<!--
								<tr>
									<td><label><input type='checkbox'  name='checkSearchSt_all' class="checkSearchSt_all"  <?if($checkSearchSt_all == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>ALL</label></td>
									<td><label><input type='checkbox'  name='checkSearchAr_all' class="checkSearchAr_all"  <?if($checkSearchAr_all == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>ALL</label></td>
									<td><label><input type='checkbox'  name='checkSearchTy_all' class="checkSearchTy_all"  <?if($checkSearchTy_all == 'on' || $search_mode != 'ok'){  echo "checked='true'";}?>>ALL</label></td>
								</tr>
								-->
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
									<td><label><input type='checkbox' class="checkSearchSt" name='search_check_box_CANCEL' <?if($search_check_box_CANCEL == 'on'){  echo "checked='true'";}?>> CANCEL</td>
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
		<!-- 검색 끝-->

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn">
<!--
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-inverse btnstyle1-sm" onclick="agency_array();"><i class="fas fa-check-circle"></i> Selected Agency Confirm</button>-->
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="doGroup('add','600','500');"><i class="fa fa-user-plus" ></i> Add Team</button>
				<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm m-r-20" onclick="doGroup('new','600','500');"><i class="fa fa-plus-circle" ></i> Selected New Group</button>

				<button type="button" id="exce" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="excelDownload('apis');"><i class="fas fa-file-excel"></i></i> Apis</button>
				<button type="button" id="exce" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="excelDownload('invoice');"><i class="fas fa-file-excel"></i></i> KR Invoice</button>
				<button type="button" id="exce" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="excelDownload('agency');"><i class="fas fa-file-excel"></i></i> KR Reconfirm</button>
				<button type="button" id="exce" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="excelDownload('hotel');"><i class="fas fa-file-excel"></i></i> Hotel Booking</button>
				<button type="button" id="exce" class="btnstyle1 btnstyle1-success btnstyle1-sm m-r-20" onclick="excelDownload('all');"><i class="fas fa-file-excel"></i></i> Booking List</button>

				<button type="button" id="bklist_open" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="bklistOpen()"><i class="fa fa-cog" ></i> Setting</button>
			</ul>
		</div>

		<div id="list-box">
			<div class="table-wrap">

<table cellspacing="1px" cellpadding="0" border="0" class="table-style booking-list" >	
	<tr>
		<th class="bls_checkbox"><input type="checkbox" name="check_box_all" class="check_box_all" onclick="select_all()"></th>
		<th class="bls_no" <? if( $_COOKIE['booking_list_show_no'] == "Off" ){ ?>style="display:none;" <? } ?>>NO</th>
<?if($_mode == 'delList'){?>
<th  class="bls_group">삭제자</th>

<?}?>
		<th class="bls_code" <? if( $_COOKIE['booking_list_show_code'] == "Off" ){ ?>style="display:none;" <? } ?>><a href="<?=$_show_get_url?>&sort_kind=no" style="text-decoration:none">Maching<br/> Code <?=$sort_no_icon?></a></th>
		<th class="bls_modify">Modify</th>		
		<th class="bls_status" <? if( $_COOKIE['booking_list_show_status'] == "Off" ){ ?>style="display:none;" <? } ?>><a href="<?=$_show_get_url?>&sort_kind=kind" style="text-decoration:none">STATUS <?=$sort_kind_icon?> </a></th>
		<th class="bls_location" <? if( $_COOKIE['booking_list_show_location'] == "Off" ){ ?>style="display:none;" <? } ?>><a href="<?=$_show_get_url?>&sort_kind=loc" style="text-decoration:none">Area <?=$sort_loc_icon?></a></th>
		<th class="bls_category" <? if( $_COOKIE['booking_list_show_category'] == "Off" ){ ?>style="display:none;" <? } ?>><a href="<?=$_show_get_url?>&sort_kind=cat" style="text-decoration:none">Category <?=$sort_cat_icon?></a></th>
		<th class="bls_group" <? if( $_COOKIE['booking_list_show_group'] == "Off" ){ ?>style="display:none;" <? } ?>>Group</th>
		<th class="bls_guide" <? if( $_COOKIE['booking_list_show_guide'] == "Off" ){ ?>style="display:none;" <? } ?>><a href="<?=$_show_get_url?>&sort_kind=gui" style="text-decoration:none">Guide <?=$sort_gui_icon?></a></th>
		<th class="bls_flight" <? if( $_COOKIE['booking_list_show_flight'] == "Off" ){ ?>style="display:none;" <? } ?>></a><a href="<?=$_show_get_url?>&sort_kind=in" style="text-decoration:none"><i class="fa fa-plane" aria-hidden="true"></i> IN <?=$sort_in_icon?></a></th>
		<th class="bls_flight" <? if( $_COOKIE['booking_list_show_flight'] == "Off" ){ ?>style="display:none;" <? } ?>><a href="<?=$_show_get_url?>&sort_kind=out" style="text-decoration:none"><i class="fa fa-plane" aria-hidden="true"></i> OUT <?=$sort_out_icon?></a></th>
		<th class="bls_guest" <? if( $_COOKIE['booking_list_show_guest'] == "Off" ){ ?>style="display:none;" <? } ?>>Guest</th>
		<th class="bls_hotel" <? if( $_COOKIE['booking_list_show_hotel'] == "Off" ){ ?>style="display:none;" <? } ?>>Hotel/State </th>
		<!-- <th class="bls_option" <? if( $_COOKIE['booking_list_show_option'] == "Off" ){ ?>style="display:none;" <? } ?>>Option</i></th> -->
		<th class="bls_agency" <? if( $_COOKIE['booking_list_show_agency'] == "Off" ){ ?>style="display:none;" <? } ?>>Agency</i></th>
		<th class="bls_tourcost" <? if( $_COOKIE['booking_list_show_tourcost'] == "Off" ){ ?>style="display:none;" <? } ?>>Tour Cost</i></th>
		<th>Hotel Price</th>
		<th class="bls_booking_day" <? if( $_COOKIE['booking_list_show_booking_day'] == "Off" ){ ?>style="display:none;" <? } ?>>Booking<br/>Day</i></th>
		<th class="bls_change_day" <? if( $_COOKIE['booking_list_show_change_day'] == "Off" ){ ?>style="display:none;" <? } ?>>Change<br/>Day</i></th>
		<th class="bls_progress" <? if( $_COOKIE['booking_list_show_progress'] == "Off" ){ ?>style="display:none;" <? } ?>>Progress</th>
		<!-- <th class="bls_schedule" <? if( $_COOKIE['booking_list_show_schedule'] == "Off" ){ ?>style="display:none;" <? } ?>>Schedule</th> -->
	</tr>
<?
while($bk_list = wepix_fetch_array($bk_result)){
	
	//부킹상태
	if( $bk_list[BKP_KIND] == "NEW" ){
		$booking_kind_class = "new";
	}elseif( $bk_list[BKP_KIND] == "AMEND" ){
		$booking_kind_class = "amend";
	}elseif( $bk_list[BKP_KIND] == "BLOCK" ){
		$booking_kind_class = "block";
	}elseif( $bk_list[BKP_KIND] == "CANCEL" ){
		$booking_kind_class = "cancel";
	}elseif( $bk_list[BKP_KIND] == "DUPE" ){
		$booking_kind_class = "dupe";
	}

	//가이드 닉네임
	if( $bk_list[BKP_GUIDE_ID] ){
		//한번 호출한 DB 정보를 다시 재사용
		if( ${'_show2_guide_nick_'.$bk_list[BKP_GUIDE_ID]} == "" ){
			$guide_data = wepix_fetch_array(wepix_query_error("select GD_NICK from "._DB_GUIDE." where GD_ID = '".$bk_list[BKP_GUIDE_ID]."' "));
			${'_show2_guide_nick_'.$bk_list[BKP_GUIDE_ID]} = $guide_data[GD_NICK];
			$_view2_guide_nick = $guide_data[GD_NICK];
		}else{
			$_view2_guide_nick = ${'_show2_guide_nick_'.$bk_list[BKP_GUIDE_ID]};
		}
	}else{
		$_view2_guide_nick = "";
	}

	//에이전시 본사
	if( $bk_list[BKP_AGENCY] ){
		//한번 호출한 DB 정보를 다시 재사용
		if( ${'_show2_agency_head_'.$bk_list[BKP_AGENCY]} == "" ){
			$agency_data = wepix_fetch_array(wepix_query_error("select AG_COMPANY from "._DB_AGENCY."  where AG_IDX = '".$bk_list[BKP_AGENCY]."'"));
			$_view2_agency_head = $agency_data[AG_COMPANY];
		}else{
			$_view2_agency_head = ${'_show2_agency_head_'.$bk_list[BKP_AGENCY]};
		}
	}else{
		$_view2_agency_head = "";
	}

	//에이전시 지사
	if( $bk_list[BKP_BUSINESS] ){
		//한번 호출한 DB 정보를 다시 재사용
		if( ${'_show2_agency_branch_'.$bk_list[BKP_BUSINESS]} == "" ){
			$agency_data = wepix_fetch_array(wepix_query_error("select AG_COMPANY from "._DB_AGENCY."  where AG_IDX = '".$bk_list[BKP_BUSINESS]."'"));
			$_view2_agency_branch = $agency_data[AG_COMPANY];
		}else{
			$_view2_agency_branch = ${'_show2_agency_branch_'.$bk_list[BKP_BUSINESS]};
		}
	}else{
		$_view2_agency_branch = "";
	}

    $_view2_bkp_start_date = date("d-M-y", $bk_list[BKP_START_DATE]);
    $_view2_bkp_arrive_date = date("d-M-y", $bk_list[BKP_ARRIVE_DATE]);
    $_view2_bkp_start_date2 = date("d-M-y", $bk_list[BKP_START_DATE2]);
	$_view2_bkp_arrive_date2 = date("d-M-y", $bk_list[BKP_ARRIVE_DATE2]);
	$_ary_bkp_hot_total_price = explode("│",$bk_list[BKP_HOT_TOTAL_PRICE]);

	//호텔
	$_ary_hotel = explode("│",$bk_list[BKP_HOTEL]); //호텔
	$_ary_schedule_day = explode("│",$bk_list[BKP_SCHEDULE_DAY]);
	$_ary_bkp_hot_booking_state = explode("│",$bk_list[BKP_HOT_BOOKING_STATE]);

	$_ary_bkp_land_fee_text = explode("│",$bk_list[BKP_LAND_FEE_TEXT]);
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


	$_ary_guest = explode("│",$bk_list[BKP_GUEST]); //게스트
	$_ary_bkp_hot_option = explode("│",$bk_list[BKP_HOT_ALLIN_YN]);

    $_view2_booking_date = ($bk_list[BKP_BOOKING_DATE] > 0) ? date("y-m-d", $bk_list[BKP_BOOKING_DATE]) : "";
    $_view2_booking_mo_date = ($bk_list[BKP_BOOKING_MO_DATE] > 0) ? date("y-m-d", $bk_list[BKP_BOOKING_MO_DATE]) : "";

	if($bk_list[RNUM] % 2 == 1){ $trclass = "bl_tr"; }elseif($bk_list[RNUM] % 2 == 0){ $trclass = "bl_tr2"; }

?>
<input type='hidden' name='bkp_bkg_code[]' id="bkp_bkg_code_<?=$bk_list[BKP_IDX]?>" value="<?=$bk_list[BKP_BKG_CODE]?>">
	<tr align="center" id="trid_<?=$admin_list[AD_IDX]?>" class="<?=$trclass?>" >
	<td class="bls_checkbox"><input type="checkbox" name="key_check[]"  id="bkp_idx_<?=$bk_list[BKP_IDX]?>" class="checkSelect" value="<?=$bk_list[BKP_IDX]?>" onclick="checkboxOn('<?=$bk_list[BKP_IDX]?>')"></td>
	<td class="bls_no" <? if( $_COOKIE['booking_list_show_no'] == "Off" ){ ?>style="display:none;" <? } ?>><?=$bk_list[RNUM]?></td> 
<?if($_mode == 'delList'){?>
	<td class="bls_code">
		<?=$bk_list[BKP_DEL_ID]?> <br/>(<?=date("y-m-d",$bk_list[BKP_DEL_DATE])?>)
	</td>
<?}?>
	<td class="bls_code" <? if( $_COOKIE['booking_list_show_code'] == "Off" ){ ?>style="display:none;" <? } ?>><?=$bk_list[BKP_IDX]?><br><b><?=$bk_list[BKP_MACHING_CODE]?></b></td>
	<td class="bls_modify">
<?if($_mode == 'delList'){?>
		<input type="button" value="Info" style="margin-bottom:3px;" onclick="bookingInfo('<?=$bk_list[BKP_IDX]?>','<?=$_show_get_url?>');"><br>
		<input type='button' onclick="javascript:goDelAdmin('<?=$bk_list[BKP_IDX]?>')"; value='Delete'>
<?}else{?>
		<!-- <input type="button" value="Info" style="margin-bottom:3px;" onclick="bookingInfo('<?=$bk_list[BKP_IDX]?>','<?=$_show_get_url?>');"><br> -->
		<input type="button" value="test" style="margin-bottom:3px !important;" onclick="bookingModify3('<?=$bk_list[BKP_IDX]?>');"><br>
		<input type="button" value="Modify" style="margin-bottom:3px !important;" onclick="bookingModify2('<?=$bk_list[BKP_IDX]?>');"><br>
		<input type='button' onclick="javascript:goDel('<?=$bk_list[BKP_IDX]?>')"; value='Delete'>

<?}?>
	</td>
	<td class="bls_status" <? if( $_COOKIE['booking_list_show_status'] == "Off" ){ ?>style="display:none;" <? } ?>>
		<div class="booking_kind_style <?=$booking_kind_class?>"><?=$bk_list[BKP_KIND]?></div>
	</td>
	<td class="bls_location" <? if( $_COOKIE['booking_list_show_location'] == "Off" ){ ?>style="display:none;" <? } ?>><b class="booking_type"><?=$bk_list[BKP_AREA]?></b></td>
	<td class="bls_category" <? if( $_COOKIE['booking_list_show_category'] == "Off" ){ ?>style="display:none;" <? } ?>><b class="booking_cate"><?=$bk_list[BKP_TYPE]?></b></td>

	<!-- 그룹 지정상태 여부 -->
	<td class="bls_group" <? if( $_COOKIE['booking_list_show_group'] == "Off" ){ ?>style="display:none;" <? } ?>>
		<? if( $bk_list[BKP_BKG_CODE] ){ ?>
			<b><?=$bk_list[BKP_BKG_IDX]?></b><br>
			<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-xs" style="line-height:120% !important; padding:7px 5px !important; " onclick="if (confirm('해당 팀을 그룹해제 합니다.\n해제 후 그룹정보다 다시 갱신되어 저장됩니다.\n정말 처리 하시겠습니까?')) { location.href='booking_ok.php?a_mode=outBookingGroup&out_idx=<?=$bk_list[BKP_IDX]?>&out_code=<?=$bk_list[BKP_BKG_CODE]?>' }else{ return false; }"><i class="fa fa-ban" aria-hidden="true"></i><br>Release</button>
		<? }else{ ?>
			<span style="color:#777; font-size:11px;">Unspecified Group</span>
		<? } ?>
	</td>
	<td class="bls_guide" <? if( $_COOKIE['booking_list_show_guide'] == "Off" ){ ?>style="display:none;" <? } ?>><?=$_view2_guide_nick?></td>
	<td class="bls_flight" <? if( $_COOKIE['booking_list_show_flight'] == "Off" ){ ?>style="display:none;" <? } ?>>
        <? if($_view2_bkp_start_date2  != '01-Jan-70'){?><span style="color:#0068ff;"><?=$_view2_bkp_start_date2?> <br/><?=strtoupper($bk_list[BKP_START_FLIGHT2])?></span><br/><? } ?>
		<b><?=$_view2_bkp_start_date?></b><br/><?=strtoupper($bk_list[BKP_START_FLIGHT])?>
	</td>
	<td class="bls_flight" <? if( $_COOKIE['booking_list_show_flight'] == "Off" ){ ?>style="display:none;" <? } ?>>
		<b><?=$_view2_bkp_arrive_date?></b><br/><?=strtoupper($bk_list[BKP_ARRIVE_FLIGHT])?>
        <? if($_view2_bkp_arrive_date2  != '01-Jan-70'){?><br/><span style="color:#0068ff;"><?=$_view2_bkp_arrive_date2?><br/><?=strtoupper($bk_list[BKP_ARRIVE_FLIGHT2])?></span><? } ?> 
	</td>

	<td class="bls_guest" <? if( $_COOKIE['booking_list_show_guest'] == "Off" ){ ?>style="display:none;" <? } ?> style="vertical-align:top !important;">
		<table cellspacing="1" cellpadding="0" class=" table-none-new booking-list-hotel">
		<?
		for( $i=0; $i<2; $i++ ){
			$_ary2_guest_info = explode("/",$_ary_guest[$i]);
			$_view2_guest_title = $_ary2_guest_info[0];
			$_view2_guest_name_kr = $_ary2_guest_info[1];
			$_view2_guest_name_en = strtoupper($_ary2_guest_info[2]);
		?>
			<tr>
				<td width="30px" class="p-3 text-right f-s-11"><?=$_view2_guest_title ?></td>
				<td width="50px" class="p-3 text-center"><b><?=$_view2_guest_name_kr?></b></td>
				<td class="p-3"><?=$_view2_guest_name_en?></td>
			</tr>
		<? } ?>
		<? 
			$order_guest_count = 0;
			if( count($_ary_guest) > 2 ){ 
				$order_guest_count = count($_ary_guest)-2;
		?>
			<tr>
				<td class='p-3 text-center' colspan='3'>외 <?=$order_guest_count?>명</td>
			</tr>
		<?}?>
		</table>
	</td>

	<td class="bls_hotel" <? if( $_COOKIE['booking_list_show_hotel'] == "Off" ){ ?>style="display:none;" <? } ?>>
		<table cellspacing="1" cellpadding="0" class="table-none-new booking-list-hotel">
		<? 
		$_view2_hotel_count = 0;
		$_view_total_hotel_price = 0;
		for( $i=0; $i<count($_ary_hotel); $i++ ){ 
			$_view2_hotel_count++;
			$_ary2_hotel_info = explode(":",$_ary_hotel[$i]);
			$_ary2_schedule = explode("/",$_ary_schedule_day[$i]);
			$_ary2_hotel_option = explode(",",$_ary_bkp_hot_option[$i]);
			
			$_ary2_bkp_hot_total_price = explode("/",$_ary_bkp_hot_total_price[$i]);
			$_view_total_hotel_price += $_ary2_bkp_hot_total_price[2];

			$_view2_hotel_name = $_ary2_hotel_info[1];
			$_view2_room_type = $_ary2_hotel_info[3];
			$_view2_room_count = $_ary2_hotel_info[4];
			$_view2_room_night = $_ary2_schedule[0];

			if( $_ary_bkp_hot_booking_state[$i] == "" ){ $_ary_bkp_hot_booking_state[$i] = 0; }

			if( $_ary_bkp_hot_booking_state[$i] == 0 ){
				$show_hotel_sate_style = "btnstyle1-gary";
				$show_hotel_sate_icon = "fa fa-angle-double-right";
			}elseif( $_ary_bkp_hot_booking_state[$i] == 1 ){
				$show_hotel_sate_style = "btnstyle1-success";
				$show_hotel_sate_icon = "fas fa-angle-double-right";
			}elseif( $_ary_bkp_hot_booking_state[$i] == 2 ){
				$show_hotel_sate_style = "btnstyle1-primary";
				$show_hotel_sate_icon = "far fa-check-square";
			}elseif( $_ary_bkp_hot_booking_state[$i] == 3 ){
				$show_hotel_sate_style = "btnstyle1-danger";
				$show_hotel_sate_icon = "fas fa-ban";
			}else{
				$show_hotel_sate_style = "btnstyle1-danger";
			}

			$_view2_hotel_reservation_state = $bva_tr_hotel_reservation_state[$_ary_bkp_hot_booking_state[$i]];
		?>
			<tr>
				<td class="p-3">
					<?=$_view2_hotel_count?>. <b><?=$_view2_hotel_name?></b><br>
					(<span style="color:#666; font-size:11px;"><?=$_view2_room_type?></span>)
				</td>
                <td class="p-3 text-center" style="width:50px">
					<b><?=$_view2_room_count?></b><span style="color:#777; font-size:11px;">RM</span>
					<b><?=$_view2_room_night?></b><span style="color:#777; font-size:11px;">N</span> 
				</td>
                <td class="p-3 text-center" style="width:80px">
					<? 
					for($i2=0; $i2<count($_ary2_hotel_option); $i2++){
						if( $_ary2_hotel_option[$i2] != 'none'){
					?>
						<?=$_ary2_hotel_option[$i2]?><br>
					<? }
					} ?>
				</td>
				<td class="p-3" style="width:50px"><button type="button" class="btnstyle1 <?=$show_hotel_sate_style?> btnstyle1-xs width-50" onclick='chKind(<?=$i?>,<?=$_ary_bkp_hot_booking_state[$i]?>,<?=$bk_list[BKP_IDX]?>);'><i class="<?=$show_hotel_sate_icon?>"></i> <?=$_view2_hotel_reservation_state?></button></td>
			</tr>
		<? } ?>
		</table>
	</td>
	<td class="bls_agency" <? if( $_COOKIE['booking_list_show_agency'] == "Off" ){ ?>style="display:none;" <? } ?>>
		<b><?=$_view2_agency_head?></b><br>
		<?=$_view2_agency_branch?>

		<!-- 에이전시 컨폼여부 -->
		<div id="agency_confirm_<?=$bk_list[BKP_IDX]?>">
		<? if( $bk_list[BKP_AGENCY_CONFIRM_YN] == 'N'){ ?>
			<button type="button" class="btnstyle1 btnstyle1-gary btnstyle1-xs m-t-3 width-50" onclick="agency_cf('<?=$bk_list[BKP_IDX]?>', 'N');"><i class="fas fa-angle-double-right"></i> W/T</button>
		<? }elseif( $bk_list[BKP_AGENCY_CONFIRM_YN] == 'Y'){ ?>
			<button type="button" class="btnstyle1 btnstyle1-primary btnstyle1-xs m-t-3 width-50" onclick="agency_cf('<?=$bk_list[BKP_IDX]?>', 'Y');"><i class="far fa-check-square"></i> C/F</button>
		<? } ?>

		</div>
	</td>

	<? 
	if($bk_list[BKP_LAND_FEE_YN] == 'Y'){
			$land_fee_color = 'blue';
	}else{
			$land_fee_color = 'red';
	}
	?>
	<td class="bls_tourcost" <? if( $_COOKIE['booking_list_show_tourcost'] == "Off" ){ ?>style="display:none;" <? } ?>>

		<?if($_view_land_fee == 0){?>
			<? if($bk_list[BKP_LAND_FEE]>0){?><span style='color:<?=$land_fee_color?>; font-size:11px;'><?=number_format($bk_list[BKP_LAND_FEE])?></span><?}?>
		<?}else{?>
			₩ <span style='color:<?=$land_fee_color?>; font-size:11px;'><?=number_format($_view_land_fee)?></span>
		<?}?>
	</td>
	<td class="bls_tourcost">
		 ฿ <?=number_format($_view_total_hotel_price)?>
	</td>

    <td class="bls_booking_day" <? if( $_COOKIE['booking_list_show_booking_day'] == "Off" ){ ?>style="display:none;" <? } ?>><?=$_view2_booking_date?></td>
    <td class="bls_change_day" <? if( $_COOKIE['booking_list_show_change_day'] == "Off" ){ ?>style="display:none;" <? } ?>><?=$_view2_booking_mo_date?></td>
	<td class="bls_progress" <? if( $_COOKIE['booking_list_show_progress'] == "Off" ){ ?>style="display:none;" <? } ?>><?=$booking_state_text[$bk_list[BKP_STATE]]?></td>

</tr>
<? }?>
</table>
			</div><!-- .table-wrap -->
		</div><!-- #list-box -->

		<div class="paging-wrap"><?=$view_paging?></div>
	</div><!-- #contents_body_wrap -->
</div>

<script type="text/javascript"> 
<!-- 

function goDel(key){
	if(confirm('정말 삭제 하시겠습니까?') == true){
		location.href = "<?=_A_PATH_BOOKING_OK?>?key="+key+"&a_mode=DelBooking";
	}
}
function goDelAdmin(key){
	if(confirm('영구적으로 삭제됩니다. 삭제하시곘습니까?') == true){
		location.href = "<?=_A_PATH_BOOKING_OK?>?key="+key+"&a_mode=DelManagerBooking";
	}
}



//검색 Search Area Setting
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

function bookingInfo(key,page_link_text){
	window.open("<?=_A_PATH_BOOKING_VIEW_POPUP?>"+page_link_text+"&key="+key, "bookingInfo"+key, "width=900,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
function bookingModify(key,page_link_text){
	location.href = "<?=_A_PATH_BOOKING_MODIFY_POPUP?>"+page_link_text+"&key="+key+"&mode=modify";
}
function bookingModify2(key){
	window.open("<?=_A_PATH_BOOKING_MODIFY_POPUP2?>?key="+key+"&mode=modify", "overlap_"+key, "width=1070,height=660,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
function bookingModify3(key){
	window.open("booking_modify_popup3.php?key="+key+"&mode=modify", "overlap2_"+key, "width=1200,height=700,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
function doTravelReport(key){
	location.href = "<?=_A_PATH_TRAVEL_PLAN_REG?>?bkp_key="+key;
}
function goTravelPlan(key){
	window.open("<?=_A_PATH_TRAVEL_PLAN_VIEW_POPUP?>?key="+key, "overlap", "width=900,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}
function goTravelEstimate(key){
	window.open("<?=_A_PATH_TRAVEL_ESTIMATE_VIEW_POPUP?>?key="+key, "overlap", "width=900,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>