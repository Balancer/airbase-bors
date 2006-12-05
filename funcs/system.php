<?
	function ungpc($array)
	{
		foreach($array as $key => $val)
		{
			if(is_array($val))
				$_POST[$key] = ungpc($val);
			else
				$_POST[$key] = stripslashes($val);
		}
		return $array;
	}

	function get_new_global_id($db = NULL)
	{
		$db = &new DataBase($db);
		$db->query("UPDATE hts_ext_system_data SET `value`=`value`+1 WHERE `key`='global_id';", false);
		return $db->get("SELECT `value` FROM hts_ext_system_data WHERE `key`='global_id';", false);
	}

	function new_id($engine)
	{
		$db = &new DataBase('HTS');

//		$db->insert('global_ids', array('engine' => $engine));
		$db->query("INSERT INTO `global_ids` SET `engine` = '".addslashes($engine)."'");
		$new_id = intval($db->last_id());
		
		if(!$new_id)
			exit("Ошибка получения global id");
			
		return $new_id;
	}
