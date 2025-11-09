<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
$work_mode = "view_mode";
include dirname(__DIR__, 4) . '/admin2/lib/inc_common.php';
include dirname(__DIR__, 4) . '/admin2/layout/header.php';
?>
<?= $content ?? '' ?>
<?php 
include dirname(__DIR__, 4) . '/admin2/layout/footer.php'; 
?>