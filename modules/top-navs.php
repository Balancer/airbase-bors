<?
	include_once("top-navs.inc.php");
    print_top_navs(empty($GLOBALS['main_uri']) ? "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" : $GLOBALS['main_uri']);
?>
