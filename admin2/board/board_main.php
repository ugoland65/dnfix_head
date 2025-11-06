<?
$pageGroup = "board";
$pageName = "board_main";

include "../lib/inc_common.php";
//include "board_inc.php";

	$_b_code = securityVal($b_code);

	//게시판 코드가 있을때
	if( $_b_code ) {	
/*
		$bo_c_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD_A_CONFIG." where BOARD_CODE = '".$_b_code."' "));
		$_view_bc_name = $bo_c_data[BAC_NAME]; //게시판 이름
*/
		include "board_inc.php";
		$page_title_name = "게시판 설정변경 (".$_view_bc_name.") ";
	}else{
		$page_title_name = "신규 게시판 추가";
	}

	$bo_c_where = "  ";
	$bo_c_query = "select BAC_NAME, BOARD_CODE from "._DB_BOARD_A_CONFIG." ".$bo_c_where."order by UID desc ";
	$bo_c_result = wepix_query_error($bo_c_query);

	$page_btn_name = "변경저장";

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>게시판 설정</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="treemenu-wrap">
			<div class="treemenu">

					<div class="tree-left-wrap">
						<button type="button" id="" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="location.href='board_examination.php'" style="width:100%; height:28px !important; margin-bottom:5px;" > 
							<i class="fas fa-plus-circle"></i> 게시판 컬럼 검사
						</button>

						<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="location.href='<?=_A_PATH_BOARD_MAIN?>'" style="width:100%; height:28px !important;" > 
							<i class="fas fa-plus-circle"></i> 신규 게시판 추가
						</button>
						<?
						while($bo_c_list = wepix_fetch_array($bo_c_result)){
							$_inst2_ul_class = ($bo_c_list[BOARD_CODE] == $_b_code) ? "tree-big-menu2 active" : "tree-big-menu2-closed";
						?>
							<ul id="cate_<?=$bo_c_list[MPS_IDX]?>" class="<?=$_inst2_ul_class?>" onclick="location.href='<?=_A_PATH_BOARD_MAIN?>?b_code=<?=$bo_c_list[BOARD_CODE]?>'"><?=$bo_c_list[BAC_NAME]?></ul>
						<? } ?>
					</div>

			</div><!-- .treemenu -->
			<div class="treemenu-line">
			</div><!-- .treemenu-line -->
			<div class="treemenu-body">

				<form method='post' name='form1' id="board_config_form" action='processing.board_config.php'>
				
			<? if( !$_b_code ){ ?>
				<input type="hidden" name="a_mode" value="newBoard">
			<? }elseif( $_b_code ){ ?>
				<input type="hidden" name="a_mode" value="boardConfigModify">
				<input type="hidden" name="b_code" value="<?=$_b_code?>">
			<? } ?>

				<div class="ajax-page-title">
					<?=$page_title_name?>
				</div>

				<div class="table-wrap">

					<table cellspacing="1" cellpadding="0" class="table-style2 basic-form">
						<tr>
							<td colspan="2" class="table-sub-title"><i class="far fa-dot-circle"></i> 게시판 정보</td>
						</tr>
							<tr>
								<th>게시판 코드</th>
								<td>
								<? if( $_b_code ){ ?>
									<b><?=$_b_code?></b>
								<? }else{?>
									<input type='text' name='b_code' id='b_code' style="width:200px" >
								<? } ?>
								</td>
							</tr>
							<tr>
								<th>게시판 이름</th>
								<td><input type='text' name='bac_name' id='bac_name' style="width:200px" value="<?=$_view_bc_name?>"></td>
							</tr>
							<tr>
								<th>게시판 노출 이름</th>
								<td><input type='text' name='bac_name_show' id='bac_name_show' style="width:200px" value="<?=$_view_bc_name_show?>"></td>
							</tr>

							<tr>
								<td colspan="2" class="table-sub-title"><i class="far fa-dot-circle"></i> 게시판 기능</td>
							</tr>

							<tr>
								<th>게시판 분류</th>
								<td>
									<select name="bac_kind">
										<option value="basic" <? if( $_show_bc_kind == "basic" ) echo "selected";?>>일반</option>
										<option value="mtm" <? if( $_show_bc_kind == "mtm" ) echo "selected";?>>1:1 문의</option>
									</select>
								</td>
							</tr>

							<tr>
								<th>카테고리</th>
								<td>
									<div>
										<label><input type="radio" name="bac_category_active" value="N" <? if( $_show_bc_category_active == "N" ) echo "checked"; ?>> 비사용</label>
										<label><input type="radio" name="bac_category_active" value="Y" <? if( $_show_bc_category_active == "Y" ) echo "checked"; ?>> 사용</label>
									</div>
									<div style="margin-top:3px;">
										<input type='text' name='bac_category' id='bac_category'  value="<?=$_view_bc_category?>">
									</div>
