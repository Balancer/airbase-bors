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

	function get_new_id()
	{
		$db = new DataBase();
		return $db->get("BEGIN TRANSACTION")
	}
?>
