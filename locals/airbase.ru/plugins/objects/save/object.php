<?
	hts_data_posthandler("()([^:/\*]+?)/", array(
			'source' => 'airbase_plugins_objects_source',
			'title' => 'airbase_plugins_objects_title',
		));

	function airbase_plugins_objects_source($uri, $match)
	{
		if(preg_match("!template$!", $match[2]))
			return false;

		$object = urldecode(urldecode($match[2]));

		$data = array();

		$data['object'] = $object;
		
        include_once("funcs/templates/assign.php");
        return template_assign_data("objects.html", $data);
	}

	function airbase_plugins_objects_title($uri, $match)
	{
		if(preg_match("!template$!", $match[2]))
			return false;

		return urldecode(urldecode($match[2]));
	}
