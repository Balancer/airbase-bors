<?
	require_once("handlers/load.php");
	require_once("handlers/exec.php");

function do_uri_handlers($uri, $match, $handlers)
{
	$ret = false;

	foreach ($handlers as $uri_pattern => $func)
	{
		if (!empty ($_GET['debug']))
			echo "<tt>Test pattern '$uri_pattern' to '$match' by $func()</tt><br />\n";
		$m = array ();
//		echo $uri_pattern."<br/>";
		if (preg_match($uri_pattern, $match, $m))
		{
//			echo "... ok!";
//			echo "Call $func('$uri')<br />";
			$res = $func ($uri, $m);
			if ($res === true)
			{
				if (isset ($_GET['debug']))
					echo "Loaded by pattern $uri_pattern=>$func<br/>";
				return true;
			}
			if ($res !== false)
				$ret = $uri = $res;
		}
	}

	return $ret;
}


	function do_plugin_action_handlers($uri, $match, $path)
	{
		$GLOBALS['cms_actions'] = array ();
		$GLOBALS['cms_patterns'] = array ();
	
		handlers_load_dir($path);
		// match[3] - это путь относительно базы плагинов.
		// Там формат шаблона (/path/to/plugin/)(plugin_name)(/plugin/sub/path/)
		$ret = do_action_handlers($uri, $match[3], $GLOBALS['cms_actions']);

		// Если не было локального обработчика - пробуем глобальный.
		if($ret === false)
			$ret = do_action_handlers($uri, $match[3], $GLOBALS['cms_actions']);

		return $ret;
	}

	function do_action_handlers($uri, $match, $actions)
	{
		$ret = false;
	   	foreach($actions as $action => $reg)
   		{
//		echo "<pre>Test action '$action' to '$uri' for ".print_r($reg, true)."</pre>\n";
			if(isset($_GET[$action]) || isset($_POST[$action]))
			{
//			echo "*<br/>";
				$GLOBALS['cms']['action'] = $action;
				foreach($reg as $regexp => $func)
				{
					if(!preg_match($regexp, $match, $m))
						continue;
					$res = $func($uri, $action, $m);
    	    		if($res === true)
	   	    	    	return true;
   			    	if($res !== false)
       			    	$ret = $uri = $res;
				}
			}
		}
	
		return $ret;
	}
