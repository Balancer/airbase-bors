<?
	function module_locale_info_killers()
	{
		return "";
	
		$cache = new Cache();
		
		if($cache->get('LBR:Info', 'killers'))
			return $cache->last;
	
		$hts = new DataBase('l2jdb','la2', 'la2kkk');
		
		$res = "<table class=\"btab\" cellSpacing=\"0\" width=\"100%\">\n";
		$res .= "<tr><th colSpan=\"3\">Лучшие убийцы мобов по рейтингу:</th></tr>\n";
		
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
		
		$res .= "<ul><li>Подробнее смотри <a href=\"/top/killers_full/\">детальные рейтинги убийств</a></li></ul>\n";
		
		return $cache->set($res, 3600);
	}

	echo module_locale_info_killers();
?>
