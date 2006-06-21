<?
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

//	print_r($_GET);

	require_once("funcs/users.php");
    require_once("handlers.php");

	$_SERVER['HTTP_HOST'] = str_replace(':80', '', $_SERVER['HTTP_HOST']);

    $_SERVER['REQUEST_URI'] = preg_replace("!^(.+?)\?.*?$!", "$1", $_SERVER['REQUEST_URI']);

//	if($_SERVER['HTTP_HOST'] == "la2.wrk.ru")	
//		echo("GET='".print_r($_GET,true)."', REQUEST_URI='{$_SERVER['REQUEST_URI']}'<br><br>");

	$uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	$parse = parse_url($uri);

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



//	exit();

//	echo "<xmp>".print_r($GLOBALS['cms_patterns'], true)."</xmp>";
//	print_r($_GET);

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
		
		if(!empty($_GET))
		{
			$ret = do_action_handlers($uri, $uri, $GLOBALS['cms_actions']);
		
			if($ret === true)
				return;

			if($ret !== false)
				$uri = $ret;
		}
		
		$ret = do_uri_handlers($uri, $uri, $GLOBALS['cms_patterns']);
		
		if($ret === true)
			return;

		if($ret !== false)
			$uri = $ret;
	}

//   echo "<pre>";	print_r($_SERVER);    echo "</pre>";
	echo "<pre>";

	if(empty($title))
		$title='';
		
	echo ec("Страница '$uri' не найдена. Попробуйте <a href=\"$uri?edit\">создать её</a>");
?>
