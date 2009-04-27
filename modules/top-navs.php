<?php

	if(!($main_obj = bors()->main_object()))
		$main_obj = object_load('http://'.@$_SERVER['HTTP_HOST'].'/'.@$_SERVER['REQUEST_URI']);

	if($mod = object_load('module_nav_top', $main_obj))
		echo $mod->body();

	return;
/*
	include_once("top-navs.inc.php");

	$uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

	if(!empty($GLOBALS['main_uri']))
		$uri = $GLOBALS['main_uri'];

    print_top_navs($uri);
*/
