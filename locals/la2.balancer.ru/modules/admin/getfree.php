<?
	module_local_get_npc_free();

	function module_local_get_npc_free()
	{
		$query = "FROM npc n LEFT JOIN spawnlist s ON (n.id = s.npc_templateid) WHERE s.id IS NULL";

		$per_page = 50;

		if(!empty($GLOBALS['module_data']['class']))
			$query .= " AND n.class LIKE '".addslashes($GLOBALS['module_data']['class'])."'";
		$query .= " ORDER BY n.level DESC";

		$db = new driver_mysql('l2jdb','la2','la2kkk');

		$total = $db->get("SELECT COUNT(*) $query");
		include_once('funcs/design/page_split.php');
	
		$page = max(intval($GLOBALS['cms']['page_number']),1);
		$pages = join(" ",pages_select($GLOBALS['main_uri'], $page, ($total-1)/$per_page+1));
		
		echo "<center>$pages</center>";
		
		$start = ($page-1)*$per_page;
		$limit = $per_page;
		echo "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
		echo "<tr><th>NPC</th><th>npc_id</th><th>Класс</th><th>Тип</th><th>Пол</th><th>Уровень</th><th>Здоровье</th></tr>\n";

//		$total = $db->get("SELECT COUNT(*) $query", false);

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
			echo "</tr>\n";
		}
		echo "</table>\n";

		echo "<center>$pages</center>";
	}
