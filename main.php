<?
    list($usec, $sec) = explode(" ",microtime());
    $GLOBALS['cms']['start_microtime'] = ((float)$usec + (float)$sec);

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');
    require_once('config.php');

    header("Content-Type: text/html; charset={$GLOBALS['cms']['charset']}");
    header('Content-Language: ru');
    ini_set('default_charset',$GLOBALS['cms']['charset']);
    setlocale(LC_ALL, $GLOBALS['cms']['locale']);

//	print_r($_GET);

	include_once('funcs/users.php');
    require_once('handlers.php');

	$GLOBALS['cms_aliases'] = array();

	handlers_load($GLOBALS['cms']['base_dir'].'/handlers');
	handlers_load("{$GLOBALS['cms']['base_dir']}/vhosts/{$_SERVER['HTTP_HOST']}/handlers");
	handlers_load($GLOBALS['cms']['local_dir'].'/handlers');
	handlers_load($GLOBALS['cms']['base_dir'].'/handlers-post');
	
	$_SERVER['HTTP_HOST'] = str_replace(':80', '', $_SERVER['HTTP_HOST']);

    $_SERVER['REQUEST_URI'] = preg_replace("!^(.+?)\?.*?$!", "$1", $_SERVER['REQUEST_URI']);

//    exit("GET='".print_r($_GET,true)."', REQUEST_URI='{$_SERVER['REQUEST_URI']}'<br><br>");
	$uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	$GLOBALS['ref'] = @$_SERVER['HTTP_REFERER'];

	include_once("funcs/logs.php");
	log_session_update();
	
	if(empty($GLOBALS['main_uri']))
		$GLOBALS['main_uri'] = $uri;

	if(isset($_POST['LoginForm']))
	{
		$us = new User();

		$GLOBALS['page_data']['title'] = ec("Вход");

		if($err = $us->do_login($_POST['FLogin'], $_POST['FPassword'], false))
			return error_messge($err, $uri);

		return messge(ec("Вы успешно авторизовались"), $uri);
	}

	$parse = parse_url($uri);

	$GLOBALS['cms']['page_path'] = preg_replace("!/~[\w\-]+/$!","/",$parse['path']);
	$GLOBALS['cms']['page_number'] = 1;

    foreach($GLOBALS['cms_aliases'] as $uri_pattern=>$func)
    {
//		echo "<pre>Test alias pattern '$uri_pattern' to '$uri' for $func</pre>\n";
		if(preg_match($uri_pattern, $uri, $m))
		{
//			echo "ok!";
            $res = $func($uri, $m);
            if($res === true)
                return;
            if($res !== false)
                $uri = $res;
		}
	}

//	exit();

//	echo "<xmp>".print_r($GLOBALS['cms_patterns'], true)."</xmp>";
//	print_r($_GET);

//	$GLOBALS['cms']['action'] = '';

	if(!empty($_GET))
	{
	    foreach($GLOBALS['cms_actions'] as $action=>$func)
    	{
//			echo "<pre>Test action '$action' to '$uri'</pre>\n";
			if(isset($_GET[$action]))
			{
				$GLOBALS['cms']['action'] = $action;
				$res = $func($uri, $action);
            	if($res === true)
   	            	return;
	       	    if($res !== false)
    	       	    $uri = $res;
			}
		}
	}

    foreach($GLOBALS['cms_patterns'] as $uri_pattern=>$func)
    {
//		echo "<pre>Test pattern '$uri_pattern' to '$uri'</pre>\n";
		if(preg_match($uri_pattern, $uri, $m))
		{
//			echo "ok!";
            $res = $func($uri, $m);
            if($res === true)
                return;
            if($res !== false)
                $uri = $res;
		}
	}


//   echo "<pre>";	print_r($_SERVER);    echo "</pre>";
	echo "<pre>";

	if(empty($title))
		$title='';
		
	echo ec("Страница '$uri' не найдена. Попробуйте <a href=\"$uri?edit\">создать её</a>");
?>
