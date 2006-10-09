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
		$db = new DataBase($db);
		$db->query("UPDATE hts_ext_system_data SET `value`=`value`+1 WHERE `key`='global_id';", false);
		return $db->get("SELECT `value` FROM hts_ext_system_data WHERE `key`='global_id';", false);
	}
?>
