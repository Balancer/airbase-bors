<?
	module_local_db_items();

	function module_local_db_items()
	{
		$items_per_page = 50;
	
		$query = "FROM `prices` WHERE `name` <> ''";

		if(!empty($_GET['name']))
			$query .= " AND `name` LIKE '%".addslashes($_GET['name'])."%'";

		if(!empty($_GET['crystal']))
			$query .= " AND `crystal_type` LIKE '".addslashes($_GET['crystal'])."'";
			
		$query .= "ORDER BY `name`";

		$db = new driver_mysql('l2jdb','la2','la2kkk');

		$total = $db->get("SELECT COUNT(*) $query");
		include_once('funcs/design/page_split.php');
	
		$page = max(intval($GLOBALS['cms']['page_number']),1);
		$pages = join(" ",pages_select($GLOBALS['main_uri'], $page, ($total-1)/$items_per_page+1));
		
		echo "<center>";
		echo "<p>$pages</p>";

		echo "<p>";
		echo "<form method=\"get\" action=\"{$GLOBALS['main_uri']}\">";
		echo "<input name=\"name\" value=\"".htmlspecialchars(@$_GET['name'])."\"/>&nbsp;";
		echo "<select name=\"crystal\">";
		echo "<option value=\"\"".(@$_GET['crystal']==''?' selected':'').">Все</option>";
		echo "<option value=\"none\"".(@$_GET['crystal']=='none'?' selected':'').">non-grade</option>";
		echo "<option value=\"d\"".(@$_GET['crystal']=='d'?' selected':'').">D-grade</option>";
		echo "<option value=\"c\"".(@$_GET['crystal']=='c'?' selected':'').">C-grade</option>";
		echo "<option value=\"b\"".(@$_GET['crystal']=='b'?' selected':'').">B-grade</option>";
		echo "<option value=\"a\"".(@$_GET['crystal']=='a'?' selected':'').">A-grade</option>";
		echo "<option value=\"s\"".(@$_GET['crystal']=='s'?' selected':'').">S-grade</option>";
		echo "</option>";
		echo "<input type=\"submit\" value=\"Найти\"></form></p>";
		echo "</center>";
		
		$start = ($page-1)*$items_per_page;
		$limit = $items_per_page;
		echo "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
		echo "<tr><th colSpan=\"2\">Название</th><th>Grade</th><th>item_id</th><th>Цена</th><th>Вес</th><th>Дроп: Моб&nbsp;(урв.моба&nbsp;кол-во&nbsp;x&nbsp;вероятность)</th><th>В игре</th></tr>\n";

		$cryst = array(
			's' => 'S',
			'a' => 'A',
			'b' => 'B',
			'c' => 'C',
			'd' => 'D',
			'none' => '&nbsp;',
		);

		foreach($db->get_array("SELECT * $query LIMIT $start, $limit;", false) as $row)
		{
			if(!$row['name'])
				continue;
		
			$item_id = intval($row['item_id']);
		
			echo "<tr valign=\"top\">";
			echo "<td><img src=\"/images/items/{$item_id}.png\" width=\"32\" height=\"32\"/ align=\"top\"></td><td>{$row['name']}</td>";
			echo "<td>".$cryst[$row['crystal_type']]."</td>";
			echo "<td>$item_id</td>";
			echo "<td>".($row['price']?$row['price']:'&nbsp;')."</td>";
			echo "<td>{$row['weight']}</td>";

			$drop = $db->get_array("SELECT d.mobId, d.sweep, n.name, n.id, n.level, d.chance, gchance, d.min, d.max, s.npc_templateid as spawn FROM `droplist` d LEFT JOIN npc n ON (d.mobId = n.id) LEFT JOIN spawnlist s ON (s.npc_templateid = d.mobId) WHERE `itemId` = $item_id GROUP BY s.npc_templateid ORDER BY d.chance*(d.min+d.max) DESC LIMIT 25;");

			if(sizeof($drop)>0)
			{
				echo "<td align=\"left\"><small>";
				$first = true;
				foreach($drop as $d)
				{
					if($d['chance'] == 0 || $d['gchance'] == 0)
						continue;
				
					if(!$first)
						echo "<br/>";
					$first = false;

					if($sf = $d['sweep'])
						echo "<b>";
					echo "<nobr><a href=\"http://la2.balancer.ru/db/mob/?id={$d['id']}\" style=\"color: orange\" title=\"mob_id={$d['id']}\">";
					if($f = !$db->get("SELECT npc_templateid FROM `spawnlist` WHERE `npc_templateid` = {$d['mobId']} LIMIT 1;"))
						echo "<s>";
					echo str_replace(' ','&nbsp;',preg_replace('!^\d+\s*!','',trim($d['name'])))."&nbsp;<small>[{$d['level']}]</small>";
					if($f)	echo "</s>";
					echo "</a></nobr>";
					if($sf) echo "</b>";

					$chance = $d['chance']/10000.0*$d['gchance']/1000000.0;
					if($chance >= 1)
						$chance = intval($chance+0.5)."%";
					elseif($chance > 0)
						$chance = "1/".intval(100/$chance);
					else
						$chance = "0% ({$d['chance']}/{$d['gchance']})";


					echo "<small>&nbsp;(".($d['min']==$d['max'] ? $d['min'] : $d['min'].'-'.$d['max'])."&nbsp;x&nbsp;$chance)</small><nobr>";
				}
				echo "</small></td>";
			}
			else
				echo "<td>&nbsp;</td>";

			$count = $db->get("SELECT sum(i.`count`) FROM `items` i LEFT JOIN characters c ON (c.obj_Id = i.owner_id) WHERE `item_id` = $item_id AND c.obj_Id IS NOT NULL AND c.accesslevel < 30 AND c.accesslevel >= 0;");

			if($count)
				echo "<td>$count</td>";
			else
				echo "<td>&nbsp;</td>";
		
		}
		echo "</table>\n";

		echo "<center>$pages</center>";
	}
?>
