<?
	hts_data_posthandler("db/", array(
			'source' => 'airbase_plugins_hangar_db_main_source',
			'title' => ec('База данных'),
		));

	function airbase_plugins_hangar_db_main_source($uri, $match)
	{
		$data = array();

		$db = new DataBase('AIRBASE');
		
		$data['list'] = $db->get_array("SELECT name FROM obj_param WHERE selectable ORDER BY name");

        include_once("engines/smarty/assign.php");
        return template_assign_data("main.html",$data);
	}
