<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/favorites/?$!", 'handler_favorites');

    function handler_favorites($uri, $m=array())
	{
		require_once('funcs/templates/show.php');
		include_once('funcs/design/page_split.php');

		$max_favorites = 50;
	
        $hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);
		
		$us = new User;
		$user_first_name = $us->data('first_name');
		$user_last_name = $us->data('last_name');
		$user_id = $us->data('id');
		
		$data = array();
		$tpl_vars = 'caching modify_time source title topics total_topics user_first_name user_last_name';

		$GLOBALS['title'] = $title = $hts->get_data($uri, 'title');
		$caching = false;
		$modify_time = time();
		$source = '';

		$GLOBALS['log_level'] = 2;

		$topics = array();
		foreach($hts->dbh->get_array("
			SELECT 	c.value as tid, 
					t.value as title,
					m.value as modify,
					d.value as description,
					a.value as author_name,
					v.value as views
			FROM hts_data_child c 
				LEFT JOIN hts_data_modify_time `m` ON (`c`.`value` = `m`.`id`)
				LEFT JOIN hts_data_title `t` ON (`c`.`value` = `t`.`id`)
				LEFT JOIN hts_data_description `d` ON (`c`.`value` = `d`.`id`)
				LEFT JOIN hts_data_author_name `a` ON (`c`.`value` = `a`.`id`)
				LEFT JOIN hts_data_views v ON (c.value = v.id)
			WHERE `c`.`id` LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/favorites/user%' 
			 	AND `c`.`value` LIKE '%/thread%'
			ORDER BY `m`.`value` DESC
			LIMIT $max_favorites;", false) as $topic)
		{
			$posts = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '{$topic['tid']}' AND `value` LIKE '%post%';");
			$posts = max(0, $posts-1);
		
			if(time() - $topic['modify'] < 86400 && strftime("%d",$topic['modify']) == strftime("%d",time()))
				$date = strftime("%H:%M",$topic['modify']);
			else
				$date = strftime("%d.%m.%y",$topic['modify']);

			$topics[] = array(
				'uri' 			=> preg_replace("!^(.+/)thread(\d+/)$!", "$1$2", $topic['tid']),
				'title' 		=> $topic['title'], 
				'date' 			=> $date,
				'description'	=> $topic['description'],
				'answers'		=> $posts,
				'views' 		=> intval($topic['views']),
				'author_name' 	=> $topic['author_name'],
			);
		}		

		$total_topics = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '$uri' AND `value` LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/guest/topic%';");

		foreach(split(' ', $tpl_vars) as $var)
			$data[$var] = $$var;

		$data['conferences_uri'] = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}";

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/conferences-notable/", $data);

		return true;
    }
?>
