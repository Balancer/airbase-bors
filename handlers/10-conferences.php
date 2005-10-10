<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/?$!", 'handler_forum');

    function handler_forum($uri, $m=array())
	{
		require_once('funcs/templates/show.php');
		include_once('funcs/design/page_split.php');
		include_once('funcs/datetime.php');

		include('inc/add_current_user_info.php');

        $hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);
		
		$data = array();
		foreach(split(' ','caching modify_time title source topics') as $key)
			$tpl_vars[] = $key;

		$GLOBALS['title'] = $title = $hts->get_data($uri, 'title');
		$caching = false;
		$modify_time = time();
		$source = '';

		$GLOBALS['log_level'] = 2;

		$start = time() - 86400*31*5;

		$topics = array();
		foreach($hts->dbh->get_array("
			SELECT 	`c`.`value` as `tid` , 
					`t`.`value` as `title` ,
					`m`.`value` as `modify` ,
					`d`.`value` as `description` ,
					`n`.`value` as `news_uri` ,
					`a`.`value` as `author_name`,
					v.value as views
			FROM `hts_data_child` `c` 
				LEFT JOIN `hts_data_modify_time` `m` ON (`c`.`value` = `m`.`id`)
				LEFT JOIN `hts_data_title` `t` ON (`c`.`value` = `t`.`id`)
				LEFT JOIN `hts_data_description` `d` ON (`c`.`value` = `d`.`id`)
				LEFT JOIN `hts_data_child` `n` ON (`c`.`value` = `n`.`id` AND `n`.`value` LIKE 'http://{$GLOBALS['cms']['conferences_host']}/news%')
				LEFT JOIN `hts_data_author_name` `a` ON (`c`.`value` = `a`.`id`)
				LEFT JOIN hts_data_views v ON (c.value = v.id)
			WHERE `c`.`id` LIKE '$uri' 
			 	AND `c`.`value` LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/thread%'
				AND m.value > $start
			ORDER BY `m`.`value` DESC;") as $topic)
		{
			$posts = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '{$topic['tid']}' AND `value` LIKE '%post%';");
			$posts = max(0, $posts-1);
		
			$topics[] = array(
				'uri' 			=> preg_replace("!^(.+/)thread(\d+/)$!", "$1$2", $topic['tid']),
				'title' 		=> strlen($topic['title'])>35 ? substr($topic['title'],0,35)."..." : $topic['title'], 
				'date' 			=> short_time($topic['modify']),
				'description'	=> $topic['description'],
				'answers'		=> $posts,
				'news_uri' 		=> $topic['news_uri'],
				'author_name' 	=> $topic['author_name'],
				'views' 		=> intval($topic['views']),
			);
		}		

		foreach($tpl_vars as $var)
			$data[$var] = $$var;

		$data['conferences_uri'] = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}";

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/conferences-notable/", $data);
#		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/conferences-main/", $data);

		return true;
    }
?>
