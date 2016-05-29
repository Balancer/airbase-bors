<?
    register_handler('!^(http://[^/]+.*/)mob/?$!', 'handler_local_mob_title');

    function handler_local_mob_title($uri, $m=array())
	{
		$mob_id = intval(@$_GET['id']);

		if(!$mob_id)
			return false;
		
		$db = new driver_mysql('l2jdb', 'la2', 'la2kkk');
		$GLOBALS['page_data']['title'] = "Параметры NPC ".$db->get("SELECT `name` FROM `npc` WHERE `id`=$mob_id", false);
		$GLOBALS['cms']['cache_disabled'] = true;

		return false;
    }
?>
