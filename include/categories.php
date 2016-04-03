<?php
	function get_categories($host, $cat)
	{
		$hts = new DataBaseHTS('WWW');
		$db  = new driver_mysql('WWW');
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
		
		$db->close();
		return $cats;
	}

	function get_category_name($host, $cat, $value)
	{
		$hts = new DataBaseHTS('WWW');
		
//		echo "Get $host.==$cat==://==$value==<br/>";
		
//		$GLOBALS['log_level'] = 10;
		$ret = $hts->get_data("category://$host/$cat/$value/", 'title');
//		$GLOBALS['log_level'] = 2;

		if(!$ret)
			$ret = $value;

//		echo $ret."<br />";

		return $ret;
	}

	function get_countries()
	{
		$hts = new DataBaseHTS('WWW');
		$countries = array();
		foreach($hts->get_data_array("category://{$_SERVER['HTTP_HOST']}/personal/country/", 'child') as $country)
			$countries[] = array(
				'category'=>$country,
				'title'=>$hts->get_data($country, 'title'),
			);
			
		return $countries;
	}

	function get_cities()
	{
		$hts = new DataBaseHTS('WWW');
		$cities = array();
		foreach($hts->get_data_array("category://{$_SERVER['HTTP_HOST']}/personal/city/", 'child') as $city)
			$cities[] = array(
				'category'	=> $city,
				'title'		=> $hts->get_data($city, 'title'),
			);
			
		return $cities;
	}