<STYLE TYPE="text/css">
.alert{ margin:5px 0 0 !important;  }
</STYLE>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<strong>help</strong> 구분자는 "|" 입니다
									</div>
								</td>
							</tr>

						<tr>
							<th>이미지 업로드</th>
							<td>
								<div>
									<label><input type="checkbox" name="bac_image_active" value="Y" <? if( $_show_bc_image_active == "Y" ) echo "checked";?>> 사용</label>
								</div>
								<div>
									업로드 용량제한 : <input type='text' name='bac_image_size' id='bac_image_size' style="width:100px" value="<?=$_show_bc_image_size?>"> bytes 이하
									( 0 일경우 제한하지 않음 )
								</div>
							</td>
						</tr>

						<tr>
							<th>썸네일</th>
							<td>
								<div>
									<label><input type="checkbox" name="bac_thumbnail_active" value="Y" <? if( $_show_bc_thumbnail_active == "Y" ) echo "checked";?>> 사용</label>
									<label class="m-l-20"><input type="checkbox" name="bac_thumbnail_auto_active" value="Y" <? if( $_show_bc_thumbnail_auto_active == "Y" ) echo "checked";?>> 오토 썸네일</label>
								</div>
								<div style="margin-top:5px;">
									썸네일 사이즈: 
									<input type='text' name='bac_thumbnail_w' id='bac_thumbnail_w' style="width:80px" value="<?=$_show_bc_thumbnail_w?>" placeholder="가로 사이즈"> X
									<input type='text' name='bac_thumbnail_h' id='bac_thumbnail_h' style="width:80px" value="<?=$_show_bc_thumbnail_h?>"  placeholder="세로 사이즈">
									(  단위 : PX ) 
								</div>
							</td>
						</tr>
						<tr>
							<th>중복 조회수</th>
							<td><label><input type="checkbox" name="bac_hit_duplicate_active" value="Y" <? if( $_show_bc_hit_duplicate_active == "Y" ) echo "checked";?>> 사용</label></td>
						</tr>
						<tr>
								<th>읽음 확인 </th>
								<td>
									<label><input type="radio" name="bac_view_check_active" value="N" <? if( $_show_bc_view_check_active == "N" ) echo "checked"; ?>> 비사용</label>
									<label><input type="radio" name="bac_view_check_active" value="Y" <? if( $_show_bc_view_check_active == "Y" ) echo "checked"; ?>> 사용</label>
								</td>
						</tr>
						<tr>
							<th>상품 연동</th>
							<td>
								<div>
									<label><input type="checkbox" name="bac_product_active" value="Y" <? if( $_show_bc_product_active == "Y" ) echo "checked";?>> 사용</label>
								</div>
								<div>
									연동 그룹 : 
									<select name="bac_product_mode">
										<option value="basic" <? if( $_show_bc_product_mode == "basic" ) echo "selected";?>>일반 상품</option>
										<option value="travel" <? if( $_show_bc_product_mode == "travel" ) echo "selected";?>>여행 상품</option>
