<?
    show_conference_topic();

    function show_conference_topic()
    {
	   	$topic = intval($GLOBALS['module_data']['topic']);
	   	$forum = intval($GLOBALS['module_data']['forum']);

        $db = new DataBase();

		$db_names = array('1'=>'forum_news', '3'=>'forum_aviafirms', '4'=>'forum_sales');
		$db_name = $db_names[$forum];
    
//		$GLOBALS['log_level'] = 10;
		foreach($db->get_array("SELECT f.*, b.body FROM `{$db_name}` f LEFT JOIN `{$db_name}_bodies` b ON (f.thread = b.thread) WHERE f.thread = $topic GROUP BY f.id ORDER BY f.datestamp") as $r)
		{
			echo "<b>{$r['subject']}</b><br />";
			echo "<i><small><b>{$r['author']}</b>, {$r['datestamp']}</i></small><br /><br />\n";
			echo "{$r['body']}<hr />\n";
		}
    }

?>
