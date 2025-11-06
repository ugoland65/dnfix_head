<?
include "../lib/inc_common.php";

$_a_mode = securityVal($action_mode);

// ******************************************************************************************************************
// 개발자 설정
// ******************************************************************************************************************
if( $_a_mode == "developer" ){

	$_sitename = securityVal($sitename);
	$_logofile = securityVal($logofile);
	$_logofile_login = securityVal($logofile_login);
	$_copyright = securityVal($copyright);
	$_browser_title = securityVal($browser_title);

	$_gnb_active_booking = securityVal($gnb_active_booking);
	$_gnb_active_member_guide = securityVal($gnb_active_member_guide);
	$_gnb_active_partner = securityVal($gnb_active_partner);
	$_gnb_active_comparison = securityVal($gnb_active_comparison);
	$_gnb_active_product2 = securityVal($gnb_active_product2);
	$_gnb_active_product = securityVal($gnb_active_product);
	$_gnb_dir_config = securityVal($gnb_dir_config);
	$_gnb_active_osi = securityVal($gnb_active_osi);

	$_d_capacity_total = securityVal($d_capacity_total);
	$_d_capacity_text = securityVal($d_capacity_text);

	$_ad_config_save = "<?
	/*
		cf_salt_ad_glob_sitename = \"".trim($_sitename)."\"
		cf_salt_ad_glob_logofile = \"".trim($_logofile)."\"
		cf_salt_ad_glob_logofile_login = \"".trim($_logofile_login)."\"
		cf_salt_ad_glob_copyright = \"".trim($_copyright)."\"
		cf_salt_ad_glob_browser_title = \"".trim($_browser_title)."\"

		cf_salt_ad_glob_gnb_active_booking = \"".trim($_gnb_active_booking)."\"
		cf_salt_ad_glob_gnb_active_member_guide = \"".trim($_gnb_active_member_guide)."\"
		cf_salt_ad_glob_gnb_active_partner = \"".trim($_gnb_active_partner)."\"
		cf_salt_ad_glob_gnb_active_comparison = \"".trim($_gnb_active_comparison)."\"
		cf_salt_ad_glob_gnb_active_product2 = \"".trim($_gnb_active_product2)."\"
		cf_salt_ad_glob_gnb_active_product = \"".trim($_gnb_active_product)."\"
		cf_salt_ad_glob_gnb_dir_config = \"".trim($_gnb_dir_config)."\"
		cf_salt_ad_glob_gnb_active_osi = \"".trim($_gnb_active_osi)."\"

		cf_salt_ad_glob_d_capacity_total = \"".trim($_d_capacity_total)."\"
		cf_salt_ad_glob_d_capacity_text = \"".trim($_d_capacity_text)."\"
	*/
	?>";

	$fp = fopen("../../config_file/cff_ad_glob.php","w");
	fwrite($fp,$_ad_config_save);
	fclose($fp);

	$_ws_mode = securityVal($ws_mode);
	$_ws_code = securityVal($ws_code);

	$_index_path = securityVal($index_path);
	$_index_path_mobile = securityVal($index_path_mobile);
	$_skin_name = securityVal($skin_name);
	$_skin_name_mobile = securityVal($skin_name_mobile);
	$_sys_user_email_id = securityVal($sys_user_email_id);
	$_sys_real_name = securityVal($sys_real_name);
	$_sys_individual_function = securityVal($individual_function);
	$_sys_individual_variable = securityVal($individual_variable);
	$_sys_folder_dir_admin = securityVal($sys_folder_dir_admin);
	$_sys_folder_dir_pc = securityVal($sys_folder_dir_pc);
	$_sys_folder_dir_mobile = securityVal($sys_folder_dir_mobile);

	$_all_config_save = "<?
	/*
		cf_salt_all_glob_ws_mode = \"".trim($_ws_mode)."\"
		cf_salt_all_glob_ws_code = \"".trim($_ws_code)."\"
		cf_salt_all_glob_index_path = \"".trim($_index_path)."\"
		cf_salt_all_glob_index_path_mobile = \"".trim($_index_path_mobile)."\"
		cf_salt_all_glob_skin_name = \"".trim($_skin_name)."\"
		cf_salt_all_glob_skin_name_mobile = \"".trim($_skin_name_mobile)."\"
		cf_salt_all_glob_sys_user_email_id = \"".trim($_sys_user_email_id)."\"
		cf_salt_all_glob_sys_real_name = \"".trim($_sys_real_name)."\"
		cf_salt_all_glob_sys_individual_function = \"".trim($_sys_individual_function)."\"
		cf_salt_all_glob_sys_individual_variable = \"".trim($_sys_individual_variable)."\"
		cf_salt_all_glob_sys_folder_dir_admin = \"".trim($_sys_folder_dir_admin)."\"
		cf_salt_all_glob_sys_folder_dir_pc = \"".trim($_sys_folder_dir_pc)."\"
		cf_salt_all_glob_sys_folder_dir_mobile = \"".trim($_sys_folder_dir_mobile)."\"
	*/
	?>";

	$fp = fopen("../../config_file/cff_all_glob.php","w");
	fwrite($fp,$_all_config_save);
	fclose($fp);

	msg("저장 완료!", _A_PATH_CONFIG_DEVELOPER);
// ******************************************************************************************************************
// 오픈그래프 설정
// ******************************************************************************************************************
}elseif( $_a_mode == "openGraph" ){

	$_title = securityVal($title);
	$_subject = securityVal($subject);
	$_description = securityVal($description);
	$_keywords = securityVal($keywords);

	$_og_site_name = securityVal($og_site_name);
	$_og_title = securityVal($og_title);
	$_og_type = securityVal($og_type);
	$_og_description = securityVal($og_description);
	$_og_image = securityVal($og_image);
	$_og_url = securityVal($og_url);

	$_tw_title = securityVal($tw_title);
	$_tw_card = securityVal($tw_card);
	$_tw_description = securityVal($tw_description);
	$_tw_image = securityVal($tw_image);
	$_tw_domain = securityVal($tw_domain);

	$_open_graph_config_save = "<?
	/*
		cf_salt_open_graph_title = \"".trim($_title)."\"
		cf_salt_open_graph_subject = \"".trim($_subject)."\"
		cf_salt_open_graph_description = \"".trim($_description)."\"
		cf_salt_open_graph_keywords = \"".trim($_keywords)."\"

		cf_salt_open_graph_og_site_name = \"".trim($_og_site_name)."\"
		cf_salt_open_graph_og_type = \"".trim($_og_type)."\"
		cf_salt_open_graph_og_title = \"".trim($_og_title)."\"
		cf_salt_open_graph_og_description = \"".trim($_og_description)."\"
		cf_salt_open_graph_og_img = \"".trim($_og_image)."\"
		cf_salt_open_graph_og_url = \"".trim($_og_url)."\"

		cf_salt_open_graph_tw_card = \"".trim($_tw_card)."\"
		cf_salt_open_graph_tw_title = \"".trim($_tw_title)."\"
		cf_salt_open_graph_tw_description = \"".trim($_tw_description)."\"
		cf_salt_open_graph_tw_image = \"".trim($_tw_image)."\"
		cf_salt_open_graph_tw_domain = \"".trim($_tw_domain)."\"
	*/
	?>";

	$fp = fopen("../../config_file/cff_open_graph.php","w");
	fwrite($fp,$_open_graph_config_save);
	fclose($fp);

	msg("저장 완료!", _A_PATH_CONFIG_OPEN_GRAPH);
}

exit;
?>