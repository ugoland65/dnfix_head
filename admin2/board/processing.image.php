<?
	include "../lib/inc_common.php";
	include('../../class/image.php'); //이미지 처리 클래스

	$_b_token = securityVal($b_token);
	$_temp_folder = securityVal($temp_folder);

	if( !$_b_token ){
		echo "|Erorr|토큰 오류|";
		exit;
	}

	//임시폴더가 없다면 생성한다
	$file_dir_name = "../../data/board/temp/".$_temp_folder;
/*
		echo "|Erorr|".$file_dir_name."|";
		exit;
*/
	if(!is_dir($file_dir_name)){
/*
		mkdir($file_dir_name,0777,true);
*/
		@mkdir($file_dir_name, 0777);
		@chmod($file_dir_name, 0777);

	}

	if ( $_FILES['fileObj']['name'] ) {
		if (!$_FILES['fileObj']['error']) {

			$tmp_file = $_FILES['fileObj']['tmp_name'];
			$timg = @getimagesize($tmp_file);
    
			//확장자 분리
			$_ary_ext = explode('.', $_FILES['fileObj']['name']);
			
			//파일명에 ( . ) 들어갔을경우 씨부랄
			$_ext_index = count($_ary_ext) - 1;

			//이미지인지 체크
			if(in_array($timg[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))){

				$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
				shuffle($chars_array);
				$shuffle = implode('', $chars_array);
				$savename = $_b_token."_".substr($shuffle,0,8)."_".$check_time.".".$_ary_ext[$_ext_index];

				$destination = $file_dir_name."/".$savename;
				move_uploaded_file($tmp_file, $destination);

				echo "|Processing_Complete|처리완료|".$destination."|".$savename;
				exit;

			}else{
				echo "|Erorr|이미지만 등록 가능합니다.|";
				exit;
			}

		}
	}

	echo "|Erorr|내용에 올바르지 않은 코드가 다수 포함되어 있습니다.|";
	exit;


exit;
?>