<?
	module_local_db_mobs();

	function module_local_db_mobs()
	{
		$items_per_page = 50;
	
		$query = "FROM npc n";
		$where = "";
		$join = "";
		foreach(split(" ", "name class type sex") as $key)
			if(!empty($_GET[$key]))
				$where .= " AND n.$key LIKE '%".addslashes($_GET[$key])."%'";

		if(!empty($_GET['level_min']))
			$where .= " AND n.level >= ".intval($_GET['level_min']);

		if(!empty($_GET['level_max']))
			$where .= " AND n.level <= ".intval($_GET['level_max']);

		if(@$_GET['exists'])
		{
			echo " ===== ";
			$join .= " LEFT JOIN spawnlist s ON (n.id = s.npc_templateid)";
			if(@$_GET['exists']=='yes')
				$where .= " AND s.npc_templateid IS NOT NULL AND s.respawn_delay > 0 GROUP BY n.id";
			else
				$where .= " AND s.npc_templateid IS NULL";
		}
			
		$query = "$query $join WHERE n.name != '' AND n.id != 0 $where ORDER BY n.name";

		$db = new DataBase('l2jdb','la2','la2kkk');

		$total = $db->get("SELECT COUNT(*) $query");
		include_once('funcs/design/page_split.php');
	
		$page = max(intval($GLOBALS['cms']['page_number']),1);
		$pages = join(" ",pages_select($GLOBALS['uri'], $page, ($total-1)/$items_per_page+1));
		
		echo "<center>";
		echo "<p>$pages</p>";

		echo "<form method=\"get\" action=\"{$GLOBALS['main_uri']}\">\n";
		echo "<table class=\"btab\" cellSpacing=\"0\">\n";
		echo "<tr><th>Название:</th><td><input name=\"name\" value=\"".htmlspecialchars(@$_GET['name'])."\"></td></tr>\n";
		echo "<tr><th>Класс:</th><td><input name=\"class\" value=\"".htmlspecialchars(@$_GET['class'])."\"></td></tr>\n";
		echo "<tr><th>Тип:</th><td><input name=\"type\" value=\"".htmlspecialchars(@$_GET['type'])."\"></td></tr>\n";
		echo "<tr><th>Пол:</th><td><input name=\"sex\" value=\"".htmlspecialchars(@$_GET['sex'])."\"></td></tr>\n";
		echo "<tr><th>Уровень:</th><td><input name=\"level_min\" value=\"".htmlspecialchars(@$_GET['level_min'])."\">&nbsp;-&nbsp;<input name=\"level_max\" value=\"".htmlspecialchars(@$_GET['level_max'])."\"></td></tr>\n";
		echo "<tr><th>В игре:</th><td><select name=\"exists\"><option value=\"\">&nbsp;</option><option value=\"yes\">Есть</option><option value=\"no\">Нет</option></select></td></tr>\n";
		echo "<tr><th>&nbsp;</th><td><input type=\"submit\" value=\"Найти\"></td></tr>";
		echo "</table></form>";


		echo "</center>";
		
		$start = ($page-1)*$items_per_page;
		$limit = $items_per_page;
		echo "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
		echo "<tr><th rowSpan=\"2\">Название</th><th rowSpan=\"2\">id</th><th rowSpan=\"2\">Урв</th><th rowSpan=\"2\">Кол-во</th><th colSpan=\"2\">Респавн</th><th rowSpan=\"2\">Дроп</th></tr>\n";
		echo "<tr><th>Период</th><th>Ближайший</th></tr>\n";

		foreach($db->get_array("SELECT DISTINCT n.id, n.level, n.name $query LIMIT $start, $limit;", false) as $row)
		{
			if(!$row['name'])
				continue;
		
			$mob_id = intval($row['id']);
		
			echo "<tr valign=\"top\">";
			echo "<th><nobr><a href=\"/db/mob/?id=$mob_id\">{$row['name']}</a></nobr></th>";
			echo "<td>$mob_id</td>";
			echo "<td>{$row['level']}</td>";

			$count = $db->get("SELECT sum(`count`) FROM `spawnlist` WHERE npc_templateid = $mob_id;");

			if($count>0)
				echo "<td>$count</td>";
			else
				echo "<td>&nbsp;</td>";
			
			if($count>0)
			{
				$r = $db->get("SELECT respawn_delay, respawn_time FROM `spawnlist` WHERE npc_templateid = $mob_id ORDER BY respawn_time DESC LIMIT 1;");

				echo "<td align=\"left\">".approx_time($r['respawn_delay'])."&nbsp;</small></td>";
				echo "<td align=\"left\">". ( @$r['respawn_time'] > time() ? approx_time($r['respawn_time']-time()) : "&nbsp;") . "</small></td>";
			}
			else
				echo "<td colSpan=\"2\">&nbsp;</td>";

			$drop = $db->get_array("SELECT d.itemId as item_id, d.sweep, d.chance, d.min, d.max, p.name FROM `droplist` d LEFT JOIN prices p ON (d.itemId = p.item_id) WHERE `mobId` = $mob_id ORDER BY d.chance*(d.min+d.max)*p.price DESC;");

			if(sizeof($drop)>0)
			{
				echo "<td align=\"left\"><small>";
				$first = true;
				foreach($drop as $d)
				{
					if(!$first)
						echo "<br/>";
					$first = false;

					if($d['min']!=$d['max'] || $d['min'] > 1)
						echo ($d['min']==$d['max'] ? $d['min'] : $d['min'].'-'.$d['max'])."&nbsp;x&nbsp;";
					if($sf = $d['sweep'])
						echo "<b>";
					echo "<a href=\"http://la2.balancer.ru/db/items/?name={$d['name']}\" style=\"color: orange\" title=\"item_id={$d['item_id']}\">";
					echo str_replace(' ','&nbsp;',preg_replace('!^\d+\s*!','',trim($d['name'])));
					echo "</a>";
					if($sf) echo "</b>";

					echo "<small>,&nbsp;".($d['chance']/10000)."%</small>";
				}
				echo "</small></td>";
			}
			else
				echo "<td>&nbsp;</td>";


		}
		echo "</table>\n";

		echo "<center>$pages</center>";
	}

	function approx_time($seconds)
	{
		$days  = intval($seconds/86400);
		$hours = intval(($seconds%86400)/3600);
		$min   = intval(($seconds%3600)/60);
		$sec   = intval($seconds%60);

		$sec   = $hours ? ($sec   ? "{$sec}сек."   : "") : "";
		$min   = $days ? ($min   ? "{$min}мин."   : "") : "";
		$hours = $days < 7 ? ($hours ? "{$hours}час." : "") : "";
		$days  = $days  ? "{$days}д.&nbsp;" : "";

		return "$days$hours$min$sec";
	}
?>
