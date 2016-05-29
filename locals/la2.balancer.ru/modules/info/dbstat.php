<?php
	function module_locale_info_dbstat()
	{
		$cache = new bors_cache();
		
		if($cache->get('LBR:Info', 'dbstat'))
			return $cache->last;
	
		$hts = new driver_mysql('l2jdb','la2', 'la2kkk');
		
		$res = "<table class=\"btab\" cellSpacing=\"0\" width=\"100%\">\n";
		$res .= "<tr><th colSpan=\"4\">На нашем сервере (всего / видов):</th></tr>\n";
		
		$res .= "<tr>";
		$res .= "<td>Монстров:</td><td>".
			$hts->get("SELECT SUM(`count`) FROM spawnlist LEFT JOIN npc ON npc_templateid = id WHERE type IN ('L2Monster', 'L2RaidBoss', 'L2Minion')")." / ".
			$hts->get("SELECT COUNT(*) FROM npc WHERE type IN ('L2Monster', 'L2RaidBoss', 'L2Minion')").
			"</td>";
		$res .= "<td>NPC:</td><td>".$hts->get("SELECT SUM(`count`) FROM spawnlist LEFT JOIN npc ON npc_templateid = id WHERE type NOT IN ('L2Monster', 'L2RaidBoss','L2Minion')")." / ".
			$hts->get("SELECT COUNT(*) FROM npc WHERE type NOT IN ('L2Monster', 'L2RaidBoss', 'L2Minion')")."</td>";
		$res .= "</tr>\n";

		$res .= "<tr>";
		$res .= "<td><b>Рейдбоссов</b>:</td><td>".
			$hts->get("SELECT SUM(`count`) FROM spawnlist LEFT JOIN npc ON npc_templateid = id WHERE type='L2RaidBoss'")." / ".
			$hts->get("SELECT COUNT(*) FROM npc WHERE type='L2RaidBoss'").
			"</td>";
		$res .= "<td>Миньонов:</td><td>".$hts->get("SELECT SUM(`count`) FROM spawnlist LEFT JOIN npc ON npc_templateid = id WHERE type='L2Minion'")."</td>";
		$res .= "</tr>\n";

		$days = intval((time()-1142590622)/86400);
		require_once('funcs/strings.php');
		$res .= "<tr><th colSpan=\"4\">За $days ".sklon($days, 'день', 'дня', 'дней')." было убито:</th></tr>\n";
		$res .= "<tr>";
		$res .= "<td>Монстров всего:</td><td>".$hts->get("SELECT SUM(`count`) FROM killcount LEFT JOIN npc ON npc_id = id WHERE type IN ('L2Monster', 'L2RaidBoss', 'L2Minion')")."</td>";
		$res .= "<td>NPC:</td><td>".$hts->get("SELECT SUM(`count`) FROM killcount LEFT JOIN npc ON npc_id = id WHERE type NOT IN ('L2Monster', 'L2RaidBoss', 'L2Minion')")."</td>";
		$res .= "</tr>\n";

		$res .= "<tr>";
		$res .= "<td><b>Рейдбоссов</b>:</td><td>".intval($hts->get("SELECT SUM(`count`) FROM killcount LEFT JOIN npc ON npc_id = id WHERE type = 'L2RaidBoss'"))."</td>";
		$res .= "<td><b><a href=\"http://la2.balancer.ru/db/mob/?id=12211\">Antharas</a></b>:</td><td><b><div style=\"color: #FF0000;\">".intval($hts->get("SELECT SUM(`count`) FROM killcount WHERE npc_id = 12211"))."</div></b></td>";
		$res .= "</tr>\n";

		$res .= "</table>\n";
		
		return $cache->set($res, 3600);
	}

	echo module_locale_info_dbstat();
?>

