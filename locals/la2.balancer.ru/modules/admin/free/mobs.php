<?
	module_local_admin_free_mobs();

	function module_local_admin_free_mobs()
	{
		$per_page = 50;

		$query = "FROM npc n LEFT JOIN spawnlist s ON (n.id = s.npc_templateid) WHERE s.npc_templateid IS NULL";

		foreach(split(" ", "name class type sex") as $key)
			if(!empty($_GET[$key]))
				$query .= " AND n.$key LIKE '%".addslashes($_GET[$key])."%'";

		if(!empty($_GET['level_min']))
			$query .= " AND n.level >= ".intval($_GET['level_min']);

		if(!empty($_GET['level_max']))
			$query .= " AND n.level <= ".intval($_GET['level_max']);

		if(!empty($_GET['hp_min']))
			$query .= " AND n.hp >= ".intval($_GET['hp_min']);

		if(!empty($_GET['hp_max']))
			$query .= " AND n.hp <= ".intval($_GET['hp_max']);

		if(!empty($_GET['aggro']))
			$query .= " AND n.aggro ".($_GET['aggro']=='yes' ? ' > 0 ' : ' == 0 ');

		$query .= " ORDER BY n.name";

		$db = new DataBase('l2jdb','la2','la2kkk');

		$total = $db->get("SELECT COUNT(*) $query");
		include_once('funcs/design/page_split.php');
	
		$page = max(intval($GLOBALS['cms']['page_number']),1);
		$pages = join(" ",pages_select($GLOBALS['uri'], $page, ($total-1)/$per_page+1));
		
		echo "<center><p>$pages</p>\n";

		echo "<form method=\"get\" action=\"{$GLOBALS['main_uri']}\">\n";
		echo "<table class=\"btab\" cellSpacing=\"0\">\n";
		echo "<tr><th>Название:</th><td><input name=\"name\" value=\"".htmlspecialchars(@$_GET['name'])."\"></td></tr>\n";
		echo "<tr><th>Класс:</th><td><input name=\"class\" value=\"".htmlspecialchars(@$_GET['class'])."\"></td></tr>\n";
		echo "<tr><th>Тип:</th><td><input name=\"type\" value=\"".htmlspecialchars(@$_GET['type'])."\"></td></tr>\n";
		echo "<tr><th>Пол:</th><td><input name=\"sex\" value=\"".htmlspecialchars(@$_GET['sex'])."\"></td></tr>\n";
		echo "<tr><th>Уровень:</th><td><input name=\"level_min\" value=\"".htmlspecialchars(@$_GET['level_min'])."\">&nbsp;-&nbsp;<input name=\"level_max\" value=\"".htmlspecialchars(@$_GET['level_max'])."\"></td></tr>\n";
		echo "<tr><th>Здоровье:</th><td><input name=\"hp_min\" value=\"".htmlspecialchars(@$_GET['hp_min'])."\">&nbsp;-&nbsp;<input name=\"hp_max\" value=\"".htmlspecialchars(@$_GET['hp_max'])."\"></td></tr>\n";
		echo "<tr><th>Агрессивность:</th><td><select name=\"aggro\"><option value=\"\">любые</option><option value=\"yes\">агрессивные</option><option value=\"no\">неагрессивные</option></select>";
		echo "<tr><th>&nbsp;</th><td><input type=\"submit\" value=\"Найти\"></td></tr>";
		echo "</table></form>";
		echo "</center>";
		
		$start = ($page-1)*$per_page;
		$limit = $per_page;
		echo "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
		echo "<tr><th>NPC</th><th>npc_id</th><th>Класс</th><th>Тип</th><th>Пол</th><th>Уровень</th><th>Здоровье</th><th>Агр.</th></tr>\n";

		foreach($db->get_array("SELECT n.* $query LIMIT $start, $limit;", false) as $row)
		{
			echo "<tr valign=\"top\">";
			echo "<th><nobr>{$row['name']}</nobr></td>";
			echo "<td>{$row['id']}</td>";
			echo "<td>{$row['class']}</td>";
			echo "<td>{$row['type']}</td>";
			echo "<td>{$row['sex']}</td>";
			echo "<td>{$row['level']}</td>";
			echo "<td>{$row['hp']}</td>";
			echo "<td>{$row['aggro']}</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";

		echo "<center>$pages</center>";
	}
?>
