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
		if(empty($GLOBALS['cms_actions'][$action_type]))
			$GLOBALS['cms_actions'][$action_type] = $func;
	}

	function register_alias($uri_regexp, $function)
	{
		$GLOBALS['cms_aliases'][$uri_regexp] = $function;
	}

    function handlers_load($dir='handlers')
    {
//		echo "<b>Load handlers from $dir</b><br/>";
	
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
            elseif(is_dir("$dir/$file") && !preg_match("!(post|pre)$!", $file))
                handlers_load("$dir/$file");
        }
    }

	function hts_data_prehandler_add($regexp, $data_key, $func)
	{
		if(!empty($_GET['debug']))
			echo "<small>Add function ".print_r(&$func,true)." to uri like '$regexp for key $data_key</small><br>/";
		$GLOBALS['cms']['data_prehandler'][$data_key][$regexp] = $func;
	}

	function hts_data_posthandler_add($regexp, $data_key, $function)
	{
		$GLOBALS['cms']['data_posthandler'][$data_key][$regexp] = $function;
	}
?>
