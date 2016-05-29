<?
	hts_data_posthandler("()([^:/\*]+?)/", array(
			'source' => 'airbase_plugins_objects_source',
			'title' => 'airbase_plugins_objects_title',
			'nav_name' => 'airbase_plugins_objects_title',
		));

	function airbase_plugins_objects_source($uri, $match)
	{
		if(preg_match("!template$!", $match[2]))
			return false;

		$object = urldecode($match[2]);

		$data = array();

		$data['object'] = $object;
		
        include_once("engines/smarty/assign.php");
        return template_assign_data("objects.html", $data);
	}

/*	function airbase_plugins_objects_parent($uri, $match)
	{
		$object = urldecode($match[2]);

		$data = array();

		$db = new driver_mysql('AIRBASE');
		
		$parents = array();
		
		foreach($db->get_array("SELECT DISTINCT parameter, WHERE") as)

	}
*/
	function airbase_plugins_objects_title($uri, $match)
	{
		if(preg_match("!template$!", $match[2]))
			return false;

		return urldecode($match[2]);
	}
