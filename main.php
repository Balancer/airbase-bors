<?
//	ini_set("xdebug.profiler_enable", "1");

//	exit($_SERVER['REQUEST_URI']);


	if($_SERVER['REQUEST_URI'] == '/cms/main.php')
	{
		@file_put_contents($file = $_SERVER['DOCUMENT_ROOT']."/cms/logs/main-php-referers.log", @$_SERVER['HTTP_REFERER'] . "; IP=".@$_SERVER['REMOTE_ADDR']."; UA=".@$_SERVER['HTTP_USER_AGENT']."\n", FILE_APPEND);
		@chmod($file, 0666);
		exit("Link error");
	}

	global $client;
	$client['is_bot'] = false;
	foreach(array(
			'yahoo' => 'Yahoo',
			'rambler' => 'Rambler',
			'google' => 'Google',
			'yandex' => 'Yandex',
		) as $pattern => $bot)
	{
		if(preg_match("!".$pattern."!i", $_SERVER['HTTP_USER_AGENT']))
		{
			$client['is_bot'] = $bot;
			break;
		}
	}

	if($client['is_bot'] && rand(0,100) < 50)
	{
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		header('Retry-After: 600');

		@file_put_contents($file = $_SERVER['DOCUMENT_ROOT']."/cms/logs/blocked-bots.log", $_SERVER['REQUEST_URI']."/".@$_SERVER['HTTP_REFERER'] . "; IP=".@$_SERVER['REMOTE_ADDR']."; UA=".@$_SERVER['HTTP_USER_AGENT']."\n", FILE_APPEND);
		@chmod($file, 0666);
		exit("Service Temporarily Unavailable");
	}
	
    list($usec, $sec) = explode(" ",microtime());
    $GLOBALS['stat']['start_microtime'] = ((float)$usec + (float)$sec);

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');

    require_once("config.php");

    @header("Content-Type: text/html; charset={$GLOBALS['cms']['charset']}");
    @header('Content-Language: ru');
    ini_set('default_charset',$GLOBALS['cms']['charset']);
    setlocale(LC_ALL, $GLOBALS['cms']['locale']);

	if(empty($GLOBALS['cms']['only_load']) && empty($_GET) && preg_match("!^(.+?)\?(.+)$!", $_SERVER['REQUEST_URI'], $m))
	{
		$_SERVER['QUERY_STRING'] = $m[2];
		$_SERVER['REQUEST_URI'] = $m[1];
		foreach(split("&", $m[2]) as $pair)
		{
			@list($var, $val) = split("=", $pair);
			$_GET[$var] = "$val";
			$_POST[$var] = "$val";
		}
	}

	$_GET = array_merge($_GET, $_POST);

//	print_r($_POST);
	require_once("funcs/templates/global.php");
	require_once("funcs/users.php");
    require_once("funcs/handlers.php");

	if(empty($GLOBALS['cms']['only_load']))
	{
		$_SERVER['HTTP_HOST'] = str_replace(':80', '', $_SERVER['HTTP_HOST']);

    	$_SERVER['REQUEST_URI'] = preg_replace("!^(.+?)\?.*?$!", "$1", $_SERVER['REQUEST_URI']);
	}
	
//	if($_SERVER['HTTP_HOST'] == "la2.wrk.ru")	
//		echo("GET='".print_r($_GET,true)."', REQUEST_URI='{$_SERVER['REQUEST_URI']}'<br><br>");

	$uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	
//	$uri = preg_replace("!/[^/]+\.html$!", "/", $uri);

    @header("X-Bors: $uri");
	
	$parse = parse_url($uri);
	
    require_once("include/classes/cache/CacheStaticFile.php");
	$cs = &new CacheStaticFile($uri);
	if(!empty($GLOBALS['cms']['cache_static']) 
		&& empty($_GET) 
		&& empty($_POST) 
		&& ($cs_uri = $cs->get_name($uri)) 
		&& file_exists($cs->get_file($uri)))
	{
		include_once("funcs/navigation/go.php");
		go($cs_uri); 
		exit();
	}

//	exit($uri);

//	if(preg_match("!/~page(\d+)/?$!", $parse['path'], $m))
//		$GLOBALS['cms']['page_number'] = max(1, intval($m[1]));
//	else
	$GLOBALS['cms']['page_number'] = 1;
//	$uri = "http://{$_SERVER['HTTP_HOST']}".preg_replace("!/~[\w\-]+/$!","/",$_SERVER['REQUEST_URI']);

	if(empty($GLOBALS['main_uri']))
		$GLOBALS['main_uri'] = $uri;

	$GLOBALS['cms']['page_path'] = $GLOBALS['main_uri'];

	$GLOBALS['ref'] = @$_SERVER['HTTP_REFERER'];

	if(empty($GLOBALS['cms']['disable']['log_session']))
	{
		include_once("funcs/logs.php");
		log_session_update();
	}
	
	include_once("funcs/handlers.php");

	$GLOBALS['cms_patterns'] = array();
	$GLOBALS['cms_actions']  = array();

	handlers_load();

	if(!empty($GLOBALS['cms']['only_load']))
		return;
		
	$ret = handlers_exec();

	global $bors;
	if(!empty($bors) && is_object($bors))
		$bors->changed_save();

    list($usec, $sec) = explode(" ",microtime());
    $time = ((float)$usec + (float)$sec) - $GLOBALS['stat']['start_microtime'];

	if($time > 1)
	{
		@file_put_contents($file = $_SERVER['DOCUMENT_ROOT']."/cms/logs/timing.log", $time . " [".$uri . "]: " . @$_SERVER['HTTP_REFERER'] . "; IP=".@$_SERVER['REMOTE_ADDR']."; UA=".@$_SERVER['HTTP_USER_AGENT']."\n", FILE_APPEND);
		@chmod($file, 0666);
	}
	
	if($ret === true)
		return;

	if($ret !== false)
		$uri = $ret;


	echo "<pre>";

	if(empty($title))
		$title='';

	@file_put_contents($file = $_SERVER['DOCUMENT_ROOT']."/cms/logs/404.log", "$uri <= ".@$_SERVER['HTTP_REFERER'] . "; IP=".@$_SERVER['REMOTE_ADDR']."; UA=".@$_SERVER['HTTP_USER_AGENT']."\n", FILE_APPEND);
	@chmod($file, 0666);

//	return go("/404.html", true);
		
	echo ec("Страница '$uri' не найдена. Попробуйте <a href=\"$uri?edit\">создать её</a>");
	echo "</pre>";
