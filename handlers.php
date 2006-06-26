<?
function register_handler($uri_pattern, $func = NULL)
{
	register_uri_handler($uri_pattern, $func);
}

function register_uri_handler($uri_pattern, $func = NULL)
{
	if ($func == NULL)
	{
		$func = $uri_pattern;
		$uri_pattern = "!.*!";
	}

	$GLOBALS['cms_patterns'][$uri_pattern] = $func;
}

function register_action_handler($regexp, $action_type, $func = NULL)
{
	if (!$func)
	{
		$func = $action_type;
		$action_type = $regexp;
		$regexp = "!.*!";
	}

	if (empty ($GLOBALS['cms_actions'][$action_type][$regexp]))
		$GLOBALS['cms_actions'][$action_type][$regexp] = $func;
}

function register_alias($uri_regexp, $function)
{
	$GLOBALS['cms_aliases'][$uri_regexp] = $function;
}

function handlers_load($dir = 'handlers')
{
//	echo "<b>Load handlers from $dir</b><br/>";

	if (!is_dir($dir))
		return;

	$files = array ();

	if ($dh = opendir($dir))
		while (($file = readdir($dh)) !== false)
			if (substr($file, 0, 1) != '.')
				array_push($files, $file);

	closedir($dh);

	sort($files);

//	if(!empty($_GET['dbg']))
//		DebugBreak();

	foreach ($files as $file)
	{
//		echo "load $file<br>\n";

		if (substr($file, -4) == '.php')
			include_once ("$dir/$file");
		elseif (is_dir("$dir/$file") && !preg_match("!(post|pre)$!", $file)) handlers_load("$dir/$file");
	}
}

function hts_data_prehandler_add($regexp, $data_key, $func)
{
	if (!empty ($_GET['debug']))
		echo "<small>Add pre function $func to uri like '$regexp' for key $data_key</small><br />";

	$GLOBALS['cms']['data_prehandler'][$data_key][$regexp] = $func;
	krsort($GLOBALS['cms']['data_prehandler'][$data_key]);
}

function hts_data_posthandler_add($regexp, $data_key, $function)
{
	if (!empty ($_GET['debug']))
		echo "<small>Add post function $function to uri like '$regexp' for key $data_key</small><br />";

	$GLOBALS['cms']['data_posthandler'][$data_key][$regexp] = $function;
	krsort($GLOBALS['cms']['data_posthandler'][$data_key]);
}

function do_uri_handlers($uri, $match, $handlers)
{
	$ret = false;

	foreach ($handlers as $uri_pattern => $func)
	{
		if (!empty ($_GET['debug']))
			echo "<tt>Test pattern '$uri_pattern' to '$uri'</tt><br/>\n";
		$m = array ();
		if (preg_match($uri_pattern, $match, $m))
		{
			//			echo "ok!";
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

function do_plugin_uri_handlers($uri, $match, $path)
{
	$save = $GLOBALS['cms_patterns'];
	$GLOBALS['cms_patterns'] = array ();

	handlers_load($path);
	// match[3] - это путь относительно базы плагинов.
	// Там формат шаблона (/path/to/plugin/)(plugin_name)(/plugin/sub/path/)
	$ret = do_uri_handlers($uri, $match[3], $GLOBALS['cms_patterns']);

	$GLOBALS['cms_patterns'] = $save;
	return $ret;
}

function hts_data_prehandler($pattern, $data)
{
	foreach ($data as $key => $value)
	{
		if ($value == 'default')
			continue;

		if (function_exists($value))
		{
			hts_data_prehandler_add($pattern, $key, $value);
			continue;
		}

		hts_data_prehandler_add($pattern, $key, create_function('$uri, $m', "return \"".addslashes($value)."\";"));
	}

	if (empty ($data['parent']))
		hts_data_prehandler_add($pattern, 'parent', create_function('$uri, $m', 'return array($m[1]);'));

	if (empty ($data['nav_name']))
		hts_data_prehandler_add($pattern, 'nav_name', create_function('$uri, $m', '$hts = new DataBaseHTS(); return strtolower($hts->get_data($uri, "title"));'));

	if (empty ($data['source']))
		hts_data_prehandler_add($pattern, 'source', create_function('$uri, $m', 'return ec("Виртуальная страница.");'));

	if (empty ($data['modify_time']))
		hts_data_prehandler_add($pattern, 'modify_time', create_function('$uri, $m', 'return time();'));

	if (empty ($data['create_time']))
		hts_data_prehandler_add($pattern, 'create_time', create_function('$uri, $m', 'return time();'));
}

function do_plugin_action_handlers($uri, $match, $path)
{
	$save = $GLOBALS['cms_actions'];
	$GLOBALS['cms_actions'] = array ();

	handlers_load($path);
	// match[3] - это путь относительно базы плагинов.
	// Там формат шаблона (/path/to/plugin/)(plugin_name)(/plugin/sub/path/)
	$ret = do_action_handlers($uri, $match[3], $GLOBALS['cms_actions']);

	$GLOBALS['cms_actions'] = $save;
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
?>