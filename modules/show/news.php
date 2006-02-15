<?
	include_once("news.inc.php");
    echo module_show_news(empty($GLOBALS['module_data']['root']) ? $GLOBALS['main_uri'] : $GLOBALS['module_data']['root']);
?>