<?
//가격비교
if( _A_GLOB_GNB_ACTIVE_COMPARISON == "on" ){
?>
										<option value="comparison" <? if( $_show_bc_product_mode == "comparison" ) echo "selected";?>>가격비교 상품</option>
										<option value="comparison_relation" <? if( $_show_bc_product_mode == "comparison_relation" ) echo "selected";?>>가격비교 연관컨텐츠</option>
										<option value="comparison_market" <? if( $_show_bc_product_mode == "comparison_market" ) echo "selected";?>>가격비교 마켓</option>
<? } ?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<th>평점 기능</th>
							<td><label><input type="checkbox" name="bac_grade_active" value="Y" <? if( $_show_bc_grade_active == "Y" ) echo "checked";?>> 사용</label></td>
						</tr>
						<tr>
							<th>링크 기능</th>
							<td><label><input type="checkbox" name="bac_link_active" value="Y" <? if( $_show_bc_link_active == "Y" ) echo "checked";?>> 사용</label></td>
						</tr>
						<tr>
							<th>추천(좋아요) 기능</th>
							<td><label><input type="checkbox" name="bac_recom_active" value="Y" <? if( $_show_bc_recom_active == "Y" ) echo "checked";?>> 사용</label></td>
						</tr>
<? /* ?>
							<tr>
								<th>지역</th>
								<td>
									<label><input type="radio" name="show_area" value="N" <? if( $show_area == "N" ) echo "checked"; ?>> 비사용</label>
									<label><input type="radio" name="show_area" value="Y" <? if( $show_area == "Y" ) echo "checked"; ?>> 사용(선택)</label>
									<label><input type="radio" name="show_area" value="I" <? if( $show_area == "I" ) echo "checked"; ?>> 사용(필수)</label>
								</td>
							</tr>
							<tr>
								<th>평점</th>
								<td>
									<label><input type="radio" name="show_grade" value="N" <? if( $show_grade == "N" ) echo "checked"; ?>> 비사용</label>
									<label><input type="radio" name="show_grade" value="Y" <? if( $show_grade == "Y" ) echo "checked"; ?>> 사용(선택)</label>
									<label><input type="radio" name="show_grade" value="I" <? if( $show_grade == "I" ) echo "checked"; ?>> 사용(필수)</label>
								</td>
							</tr>

							<tr>
								<th>1:1 게시판</th>
								<td>
									<label><input type="radio" name="show_mtm" value="N" <? if( $show_mtm == "N" ) echo "checked"; ?>> 비사용</label>
									<label><input type="radio" name="show_mtm" value="Y" <? if( $show_mtm == "Y" ) echo "checked"; ?>> 사용</label>
								</td>
							</tr>
							<tr>
								<th>답변 </th>
								<td>
									<label><input type="radio" name="show_answer" value="N" <? if( $show_answer == "N" ) echo "checked"; ?>> 비사용</label>
									<label><input type="radio" name="show_answer" value="Y" <? if( $show_answer == "Y" ) echo "checked"; ?>> 사용</label>
								</td>
							</tr>
<? */ ?>

							<tr>
								<td colspan="2" class="table-sub-title"><i class="far fa-dot-circle"></i> 게시판 권한</td>
							</tr>
<tr>
	<th>목록 권한</th>
	<td>
		<div>
			<label onclick="accessLevel('list','off')"><input type="radio" name="bac_access_list_mode" value="Nomember" <? if($_show_bc_access_list_mode=="Nomember" OR !$_show_bc_access_list_mode ) echo "checked";?> > 비회원</label>
			<label onclick="accessLevel('list','on')"><input type="radio" name="bac_access_list_mode" value="Member" <? if($_show_bc_access_list_mode=="Member") echo "checked";?> > 회원</label>
			<label onclick="accessLevel('list','off')"><input type="radio" name="bac_access_list_mode" value="Admin" <? if($_show_bc_access_list_mode=="Admin") echo "checked";?> > 관리자</label>
			<label onclick="accessLevel('list','off')"><input type="radio" name="bac_access_list_mode" value="Group" <? if($_show_bc_access_list_mode=="Group") echo "checked";?> > 그룹</label>
		</div>
		<div id="access_list" style="margin-top:5px; display:<? if($_show_bc_access_list_mode!="Member") echo "none";?>;" >
			<input type='text' name='bac_access_list_level' id='bac_access_list_level' style="width:50px" value="<?=$_show_bc_access_list_level?>">
		</div>
	</td>
