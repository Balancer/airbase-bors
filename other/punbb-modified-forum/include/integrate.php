<?
	function punbb_get_user_warnings($user_id)
	{
		$db = &new DataBase('punbb');
		$warns = intval($db->get("SELECT SUM(score) FROM warnings WHERE time > ".(time()-86400*30)." AND user_id=".intval($user_id)));
		$db->close();
		retuen $warns;
	}
