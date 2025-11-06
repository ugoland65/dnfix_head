<?
$config_db = $docRoot."/library/db.php";

$connect = dbconn();
$mysqli = mysqli();

function dbconn() {
    global $config_db;
    $db_settings = parse_ini_file($config_db);
    @extract($db_settings);

	if( !$connect ){
		$connect = mysqli_connect($con_db_host,$con_db_id,$con_db_pass,$con_db_name);
		if (mysqli_connect_errno($connect)) {
			echo "데이터베이스 연결 실패: " . mysqli_connect_error();
		}
	}

	mysqli_query($connect,"SET NAMES 'utf8'");
	return $connect;
}

function mysqli() {
    global $config_db;
    $db_settings = parse_ini_file($config_db);
    @extract($db_settings);

	if( !$mysqli ){
		$mysqli = new mysqli($con_db_host,$con_db_id,$con_db_pass,$con_db_name);
	}
	return $mysqli;
}

function sql_query_error($query) {

    global $connect;
	$result = mysqli_query($connect, $query);

	if( !$result ){
		//$result = mysqli_error($connect);
		echo("E : " . mysqli_error($connect) . '<br>');
		echo("O : " . $query);
		exit;
	}

	return $result;
/*
	$temp_bar = "<br><br><br><br>";
	$result = mysql_query($query, $connect) or die("<font size='2'> Mysql_Query : ".$query."<br> Mysql_Error : ".mysql_error()."<br>Mysql Error Num : ".mysql_errno()."</font>".$temp_bar);
	return $result;
*/
}

function sql_result($result) {
	@mysql_result($result);
}

function sql_fetch_array($result) {
	return mysqli_fetch_array($result);
}

function sql_free_result($result) {
	@mysql_free_result($result);
}

function sql_num_rows($result) {
	return @mysqli_num_rows($result);
}

function sql_fetch_row($result) {
	return mysqli_fetch_row($result);
}

function sql_close($connect) {
	 @mysqli_close($connect);
}


function sql_counter($table_name, $where_str="", $field_name="*") {

    global $connect;

	$where_str = trim($where_str);
	if(strtolower(substr($where_str,0,5)) != "where" and $where_str) $where_str = "where ".$where_str;

	$query = " select count(".$field_name.") from ".$table_name." ".$where_str." ";
    $result = mysqli_query($connect, $query);
	$fetch_row = mysqli_fetch_row($result);

    return $fetch_row[0];
}


function sql_counter2($table_name, $where_str="", $group) {
    global $connect;

	$where_str = trim($where_str);
	$group = trim($group);

	$query = "select count(*) from ( select ".$group." from ".$table_name." ".$where_str." group by ".$group." ) T ";
    $result = mysqli_query($connect, $query);
	$fetch_row = mysqli_fetch_row($result);

    return $fetch_row[0];
}

/* -------------------------------------------------------------------------------------------------------------------- */

function wepix_query($query) {
    global $connect;
	$result = @mysqli_query($query,$connect);
	return $result;

}

function wepix_query_error($query) {

    global $connect;
	$result = mysqli_query($connect, $query);

	if( !$result ){
		//$result = mysqli_error($connect);
		echo("E : " . mysqli_error($connect) . '<br>');
		echo("O : " . $query);
		exit;
	}

	return $result;
/*
	$temp_bar = "<br><br><br><br>";
	$result = mysql_query($query, $connect) or die("<font size='2'> Mysql_Query : ".$query."<br> Mysql_Error : ".mysql_error()."<br>Mysql Error Num : ".mysql_errno()."</font>".$temp_bar);
	return $result;
*/
}

function wepix_result($result) {
	@mysql_result($result);
}

function wepix_fetch_array($result) {
	return mysqli_fetch_array($result);
}

function wepix_free_result($result) {
	@mysql_free_result($result);
}

function wepix_num_rows($result) {
	return @mysqli_num_rows($result);
}

function wepix_fetch_row($result) {
	return mysqli_fetch_row($result);
}

function wepix_close($connect) {
	 @mysqli_close($connect);
}


function wepix_counter($table_name, $where_str="", $field_name="*") {

    global $connect;

	$where_str = trim($where_str);
	if(strtolower(substr($where_str,0,5)) != "where" and $where_str) $where_str = "where ".$where_str;

	$query = " select count(".$field_name.") from ".$table_name." ".$where_str." ";
    $result = mysqli_query($connect, $query);
	$fetch_row = mysqli_fetch_row($result);

    return $fetch_row[0];
}


function wepix_counter2($table_name, $where_str="", $group) {
    global $connect;

	$where_str = trim($where_str);
	$group = trim($group);

	$query = "select count(*) from ( select ".$group." from ".$table_name." ".$where_str." group by ".$group." ) T ";
    $result = mysqli_query($connect, $query);
	$fetch_row = mysqli_fetch_row($result);

    return $fetch_row[0];
}
?>