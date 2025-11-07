<?
	ini_set("allow_url_fopen",1);
	include "simple_html_dom.php";

	$html = file_get_html('https://fb.oddsportal.com/ajax-next-games-odds/1/9/X0/20201210/1/yj6f3.dat');

	echo $html;
?>
