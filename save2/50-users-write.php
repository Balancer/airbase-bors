<?

	register_uri_handler("!^(http://[^/]+)/{$GLOBALS['cms']['conferences_path']}/users/write/?$!", 'handler_users_write');

	function handler_users_write($uri, $m=array())
	{
	    require_once('funcs/DataBaseHTS.php');

	    $hts  = new DataBaseHTS;
		$us = new User;

		$author = $us->data('id');

		if(!$author)
		{
	    	require_once('funcs/templates/smarty.php');
			$GLOBALS['page_data']['title'] = "Поучаствованные темы пользователя";
			$GLOBALS['page_data']['source'] = 'Ошибка: вы не зашли в систему.';

			show_page($uri);
			return true;
		}


		require_once('funcs/templates/show.php');
		include_once('funcs/design/page_split.php');

		$topics_per_page = 50;
	
		$data = array();
		$tpl_vars = 'caching current_page modify_time source title topics pages total_topics';

		$title = "Поучаствованные темы пользователя";
		$caching = false;
		$modify_time = time();
		$source = '';

		$current_page = $GLOBALS['cms']['page_number'];
		$start =  ($current_page-1)*$topics_per_page;

		$GLOBALS['log_level'] = 2;

		$topics = array();
		foreach($hts->dbh->get_array("
			SELECT DISTINCT p.value as tid
			FROM hts_data_author a
				LEFT JOIN hts_data_parent p ON (a.id = p.id AND p.value LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/thread%')
				LEFT JOIN hts_data_modify_time m ON (p.id = m.id)
			WHERE a.value = $author 
			 	AND a.id LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/post%'
				AND p.value IS NOT NULL
			ORDER BY m.value DESC
			LIMIT $start, $topics_per_page;", false) as $uri)
		{
			$topic = $hts->dbh->get("
				SELECT 
					t.value as title,
					m.value as modify,
					d.value as description,
					an.value as author_name
				FROM hts_data_title t
					LEFT JOIN hts_data_modify_time m ON (m.id = t.id)
					LEFT JOIN hts_data_description d ON (d.id = t.id)
					LEFT JOIN hts_data_author_name an ON (an.id = t.id)
				WHERE t.id = '".addslashes($uri)."';
			", false);

			$posts = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '$uri' AND `value` LIKE '%post%';");
			$posts = max(0, $posts-1);
		
			if(time() - $topic['modify'] < 86400 && strftime("%d",$topic['modify']) == strftime("%d",time()))
				$date = strftime("%H:%M",$topic['modify']);
			else
				$date = strftime("%d.%m.%y",$topic['modify']);

			$topics[] = array(
				'uri' 			=> $uri, 
				'title' 		=> $topic['title'], 
				'date' 			=> $date,
				'description'	=> $topic['description'],
				'answers'		=> $posts,
//				'news_uri' 		=> $topic['news_uri'],
				'author_name' 	=> $topic['author_name'],
				'views' 		=> intval($topic['views']),
			);
		}		

		$total_topics = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '$uri' AND `value` LIKE 'http://forums.airbase.ru/topic%';");
		$total_pages = intval(($total_topics-1)/$topics_per_page)+1;

		$pages = pages_select($uri, $current_page, $total_pages);
		
		foreach(split(' ', $tpl_vars) as $var)
		{
			$data[$var] = $$var;
		}

		$data['conferences_uri'] = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}";

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/conferences-notable/", $data);

		return true;
	}
?>
