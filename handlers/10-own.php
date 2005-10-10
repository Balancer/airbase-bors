<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/users/own/?$!", 'handler_own');

    function handler_own($uri, $m=array())
	{
		require_once('funcs/templates/show.php');
		include_once('funcs/design/page_split.php');
		include_once('funcs/datetime.php');
		include('inc/add_current_user_info.php');

		$topics_per_page = 50;
	
        $hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);
		
		$data = array();
		foreach(split(' ','caching current_page modify_time source title topics pages total_topics') as $var)
			$tpl_vars[] = $var;

		$GLOBALS['title'] = $title = $hts->get_data($uri, 'title');
		$caching = false;
		$modify_time = time();
		$source = '';

		$current_page = $GLOBALS['cms']['page_number'];
		$start =  ($current_page-1)*$topics_per_page;

		$GLOBALS['log_level'] = 2;

		$topics = array();
		foreach($hts->dbh->get_array("
			SELECT 	a.value as author,
					a.id as tid, 
					t.value as title,
					m.value as modify,
					d.value as description,
					an.value as author_name,
					v.value as views
			FROM hts_data_author a
				LEFT JOIN hts_data_modify_time m ON (a.id = m.id)
				LEFT JOIN hts_data_title t ON (a.id = t.id)
				LEFT JOIN hts_data_description d ON (a.id = d.id)
				LEFT JOIN hts_data_author_name an ON (a.id = an.id)
				LEFT JOIN hts_data_views v ON (a.id = v.id)
			WHERE a.value = $user_id
				AND a.id IS NOT NULL
				AND a.id LIKE '%/thread%'
			ORDER BY m.value DESC
			LIMIT $start, $topics_per_page;", false) as $topic)
		{
			$posts = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '{$topic['tid']}' AND `value` LIKE '%post%';");
			$posts = max(0, $posts-1);
		
			$topics[] = array(
				'uri' 			=> preg_replace("!^(.+/)thread(\d+/)$!", "$1$2", $topic['tid']),
				'title' 		=> $topic['title'], 
				'date' 			=> short_time($topic['modify']),
				'description'	=> $topic['description'],
				'answers'		=> $posts,
				'author_name' 	=> $topic['author_name'],
				'views' 		=> intval($topic['views']),
			);
		}		

		$total_topics = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '$uri' AND `value` LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/guest/topic%';");
		$total_pages = intval(($total_topics-1)/$topics_per_page)+1;

		$pages = pages_select($uri, $current_page, $total_pages);
		
		foreach($tpl_vars as $var)
		{
			$data[$var] = $$var;
		}

		$data['conferences_uri'] = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}";

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/conferences-notable/", $data);

		return true;
    }
?>
