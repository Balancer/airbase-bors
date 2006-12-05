<?
	module_local_admin_free_items();

	function module_local_admin_free_items()
	{
		$query = "FROM character_quests q LEFT JOIN characters c ON (q.char_id = c.obj_Id) WHERE q.char_id IS NOT NULL AND q.name like 'user-var'";

		$per_page = 50;
		if(!empty($_GET['var']))
			$query .= " AND q.var LIKE '%".addslashes($_GET['var'])."%'";

		if(!empty($_GET['char_name']))
			$query .= " AND c.char_name LIKE '%".addslashes($_GET['char_name'])."%'";

		$query .= " ORDER BY q.var, q.value";

		$db = new DataBase('l2jdb','la2','la2kkk');

		$total = $db->get("SELECT COUNT(*) $query");
		include_once('funcs/design/page_split.php');
	
		$page = max(intval($GLOBALS['cms']['page_number']),1);
		$pages = join(" ",pages_select($GLOBALS['uri'], $page, ($total-1)/$per_page+1));
		
		echo "<center><p>$pages</p>";

		echo "<p><form method=\"get\" action=\"{$GLOBALS['main_uri']}\">";
		echo "Char:&nbsp;<input name=\"char_name\" value=\"".htmlspecialchars(@$_GET['char_name'])."\">&nbsp;";
		echo "Var:&nbsp;<input name=\"var\" value=\"".htmlspecialchars(@$_GET['var'])."\">&nbsp;";
		echo "<input type=\"submit\" value=\"Найти\"></form></p>";
		echo "</center>";
		
		$start = ($page-1)*$per_page;
		$limit = $per_page;
		echo "<table cellSpacing=\"0\" class=\"btab\" width=\"100%\">\n";
		echo "<tr><th>Пользователь</th><th>char_id</th><th>var</th><th>value</th></tr>\n";

		$ch = new Cache;

		foreach($db->get_array("SELECT c.char_name, c.obj_Id as char_id, q.var, q.value  $query LIMIT $start, $limit;", false) as $row)
		{
			echo "<tr valign=\"top\">";
			echo "<td>{$row['char_name']}</td>";
			echo "<td>{$row['char_id']}</td>";
			echo "<td>{$row['var']}</td>";
			echo "<td>{$row['value']}</td>";
			echo "</tr>";
		}
		echo "</table>\n";

		echo "<center>$pages</center>";
	}
?>
