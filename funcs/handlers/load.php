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
		if(preg_match('!^\.!', $dir) || $dir == 'CVS')
			return;
		
//		if(!empty ($_GET['debug']))
//			echo "<b>Load handlers from $dir</b><br/>";

		if(!is_dir($dir))
			return;

		$files = array();

		if($dh = opendir($dir))
			while(($file = readdir($dh)) !== false)
				if(!preg_match('!^\.!', $file))
					$files[] = $file;

		closedir($dh);

		sort($files);

		foreach($files as $file)
		{
//			if (!empty ($_GET['debug']))
//				echo "load $dir/$file<br>\n";

			if (preg_match("!\.php$!", $file))
				include_once("$dir/$file");
			elseif (is_dir("$dir/$file") && !preg_match("!(post|pre)$!", $file)) 
				handlers_load_dir("$dir/$file");
		}
	}

	function plugins_load($uri, $base_dir)
    {
		if(!empty($_GET['handler_trace']))
			echo "<b>Load plugins from $base_dir</b><br/>\n";

		$path = uri2path($uri);

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
						
						
						if($path != '/')
						{
							$data['base_path']	= $m[1].$m[2]."/";
							$data['pattern']	= $pattern;
						$data['matches'] = $m;

							$data['parent_uri']	= preg_replace("!$pattern!", $m[1], $uri);
							$data['base_uri']	= $data['parent_uri'].$m[2]."/";
							$data['base_pattern_uri']	= "({$data['parent_uri']})({$m[2]})/";
						}
						else
						{
							$data['base_path']	= '/';
							$data['pattern']	= $pattern;
							
							$data['parent_uri']	= $uri;
							$data['base_uri']	= $uri;
							$data['base_pattern_uri']	= $uri;
						}

						$GLOBALS['cms']['templates']['data']['plugin']['base_uri'] = $data['base_uri'];
						$GLOBALS['cms']['templates']['data']['plugin']['base_path'] = $data['base_path'];

//						echo "<br/>$base_dir/$dir/config.php<br />";

						$errrep_save = error_reporting();
						error_reporting($errrep_save & ~ (E_NOTICE | E_WARNING));
						include_once("$base_dir/$dir/config.php");
						error_reporting($errrep_save);

						$GLOBALS['cms']['plugin_data'] = $data;
//						echo ("$base_dir/$dir/handlers/");
						handlers_load_dir("$base_dir/$dir/handlers/");
						$GLOBALS['cms']['plugin_data'] = false;
					}
				}
			}
			else
				plugins_load($uri, "$base_dir/$dir");
        }
    }
