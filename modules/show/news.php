<?
	include_once("news.inc.php");

//	echo $GLOBALS['main_uri'];
	echo module_show_news(empty($GLOBALS['module_data']['news_root']) ? $GLOBALS['main_uri'] : $GLOBALS['module_data']['news_root']);
?>
