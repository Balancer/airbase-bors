<?php

define('BORS_CORE', '/var/www/bors/composer/vendor/balancer/bors-core');
define('BORS_3RD_PARTY', '/var/www/repos/bors-third-party');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');

require_once(BORS_CORE.'/init.php');

	$dir = dirname($_SERVER['PHP_SELF']);
	if($dir == "/")
		$dir = "";
	$pun_config['root_uri'] = $pun_config['o_base_url'] = "http://{$_SERVER['HTTP_HOST']}$dir";

	if(preg_match("!/forum/topic/\d+/(\d+)/?$!", $_SERVER['REQUEST_URI'], $m))
	{
		$_GET['id'] = $m[1];
		include("viewtopic.php");
		exit();
	}

	if(preg_match("!/forum/topic/\d+/(\d+),(\d+)/?$!", $_SERVER['REQUEST_URI'], $m))
	{
		$_GET['id'] = $m[1];
		$_GET['p'] = $m[2];
		include("viewtopic.php");
		exit();
	}

	if(preg_match("!/forum/topic/\d+/(\d+),(\w+)/?$!", $_SERVER['REQUEST_URI'], $m))
	{
		$_GET['id'] = $m[1];
		$_GET['action'] = $m[2];
		include("viewtopic.php");
		exit();
	}

	if(
			preg_match("!/forum/(\d+)/?$!", $_SERVER['REQUEST_URI'], $m)
		||	preg_match("!/forum/\d+/viewforum\.php\?id=(\d+)$!", $_SERVER['REQUEST_URI'], $m)
	)
	{
		$_GET['id'] = $m[1];
		include("viewforum.php");
		exit();
	}

	if(
		preg_match("!/forum/\d+/post\.php\?tid=(\d+)$!", $_SERVER['REQUEST_URI'], $m)
	)
	{
		$_GET['tid'] = $m[1];
		include("post.php");
		exit();
	}

	if(preg_match("!/forum/?$!", $_SERVER['REQUEST_URI'], $m))
	{
		include("index.php");
		exit();
	}

	if(preg_match("!/forum/\d+/viewtopic\.php\?id=(\d+)&p=(\d+)$!", $_SERVER['REQUEST_URI'], $m))
	{
		$_GET['id'] = $m[1];
		$_GET['p'] = $m[2];
		include("viewtopic.php");
		exit();
	}

	if(preg_match("!/forum/(\d+)/updates\.js$!", $_SERVER['REQUEST_URI'], $m))
	{
		include_once("tools/forum-updates-make.php");
		echo punbb_forum_updates_make($m[1]);
		exit();
	}

	if(preg_match("!/base\.css$!", $_SERVER['REQUEST_URI'], $m))
	{
		go("{$pun_config['root_uri']}/style/imports/base.css");
		exit();
	}

	$fh = fopen("{$_SERVER['DOCUMENT_ROOT']}/forum/404.log", "at");
	fwrite($fh, $_SERVER['REQUEST_URI']."|".@$_SERVER['HTTP_REFERER']."\n");
	fclose($fh);
	go("http://www.balancer.ru/forum/");