</tr>

<tr>
	<th>글읽기 권한</th>
	<td>
		<div>
			<label onclick="accessLevel('view','off')"><input type="radio" name="bac_access_view_mode" value="Nomember" <? if($_show_bc_access_view_mode =="Nomember" OR !$_show_bc_access_view_mode) echo "checked";?> > 비회원</label>
			<label onclick="accessLevel('view','on')"><input type="radio" name="bac_access_view_mode" value="Member" <? if($_show_bc_access_view_mode=="Member") echo "checked";?> > 회원</label>
			<label onclick="accessLevel('view','off')"><input type="radio" name="bac_access_view_mode" value="Admin" <? if($_show_bc_access_view_mode=="Admin") echo "checked";?> > 관리자</label>
			<label onclick="accessLevel('view','off')"><input type="radio" name="bac_access_view_mode" value="Group" <? if($_show_bc_access_view_mode=="Group") echo "checked";?> > 그룹</label>
		</div>
		<div id="access_view" style="margin-top:5px; display:<? if($_show_bc_access_view_mode!="Member") echo "none";?>;" >
				<input type='text' name='bac_access_view_level' id='bac_access_view_level' style="width:50px" value="<?=$_show_bc_access_view_level?>">
		</div>
	</td>
</tr>

<tr>
	<th>글쓰기 권한</th>
	<td>
		<div>
			<label onclick="accessLevel('write','off')"><input type="radio" name="bac_access_write_mode" value="Nomember" <? if($_show_bc_access_write_mode =="Nomember" OR !$_show_bc_access_write_mode) echo "checked";?> > 비회원</label>
			<label onclick="accessLevel('write','on')"><input type="radio" name="bac_access_write_mode" value="Member" <? if($_show_bc_access_write_mode=="Member") echo "checked";?> > 회원</label>
			<label onclick="accessLevel('write','off')"><input type="radio" name="bac_access_write_mode" value="Admin" <? if($_show_bc_access_write_mode=="Admin") echo "checked";?> > 관리자</label>
			<label onclick="accessLevel('write','off')"><input type="radio" name="bac_access_write_mode" value="Group" <? if($_show_bc_access_write_mode=="Group") echo "checked";?> > 그룹</label>
		</div>
		<div id="access_write" style="margin-top:5px; display:<? if($_show_bc_access_write_mode!="Member") echo "none";?>;" >
				<input type='text' name='bac_access_write_level' id='bac_access_write_level' style="width:50px" value="<?=$_show_bc_access_write_level?>">
		</div>
	</td>
</tr>

<tr>
	<th>댓글쓰기 권한</th>
	<td>
		<div>
			<label onclick="accessLevel('comment','off')"><input type="radio" name="bac_access_comment_mode" value="Nomember" <? if($_show_bc_access_comment_mode =="Nomember" OR !$_show_bc_access_comment_mode) echo "checked";?> > 비회원</label>
			<label onclick="accessLevel('comment','on')"><input type="radio" name="bac_access_comment_mode" value="Member" <? if($_show_bc_access_comment_mode=="Member") echo "checked";?> > 회원</label>
			<label onclick="accessLevel('comment','off')"><input type="radio" name="bac_access_comment_mode" value="Admin" <? if($_show_bc_access_comment_mode=="Admin") echo "checked";?> > 관리자</label>
			<label onclick="accessLevel('comment','off')"><input type="radio" name="bac_access_comment_mode" value="Group" <? if($_show_bc_access_comment_mode=="Group") echo "checked";?> > 그룹</label>
		</div>
		<div id="access_comment" style="margin-top:5px; display:<? if($_show_bc_access_comment_mode!="Member") echo "none";?>;" >
				<input type='text' name='bac_access_comment_level' id='bac_access_comment_level' style="width:50px" value="<?=$_show_bc_access_comment_level?>">
		</div>
	</td>
</tr>

