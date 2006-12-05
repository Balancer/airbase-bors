<?
	module_local_admin_free_items();

	function module_local_admin_free_items()
	{
		$per_page = 50;

		$query = "FROM prices p 
				LEFT JOIN droplist d ON (p.item_id = d.itemId) 
				LEFT JOIN items i ON (i.item_id = p.item_id) 
			WHERE d.itemId IS NULL 
				AND item_type <> 'quest'
				AND i.item_id IS NULL 
				AND p.name <> ''";


		if(!empty($_GET['name']))
			$query .= " AND p.name RLIKE '".addslashes($_GET['name'])."'";

		if(!empty($_GET['crystal']))
			$query .= " AND p.crystal_type RLIKE '".addslashes($_GET['crystal'])."'";

		if(!empty($_GET['item_type']))
			$query .= " AND p.item_type RLIKE '".addslashes($_GET['item_type'])."'";

		if(!empty($_GET['consume_type']))
			$query .= " AND p.consume_type RLIKE '".addslashes($_GET['consume_type'])."'";
			
		$query .= " ORDER BY p.name";

		$db = new DataBase('l2jdb','la2','la2kkk');

		$total = $db->get("SELECT COUNT(*) $query");
		include_once('funcs/design/page_split.php');
	
		$page = max(intval($GLOBALS['cms']['page_number']),1);
		$pages = join(" ",pages_select($GLOBALS['uri'], $page, ($total-1)/$per_page+1));
		
		echo "<center>";
		echo "<p>$pages</p>";

		echo "<p>";
		echo "<form method=\"get\" action=\"{$GLOBALS['main_uri']}\">";
		echo "<input name=\"name\" value=\"".htmlspecialchars(@$_GET['name'])."\"/>&nbsp;";
		echo "Тип:&nbsp;<input name=\"item_type\" value=\"".htmlspecialchars(@$_GET['item_type'])."\" size=\"10\"/>&nbsp;";
		echo "Стек:&nbsp;<input name=\"consume_type\" value=\"".htmlspecialchars(@$_GET['consume_type'])."\" size=\"10\"/>&nbsp;";
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
		
		$start = ($page-1)*$per_page;
		$limit = $per_page;
		echo "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
		echo "<tr><th>Название</th><th>item_id</th><th>Цена</th><th>Вес</th><th>Тип</th><th>Стек</th></tr>\n";

//		$total = $db->get("SELECT COUNT(*) $query", false);

		$ch = new Cache;

		foreach($db->get_array("SELECT p.* $query LIMIT $start, $limit;", false) as $row)
		{
			echo "<tr valign=\"top\">";
			echo "<td><nobr><img src=\"/images/items/{$row['item_id']}.png\" width=\"32\" height=\"32\"/ align=\"absmiddle\"/>&nbsp;{$row['name']}</nobr></td>";
			echo "<td>{$row['item_id']}</td>";
			echo "<td>{$row['price']}</td>";
			echo "<td>{$row['weight']}</td>";
			echo "<td>{$row['item_type']}</td>";
			echo "<td>{$row['consume_type']}</td>";
			echo "</tr>";
		}
		echo "</table>\n";

		echo "<center>$pages</center>";
	}
?>
