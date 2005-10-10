<?
	function register_handler($uri_pattern, $func)
	{
		$GLOBALS['cms_patterns'][$uri_pattern] = $func;
//		print_r($GLOBALS['cms_patterns']);
	}

	function register_uri_handler($uri_pattern, $func)
	{
		$GLOBALS['cms_patterns'][$uri_pattern] = $func;
	}

	function register_action_handler($action_type, $func)
	{
		$GLOBALS['cms_actions'][$action_type] = $func;
	}

    function handlers_load($dir='handlers')
    {
        if(!is_dir($dir)) 
        	return;
        
        $files = array();

        if($dh = opendir($dir)) 
        {
            while(($file = readdir($dh)) !== false)
                if(substr($file,0,1)!='.')
                    array_push($files, $file);
        }
        closedir($dh);
        
        sort($files);

        foreach($files as $file) 
        {
//            echo "load $file<br>\n";

            if(substr($file,-4)=='.php')
                include_once("$dir/$file");
            else
                handlers_load("$dir/$file");
        }
    }
?>
