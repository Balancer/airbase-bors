<?
	hts_data_posthandler("()([^/]+):([^/\*]+)/", array(
			'source' => 'airbase_plugins_category_source',
			'title' => 'airbase_plugins_category_title',
		));

	function airbase_plugins_category_source($uri, $match)
	{
		if(preg_match("!template$!", $match[3]))
			return false;

		$type  = urldecode(urldecode($match[2]));
		$value = urldecode(urldecode($match[3]));

		$data = array();

		$db = new DataBase('AIRBASE');
		
		$data['list'] = $db->get_array("
			SELECT DISTINCT 
				o.object, o2.parameter
			FROM OBJECTS o
				LEFT JOIN OBJECTS o2 ON (o.object = o2.value)
			WHERE o.parameter = '".addslashes($type)."'
				AND o.value = '".addslashes($value)."'
			ORDER BY o.object");
		
        include_once("funcs/templates/assign.php");
        return template_assign_data("category.html", $data);
	}

	function airbase_plugins_category_title($uri, $match)
	{
		if(preg_match("!template$!", $match[3]))
			return false;

		return urldecode(urldecode($match[3]));
	}
