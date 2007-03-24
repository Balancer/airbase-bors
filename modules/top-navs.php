<?
	include_once("top-navs.inc.php");

	$uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

	if(!empty($GLOBALS['main_uri']))
		$uri = $GLOBALS['main_uri'];

    print_top_navs($uri);
