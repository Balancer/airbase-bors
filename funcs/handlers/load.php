<?
	require_once("funcs/uris.php");
	require_once("funcs/handlers/register.php");

	function handlers_load()
	{
		$_SERVER['HTTP_HOST'] = str_replace(':80', '', $_SERVER['HTTP_HOST']);
    	$_SERVER['REQUEST_URI'] = preg_replace("!^(.+?)\?.*?$!", "$1", $_SERVER['REQUEST_URI']);

		$uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		$parse = parse_url($uri);

		$GLOBALS['cms']['page_number'] = 1;

		if(empty($GLOBALS['main_uri']))
			$GLOBALS['main_uri'] = $uri;

		$GLOBALS['cms']['page_path'] = $GLOBALS['main_uri'];

		$GLOBALS['ref'] = @$_SERVER['HTTP_REFERER'];

		$GLOBALS['cms_patterns'] = array();
		$GLOBALS['cms_actions']  = array();

		foreach(array('/pre', '', '/post') as $stage)
		{
			foreach(array($GLOBALS['cms']['local_dir'],
					"{$GLOBALS['cms']['base_dir']}/vhosts/{$_SERVER['HTTP_HOST']}",
					$GLOBALS['cms']['base_dir']) as $base_path)
			{
				plugins_load($uri, "$base_path/plugins$stage");
				
				handlers_load_dir("$base_path/handlers$stage");
			}
		}
	}	

	function handlers_load_dir($dir)
	{
		if(!empty ($_GET['debug']))
			echo "<b>Load handlers from $dir</b><br/>";

		if(!is_dir($dir))
			return;

		$files = array ();

		if ($dh = opendir($dir))
			while (($file = readdir($dh)) !== false)
				if ($file{0} != '.')
					array_push($files, $file);

		closedir($dh);

		sort($files);

		foreach ($files as $file)
		{
			if (!empty ($_GET['debug']))
				echo "load $file<br>\n";

			if (substr($file, -4) == '.php')
				include_once("$dir/$file");
			elseif (is_dir("$dir/$file") && !preg_match("!(post|pre)$!", $file)) 
				handlers_load_dir("$dir/$file");
		}
	}
/*
	function do_plugin_uri_handlers($uri, $match, $path)
	{
		if(!empty($_GET['debug']))
			echo "*** do_plugin_uri_handlers: $path ***<br />\n";

		$GLOBALS['cms_actions'] = array ();
		$GLOBALS['cms_patterns'] = array ();
	
		handlers_load_dir($path);
		// match[3] - это путь относительно базы плагинов.
		// Там формат шаблона (/path/to/plugin/)(plugin_name)(/plugin/sub/path/)
	//	if (!empty ($_GET['debug']))
	//	{	echo __LINE__.":<xmp>"; print_r($GLOBALS['cms_patterns']); echo "</xmp>";}
		return do_uri_handlers($uri, $match[3], $GLOBALS['cms_patterns']);
	}
*/

	function plugins_load($uri, $base_dir)
    {
//		echo "<b>Load plugins from $base_dir</b><br/>\n";

		$path = uri2path($uri);

//		if(!empty($_GET['debug']))
//			DebugBreak();
	
        if(!is_dir($base_dir)) 
        	return false;
        
        $dirs = array();

        if($dh = opendir($base_dir)) 
            while(($dir = readdir($dh)) !== false)
                if(is_dir("$base_dir/$dir") && $dir{0} != '.')
                   	array_push($dirs, $dir);
  
        closedir($dh);
        sort($dirs);
	
        foreach($dirs as $dir) 
        {
//			echo "Dir: $base_dir/$dir/main.uri<br/>";
            if(file_exists("$base_dir/$dir/main.uri"))
			{
				include_once("funcs/templates/assign.php");
				
				foreach(file("$base_dir/$dir/main.uri") as $pattern)
				{
					$pattern = trim($pattern);
//					echo "Apply '$pattern' to '$path'<br/>\n";
					if(preg_match("!^$pattern$!", $path, $m))
					{
//						echo "$path::$pattern---<br/>\n";
					    ini_set('include_path', "$base_dir/$dir:".ini_get('include_path'));

						$data = array();
						
						$data['base_path']	= $m[1].$m[2]."/";
						$data['pattern']	= $pattern;
						
						$data['parent_uri']	= preg_replace("!$pattern!", $m[1], $uri);
						$data['base_uri']	= $data['parent_uri'].$m[2]."/";
						$data['base_pattern_uri']	= "({$data['parent_uri']})({$m[2]})/";

						$GLOBALS['cms']['templates']['data']['plugin']['base_uri'] = $data['base_uri'];
						$GLOBALS['cms']['templates']['data']['plugin']['base_path'] = $data['base_path'];

//						echo "<br/>$base_dir/$dir/config.php<br />";

						$errrep_save = error_reporting();
						error_reporting($errrep_save & ~ (E_NOTICE | E_WARNING));
						include_once("$base_dir/$dir/config.php");
						error_reporting($errrep_save);

						$GLOBALS['cms']['plugin_data'] = $data;
						handlers_load_dir("$base_dir/$dir/handlers/");
						$GLOBALS['cms']['plugin_data'] = false;
					}
				}
			}
			else
				plugins_load($uri, "$base_dir/$dir");
        }
    }
