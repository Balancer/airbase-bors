<?
	include_once("funcs/modules/top-nav.php");
	echo get_top_nav(empty($GLOBALS['page']) ? "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" : $GLOBALS['page']);
?>
