<?
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/lcml.php");

    echo module_show_punbb_forum_news();
    
    function module_show_punbb_forum_news()
    {
		$db = new DataBase('punbb', 'punbb', 'punbb756');

		$topics = $db->get_array("SELECT t.*, p.message FROM `topics` t LEFT JOIN `posts` p ON (t.id = p.topic_id) WHERE t.forum_id = 8 GROUP BY t.id ORDER BY t.posted DESC LIMIT 7");
		foreach($topics as $t)
		{
		    $title = $t['subject'];
		    $post = lcml($t['message']);
		    $date = strftime("%Y-%m-%d %H:%M", $t['posted']);

			echo "<div class=\"box\"><h3>$title</h3>\n";
			echo "$post<nobr>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"http://balancer.ru/forums/viewtopic.php?id={$t['id']}\"><small><i>дальше&nbsp;&#187;&#187;&#187;</i></small></a></nobr>\n<br />";
			echo "<div align=\"right\">$date</div></div>\n";
		}
//	print_r($pages);	
//	$GLOBALS['log_level']=2;
    }
?>
