<?
	hts_data_posthandler("()([^:]+):\*/", array(
			'source' => 'airbase_plugins_all_category_source',
			'title' => 'airbase_plugins_all_category_title',
			'nav_name' => 'airbase_plugins_all_category_nav_name',
		));

	function airbase_plugins_all_category_source($uri, $match)
	{
		if(preg_match("!template$!", $match[2]))
			return false;


		$name  = urldecode($match[2]);

		$data = array();

		$db = new driver_mysql('AIRBASE');
		
		$data['list'] = $db->get_array("SELECT DISTINCT value
			FROM OBJECTS 
			WHERE parameter = '".addslashes($name)."'
			ORDER BY value");
		
		$data['category'] = $name;
		
        include_once("engines/smarty/assign.php");
        return template_assign_data("category_all.html", $data);
	}

	function airbase_plugins_all_category_title($uri, $match)
	{
		if(preg_match("!template$!", $match[2]))
			return false;

		return ec("Выборка по категории '").urldecode($match[2])."'";
	}

	function airbase_plugins_all_category_nav_name($uri, $match)
	{
		if(preg_match("!template$!", $match[2]))
			return false;

		return strtolower(urldecode($match[2]));
	}
