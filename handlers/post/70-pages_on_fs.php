<?
    require_once('funcs/DataBaseHTS.php');

	if(empty($GLOBALS['cms']['only_load']))
		register_handler('!^http://([^/]+)(.*)!', 'handler_pages_on_fs');

	function handler_pages_on_fs($uri, $m=array())
	{
		$hts = &new DataBaseHTS();
	
		$parse = $hts->parse_uri($uri);

		if(!file_exists($parse['local_path']."source.txt"))
			return false;
		
		foreach(split(' ', 'source title nav_name template cr_type') as $key)
		{
			$file = $parse['local_path'].$key.".txt";
			if(file_exists($file))
				hts_data_prehandler_add("!".preg_quote($uri)."!", $key, create_function('$uri, $m', 
					0 ? "return ec(file_get_contents('$file'));"
					: "return ec(handler_pages_on_fs_read_encoded('$file'));"
					));
		}

		foreach(split(' ', 'parent child') as $key)
		{
			$file = $parse['local_path'].$key.".txt";
			if(file_exists($file))
				hts_data_prehandler_add("!".preg_quote($uri)."!", $key, create_function('$uri, $m', "return file('$file');"));
		}

		return false;
	}

	function handler_pages_on_fs_read_encoded($file)
	{
		$data = file_get_contents($file);

		$data = str_replace(array('«'), array('&quote;'), $data);

		return $data;
	}
