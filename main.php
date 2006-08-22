<?
//	ini_set("xdebug.profiler_enable", "1");

//	print_r($_SERVER);

    list($usec, $sec) = explode(" ",microtime());
    $GLOBALS['stat']['start_microtime'] = ((float)$usec + (float)$sec);

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');
    require_once("config.php");

    header("Content-Type: text/html; charset={$GLOBALS['cms']['charset']}");
    header('Content-Language: ru');
    ini_set('default_charset',$GLOBALS['cms']['charset']);
    setlocale(LC_ALL, $GLOBALS['cms']['locale']);

	if(empty($GLOBALS['cms']['only_load']) && empty($_GET) && preg_match("!^(.+?)\?(.+)$!", $_SERVER['REQUEST_URI'], $m))
	{
		$_SERVER['REQUEST_URI'] = $m[1];
		foreach(split("&", $m[2]) as $pair)
		{
			@list($var, $val) = split("=", $pair);
			$_GET[$var] = "$val";
			$_POST[$var] = "$val";
		}
	}

//	print_r($_GET);

	require_once("funcs/users.php");
    require_once("handlers.php");

	if(empty($GLOBALS['cms']['only_load']))
	{
		$_SERVER['HTTP_HOST'] = str_replace(':80', '', $_SERVER['HTTP_HOST']);

    	$_SERVER['REQUEST_URI'] = preg_replace("!^(.+?)\?.*?$!", "$1", $_SERVER['REQUEST_URI']);
	}
	
//	if($_SERVER['HTTP_HOST'] == "la2.wrk.ru")	
//		echo("GET='".print_r($_GET,true)."', REQUEST_URI='{$_SERVER['REQUEST_URI']}'<br><br>");

	$uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	
//	$uri = preg_replace("!/[^/]+\.html$!", "/", $uri);
	
	$parse = parse_url($uri);
	
    require_once("funcs/CacheStaticFile.php");
	$cs = new CacheStaticFile($uri);
	if(!empty($GLOBALS['cms']['cache_static']) && empty($_GET) && empty($_POST) && ($cs_uri = $cs->get_name($uri)) && file_exists($cs->get_file($uri)))
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

	include_once("funcs/logs.php");
	log_session_update();

	if(isset($_POST['LoginForm']))
	{
		include_once("funcs/modules/messages.php");
		
		$us = new User();

		$GLOBALS['page_data']['title'] = ec("Вход");

		if($err = $us->do_login($_POST['FLogin'], $_POST['FPassword'], false))
			return error_messge($err, $uri);

		return message(ec("Вы успешно авторизовались"), $uri);
	}


//	echo "<xmp>".print_r($_POST, true)."</xmp>";	print_r($_GET);	exit();

//	$GLOBALS['cms']['action'] = '';

	foreach(split(' ','handlers/pre handlers handlers/post') as $sub_path)
	{
		$GLOBALS['cms_patterns'] = array();
		$GLOBALS['cms_actions']  = array();

//		echo "Load $sub_path for $uri<br />\n";
	
		foreach(array($GLOBALS['cms']['local_dir'],
					"{$GLOBALS['cms']['base_dir']}/vhosts/{$_SERVER['HTTP_HOST']}",
					$GLOBALS['cms']['base_dir']) as $base_path)
		{
//			if(!empty($_GET['dbg']))
//				DebugBreak();

			handlers_load("$base_path/$sub_path");
		}
		
		if(empty($GLOBALS['cms']['only_load']) && (!empty($_GET) || !empty($_POST)))
		{
//			echo "=====================================================";
			$ret = do_action_handlers($uri, $uri, $GLOBALS['cms_actions']);
//			exit(print_r($_GET, true));
		
			if($ret === true)
				return;

			if($ret !== false)
				$uri = $ret;
		}

//		echo "********do_uri_handlers($uri, $uri, ".print_r($GLOBALS['cms_patterns'], true)."**************<br/>";
		
		$ret = do_uri_handlers($uri, $uri, $GLOBALS['cms_patterns']);
		
		if($ret === true)
			return;

		if($ret !== false)
			$uri = $ret;
	}

	if(@$GLOBALS['cms']['only_load'])
		return;

//   echo "<pre>";	print_r($_SERVER);    echo "</pre>";
	echo "<pre>";

	if(empty($title))
		$title='';
		
	echo ec("Страница '$uri' не найдена. Попробуйте <a href=\"$uri?edit\">создать её</a>");
?>