<tr>
	<th>답변쓰기 권한</th>
	<td>
		<div>
			<label onclick="accessLevel('reply','off')"><input type="radio" name="bac_access_reply_mode" value="Nomember" <? if($_show_bc_access_reply_mode =="Nomember" OR !$_show_bc_access_reply_mode) echo "checked";?> > 비회원</label>
			<label onclick="accessLevel('reply','on')"><input type="radio" name="bac_access_reply_mode" value="Member" <? if($_show_bc_access_reply_mode=="Member") echo "checked";?> > 회원</label>
			<label onclick="accessLevel('reply','off')"><input type="radio" name="bac_access_reply_mode" value="Admin" <? if($_show_bc_access_reply_mode=="Admin") echo "checked";?> > 관리자</label>
			<label onclick="accessLevel('reply','off')"><input type="radio" name="bac_access_reply_mode" value="Group" <? if($_show_bc_access_reply_mode=="Group") echo "checked";?> > 그룹</label>
		</div>
		<div id="access_reply" style="margin-top:5px; display:<? if($_show_bc_access_reply_mode!="Member") echo "none";?>;" >
				<input type='text' name='bac_access_reply_level' id='bac_access_reply_level' style="width:50px" value="<?=$_show_bc_access_reply_level?>">
		</div>
	</td>
</tr>

							<tr>
								<td colspan="2" class="table-sub-title"><i class="far fa-dot-circle"></i> 차단/필터</td>
							</tr>
							<tr>
								<th>IP 차단</th>
								<td><textarea name="bac_block_ip" rows="" cols=""><?=$_show_bc_block_ip?></textarea></td>
							</tr>
							<tr>
								<th>단어 필터링</th>
								<td><textarea name="bac_filter" rows="" cols=""><?=$_show_bc_filter?></textarea>
								구분자 ( , )</td>
							</tr>
							<tr>
								<td colspan="2" class="table-sub-title"><i class="far fa-dot-circle"></i> 게시판 관리자(매니저)</td>
							</tr>
							<tr>
								<th>관리자 IDX</th>
								<td><input type='text' name='bac_manager_idx' id='bac_manager_idx' value="<?=$_show_bc_manager_idx?>"></td>
							</tr>

							<tr>
								<td colspan="2" class="table-sub-title"><i class="far fa-dot-circle"></i> 게시판 디자인</td>
							</tr>
							<tr>
								<th>게시판 레이아웃 (PC)</th>
								<td><input type='text' name='bac_layout_skin' id='bac_layout_skin' style="width:200px" value="<?=$_show_board_layout_skin?>"></td>
							</tr>
							<tr>
								<th>게시판 스킨 (PC)</th>
								<td><input type='text' name='bac_skin' id='bac_skin' style="width:200px" value="<?=$_show_board_skin?>"></td>
							</tr>
							<tr>
								<th>게시판 스킨 (모바일)</th>
								<td><input type='text' name='bac_skin_mo' id='bac_skin_mo' style="width:200px" value="<?=$_show_board_skin_mo?>"></td>
							</tr>
							<tr>
								<th>목록수</th>
								<td><input type='text' name='bac_list_num' id='bac_list_num' style="width:200px" value="<?=$_show_bc_list_num?>"></td>
							</tr>
						</table>
					</div>

					<div class="text-center m-t-10">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-submit" onclick="goSave();" > 
							<i class="far fa-check-circle"></i> <?=$page_btn_name?><!-- 신규 게시판 생성 -->
						</button>
					</div>

			</form>

			</div><!-- .treemenu-body -->
		</div><!-- .treemenu-wrap -->

<!-- 
		<table cellspacing="0" cellpadding="0" border="0" class="table-style2 treewrap">	
			<tr>
				<td class="treewrap-menu">
				</td>
				<td class="treewrap-margin" style="border:none;"></td>
				<td class="treewrap-body">

				</td>
			</tr>
		</table>
 -->

	</div>
</div>

<script type="text/javascript"> 
<!-- 
function accessLevel(id, mode){
	if( mode == "on" ){
		$("#access_"+id).show();
	}else{
		$("#access_"+id).hide();
	}
}

function goSave(){
	$("#board_config_form").submit();
}
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>