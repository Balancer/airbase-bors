<?
	module_local_admin_user_items();

	function module_local_admin_user_items()
	{
		$query = "FROM items i LEFT JOIN characters c ON (i.owner_id = c.obj_Id) LEFT JOIN prices p ON (i.item_id = p.item_id) LEFT JOIN `clan_data` `cl` ON (cl.clan_id = c.clanid) WHERE c.obj_Id IS NOT NULL ";

		$per_page = 50;

		if(!empty($_GET['item_id']))
			$query .= " AND i.item_id = '".intval($_GET['item_id'])."'";

		if(!empty($_GET['char_name']))
			$query .= " AND c.char_name LIKE '%".addslashes($_GET['char_name'])."%'";

		$query .= " ORDER BY c.char_name";

		$db = new DataBase('l2jdb','la2','la2kkk');

		$total = $db->get("SELECT COUNT(*) $query");
		include_once('funcs/design/page_split.php');
	
		$page = max(intval($GLOBALS['cms']['page_number']),1);
		$pages = join(" ",pages_select($GLOBALS['uri'], $page, ($total-1)/$per_page+1));
		
		echo "<center><p>$pages</p>";

		echo "<p><form method=\"get\" action=\"{$GLOBALS['main_uri']}\">";
		echo "Char:&nbsp;<input name=\"char_name\" value=\"".htmlspecialchars(@$_GET['char_name'])."\">&nbsp;";
		echo "Item:&nbsp;<input name=\"item_id\" value=\"".htmlspecialchars(@$_GET['item_id'])."\">&nbsp;";
		echo "<input type=\"submit\" value=\"Найти\"></form></p>";
		echo "</center>";
		
		$start = ($page-1)*$per_page;
		$limit = $per_page;
		echo "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
		echo "<tr><th>Пользователь</th><th>char_id</th><th>Клан</th><th>Предмет</th><th>Количество</th></tr>\n";

		$ch = new Cache;

		foreach($db->get_array("SELECT c.char_name, c.obj_Id as char_id, i.item_id, p.name as item_name, i.count, cl.clan_name  $query LIMIT $start, $limit;", false) as $row)
		{
			echo "<tr valign=\"top\">";
			echo "<td>{$row['char_name']}</td>";
			echo "<td>{$row['clan_name']}</td>";
			echo "<td>{$row['char_id']}</td>";
			echo "<td>{$row['item_id']}</td>";
			echo "<td>{$row['count']}</td>";
			echo "</tr>";
		}
		echo "</table>\n";

		echo "<center>$pages</center>";
	}
?>
