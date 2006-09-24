<?
	require_once('funcs/uris.php');

	register_uri_handler('!.*!', 'plugins_base_load');

	function plugins_base_load($uri)
	{
//		echo "<b>Do plugins for $uri</b><br/>\n";
		$ret = false;

		foreach(array($GLOBALS['cms']['local_dir'],
					"{$GLOBALS['cms']['base_dir']}/vhosts/{$_SERVER['HTTP_HOST']}",
					$GLOBALS['cms']['base_dir']) as $base_path
				)
		{
			$res = plugins_load($uri, "$base_path/plugins");
			if($res === true)
				return true;
			if($res !== false)
				$ret = $uri = $res;
		}
		
		return $ret;
	}

	function plugins_load($uri, $base_dir = 'plugins')
    {
//		echo "<b>Load plugins from $base_dir</b><br/>\n";
		$ret = false;

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
				foreach(file("$base_dir/$dir/main.uri") as $pattern)
				{
					$pattern = trim($pattern);
//					echo "Apply '$pattern' to '$path'<br/>\n";
					if(preg_match("!^$pattern$!", $path, $m))
					{
//						echo "$path::$pattern---<br/>\n";
					    ini_set('include_path', "$base_dir/$dir:".ini_get('include_path'));
						
						$GLOBALS['cms']['plugin_base_path']	= $m[1].$m[2]."/";
						$GLOBALS['cms']['plugin_pattern']= $pattern;
						$GLOBALS['cms']['plugin_parent_uri']= preg_replace("!$pattern!", $m[1], $uri);
						$GLOBALS['cms']['plugin_base_uri']	= $GLOBALS['cms']['plugin_parent_uri'].$m[2]."/";
						$GLOBALS['cms']['plugin_base_pattern_uri']	= "({$GLOBALS['cms']['plugin_parent_uri']})({$m[2]})/";

						$GLOBALS['cms']['templates']['data']['plugin_base_uri'] = $GLOBALS['cms']['plugin_base_uri'];
						$GLOBALS['cms']['templates']['data']['plugin_base_path'] = $GLOBALS['cms']['plugin_base_path'];

//						echo "<br/>$base_dir/$dir/config.php<br />";
						@include_once("$base_dir/$dir/config.php");

						if(!empty($_GET))
						{
							$res = do_plugin_action_handlers($uri, $m, "$base_dir/$dir/handlers/");
		
							if($res === true)
								return true;

							if($res !== false)
								$ret = $uri = $ret;
						}

						$res = do_plugin_uri_handlers($uri, $m, "$base_dir/$dir/handlers/");
						if($res === true)
							return true;
						if($res !== false)
							$ret = $uri = $res;
					}
				}
			}
			else
				plugins_load($uri, "$base_dir/$dir");
        }

		return $ret;
    }
?>
