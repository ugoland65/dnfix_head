<?
$pageGroup = "login";
$pageName = "login_out";

include "../lib/inc_common.php";

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

session_destroy();
?>
<script>
	location.replace('<?=_A_PATH_LOGIN?>');
</script>
<?
exit;
?>