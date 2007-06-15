<?
	function module_locale_info_killers_full()
	{
		$cache = new Cache();
		
		if($cache->get('LBR:Info', 'killers_full'))
			return $cache->last;
	
		$hts = new DataBase('l2jdb','la2', 'la2kkk');
		
		$res = "<table class=\"btab\" cellSpacing=\"0\" width=\"100%\">\n";
/*		$res .= "<tr><th colSpan=\"3\">Лучшие убийцы мобов по рейтингу:</th></tr>\n";
		
		$res .= "<tr><th>Персонаж</th><th>Рейтинг</th><th>Клан</th></tr>\n";

		foreach($hts->get_array("SELECT char_name, sum(count*danger) as rating, clan_name FROM characters LEFT JOIN killcount ON char_id = obj_Id LEFT JOIN npc ON npc_id = id LEFT JOIN clan_data ON clan_id = clanId GROUP BY char_id ORDER BY rating DESC LIMIT 10;") as $row)
		{
			$res .= "<tr>";
			$res .= "<td><b>{$row['char_name']}</b></td>";
			$res .= "<td>".intval($row['rating']/10000000)."</td>";
			$res .= "<td><a href=\"/clans/?clan_id={$row['clan_name']}\">{$row['clan_name']}</a></td>";
			$res .= "</tr>\n";
		}

		$res .= "</table>\n";
		$res .= "<table class=\"btab\" cellSpacing=\"0\" width=\"100%\">\n";
*/		$res .= "<tr><th colSpan=\"3\">Лучшие убийцы мобов по числу мобов:</th></tr>\n";
		$res .= "<tr><th>Персонаж</th><th>Число убитых</th><th>Клан</th></tr>\n";

		foreach($hts->get_array("SELECT char_name, sum(count) as rating, clan_name FROM characters LEFT JOIN killcount ON char_id = obj_Id LEFT JOIN npc ON npc_id = id LEFT JOIN clan_data ON clan_id = clanId GROUP BY char_id ORDER BY rating DESC LIMIT 10;") as $row)
		{
			$res .= "<tr>";
			$res .= "<td>{$row['char_name']}</td>";
			$res .= "<td>".intval($row['rating'])."</td>";
			$res .= "<td><a href=\"/clans/?clan_id={$row['clan_name']}\">{$row['clan_name']}</a></td>";
			$res .= "</tr>\n";
		}

/*		$res .= "</table>\n";
		$res .= "<table class=\"btab\" cellSpacing=\"0\" width=\"100%\">\n";
		$res .= "<tr><th colSpan=\"3\">Самые часто убиваемые опасные мобы:</th></tr>\n";
		$res .= "<tr><th>Моб</th><th>Уровень</th><th>Число убийств</th></tr>\n";

		foreach($hts->get_array(" SELECT id, name, level, sum(count*danger) as rating, count FROM killcount LEFT JOIN npc ON npc_id = id GROUP BY npc_id ORDER BY rating DESC LIMIT 10;") as $row)
		{
			$res .= "<tr>";
			$res .= "<td><a href=\"/db/mob/?id={$row['id']}\">{$row['name']}</a></td>";
			$res .= "<td>{$row['level']}</td>";
			$res .= "<td>{$row['count']}</td>";
			$res .= "</tr>\n";
		}
*/
		$res .= "</table>\n";
		$res .= "<table class=\"btab\" cellSpacing=\"0\" width=\"100%\">\n";
		$res .= "<tr><th colSpan=\"3\">Самые часто убиваемые мобы:</th></tr>\n";
		$res .= "<tr><th>Моб</th><th>Уровень</th><th>Число убийств</th></tr>\n";

		foreach($hts->get_array(" SELECT id, name, level, count FROM killcount LEFT JOIN npc ON npc_id = id GROUP BY npc_id ORDER BY count DESC LIMIT 10;") as $row)
		{
			$res .= "<tr>";
			$res .= "<td><a href=\"/db/mob/?id={$row['id']}\">{$row['name']}</a></td>";
			$res .= "<td>{$row['level']}</td>";
			$res .= "<td>{$row['count']}</td>";
			$res .= "</tr>\n";
		}
		
		$res .= "</table>\n";
		
		return $cache->set($res, 3600);
	}

	echo module_locale_info_killers_full();
?>
