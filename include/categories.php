<?
	function get_categories($host, $cat)
	{
		$hts = new DataBaseHTS('WWW');
		$db = new DataBase('WWW');
		$cats = array();
//		$GLOBALS['log_level'] = 10;
		foreach($db->get_array("
				SELECT c.value 
				FROM hts_data_child c 
				LEFT JOIN hts_data_order o ON c.value = o.id 
				LEFT JOIN hts_data_title t ON c.value = t.id 
				WHERE c.id = '".addslashes("category://$host/$cat/")."'
				ORDER BY o.value, t.value
			") as $c)
//			$GLOBALS['log_level'] = 2;
			$cats[] = array(
				'category'	=> $c,
				'name'		=> $hts->get_data($c, 'title'),
				'default'	=> $hts->is_flag($c, 'default'),
				'value'		=> preg_replace('!^.+?([^/]+)/$!', '$1', $c),
			);
			
		return $cats;
	}

	function get_category_name($host, $cat, $value)
	{
		$hts = new DataBaseHTS('WWW');
		
//		echo "Get $value for $cat<br/>";
		
//		$GLOBALS['log_level'] = 10;
		$ret = $hts->get_data("category://$host/$cat/$value/", 'title');
//		$GLOBALS['log_level'] = 2;

		if(!$ret)
			$ret = $value;

		return $ret;
	}
