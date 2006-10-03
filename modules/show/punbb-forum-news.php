<?
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/lcml.php");

//	if(intval(user_data('level')) > 2)
//		echo "<a href=\"http://balancer.ru/forums/post.php?fid=8\">Новое сообщение</a>";

    echo module_show_punbb_forum_news();
    
    function module_show_punbb_forum_news()
    {
		include_once("/var/www/balancer.ru/htdocs/forums/include/pun_bal.php");

		$db = new DataBase('punbb');

		$topics = $db->get_array("SELECT t.*, count(p.id) as posts FROM `topics` t LEFT JOIN `posts` p ON (t.id = p.topic_id) WHERE t.forum_id = 8 AND moved_to IS  NULL GROUP BY t.id ORDER BY t.posted DESC LIMIT 7");
		foreach($topics as $t)
		{
			

		    $title = $t['subject'];
		    $post = pun_lcml($db->get("SELECT message FROM `posts` WHERE topic_id = {$t['id']} GROUP BY id LIMIT 1"));
		    $date = strftime("%Y-%m-%d %H:%M", $t['posted']);
			$t['posts']-- ;

			echo "<div class=\"box\"><h3>$title</h3>\n";
			echo "$post<br /><br />\n";
			echo "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr>";
			echo "<td><small><a href=\"http://balancer.ru/forums/viewtopic.php?id={$t['id']}\"><i>Комментариев: {$t['posts']}</i></a></small></td>\n";
			echo "<td><small><div align=\"right\"><i>{$t['poster']}, $date</i></div></small></td></tr></table></div>\n";
		}
//	print_r($pages);	
//	$GLOBALS['log_level']=2;
    }
?>
