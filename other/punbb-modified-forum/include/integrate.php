<?
	function punbb_get_user_warnings($user_id)
	{
		$db = &new DataBase('punbb');
		$warns = intval($db->get("SELECT SUM(score) FROM warnings WHERE time > ".(time()-86400*WARNING_DAYS)." AND user_id=".intval($user_id)));
		$db->close();
		return $warns;
	}
