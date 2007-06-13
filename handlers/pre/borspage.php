<?
	function borspage_load()
	{
		global $bors_map;
		if(empty($bors_map))
			$bors_map = array();
		
		foreach(array(
				BORS_INCLUDE,
				BORS_INCLUDE."vhosts/".$_SERVER['HTTP_HOST']."/",
				BORS_INCLUDE_LOCAL,
			) as $dir)
		{
			$map = array();
			if(file_exists($file = "{$dir}handlers/bors_map.php"))
				include($file);
			
			$bors_map = array_merge($bors_map, $map);
		}
	}

	borspage_load();
