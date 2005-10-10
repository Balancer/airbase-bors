<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/guest/?$!", 'handler_guest');

    function handler_guest($uri, $m=array())
	{
		require_once('funcs/templates/show.php');
		include_once('funcs/design/page_split.php');

		$topics_per_page = 50;
	
        $hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);
		
		$data = array();
		$tpl_vars = 'caching current_page modify_time source title topics pages total_topics user_first_name user_last_name';

		$GLOBALS['title'] = $title = $hts->get_data($uri, 'title');
		$caching = false;
		$modify_time = time();
		$source = '';

		$us = new User;
		$user_first_name = $us->data('first_name');
		$user_last_name = $us->data('last_name');

		$current_page = $GLOBALS['cms']['page_number'];
		$start =  ($current_page-1)*$topics_per_page;

		$GLOBALS['log_level'] = 2;

		$topics = array();
		foreach($hts->dbh->get_array("
			SELECT 	`c`.`value` as `tid` , 
					`t`.`value` as `title` ,
					`m`.`value` as `modify` ,
					`d`.`value` as `description` ,
					`a`.`value` as `author_name`
			FROM `hts_data_child` `c` 
				LEFT JOIN `hts_data_modify_time` `m` ON (`c`.`value` = `m`.`id`)
				LEFT JOIN `hts_data_title` `t` ON (`c`.`value` = `t`.`id`)
				LEFT JOIN `hts_data_description` `d` ON (`c`.`value` = `d`.`id`)
				LEFT JOIN `hts_data_author_name` `a` ON (`c`.`value` = `a`.`id`)
			WHERE `c`.`id` LIKE '$uri' 
			 	AND `c`.`value` LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/guest/thread%'
			ORDER BY `m`.`value` DESC
			LIMIT $start, $topics_per_page;") as $topic)
		{
			$posts = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '{$topic['tid']}' AND `value` LIKE '%post%';");
			$posts = max(0, $posts-1);
		
			if(time() - $topic['modify'] < 86400 && strftime("%d",$topic['modify']) == strftime("%d",time()))
				$date = strftime("%H:%M",$topic['modify']);
			else
				$date = strftime("%d.%m.%y",$topic['modify']);

			$topics[] = array(
				'uri' 			=> $topic['tid'], 
				'title' 		=> $topic['title'], 
				'date' 			=> $date,
				'description'	=> $topic['description'],
				'answers'		=> $posts,
				'author_name' 	=> $topic['author_name'],
			);
		}		

		$total_topics = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '$uri' AND `value` LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/guest/topic%';");
		$total_pages = intval(($total_topics-1)/$topics_per_page)+1;

		$pages = pages_select($uri, $current_page, $total_pages);
		
		foreach(split(' ', $tpl_vars) as $var)
		{
			$data[$var] = $$var;
		}

		$data['conferences_uri'] = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/guest";

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/conferences-notable/", $data);

		return true;
    }
?>
