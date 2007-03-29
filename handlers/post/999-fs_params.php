<?
	hts_data_posthandler_add("!.*!", '*', 'params_from_fs');
	function params_from_fs($uri, $m, $plugin_data, $key)
	{	
		$is_array = false;
		$file = preg_replace("!^http://".preg_quote($_SERVER['HTTP_HOST'])."!", $_SERVER['DOCUMENT_ROOT'], $uri).".$key.txt";
		if(!file_exists($file))
		{
			$file = preg_replace("!^http://".preg_quote($_SERVER['HTTP_HOST'])."!", $_SERVER['DOCUMENT_ROOT'], $uri).".[$key].txt";
			$is_array = true;
		}
		
		if(!file_exists($file))
			return NULL;
		
		return $is_array ? file($file) : ec(file_get_contents($file));
	}
