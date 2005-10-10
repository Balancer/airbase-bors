<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/archives/(\d{4})/(\d{2})/?$!", 'handler_archives_month');
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/guest/archives/(\d{4})/(\d{2})/?$!", 'handler_archives_month');

    function handler_archives_month($uri, $m=array())
	{
		require_once('funcs/templates/show.php');
		include_once('funcs/design/page_split.php');

        $hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);

		$data = array();
		$tpl_vars = 'caching modify_time source title topics user_first_name user_last_name';

		$year  = $m[2];
		$month = $m[3];


		include_once("funcs/datetime.php");
		$GLOBALS['title'] = $title = "Архив конференции: ".month_name($month)." ".$year." года";
		$caching = false;
		$modify_time = time();
		$source = '';

		$GLOBALS['page_data_preset']['nav_name']["http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/archives/"] = "архив";
		$GLOBALS['page_data_preset']['parent']["http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/archives/"] = array("http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/");

		$GLOBALS['page_data_preset']['nav_name']["http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/archives/$year/"] = "$year";
		$GLOBALS['page_data_preset']['parent']["http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/archives/$year/"] = array("http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/archives/");

		$GLOBALS['page_data_preset']['nav_name'][$uri] = strtolower(month_name($month));
		$GLOBALS['page_data_preset']['parent'][$uri] = array("http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/archives/$year/");

		$us = new User;
		$user_first_name = $us->data('first_name');
		$user_last_name = $us->data('last_name');

		$GLOBALS['log_level'] = 2;

		$topics = array();

		$time0 = intval(strtotime("$year-$month-1 00:00:00"));
		$mm = $month; $yy = $year;
		if($mm<12)	$mm = $mm+1;
		else {	$mm=1;	$yy++;}
		$time9 = intval(strtotime("$yy-$mm-1 00:00:00"))-1;

		$path = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}";
		
		if(preg_match("!/guest/!", $uri))
			$path = "$path/guest";

		foreach($hts->dbh->get_array("
			SELECT 	
				`c`.`id` as `tid` , 
				`t`.`value` as `title` ,
				`m`.`value` as `modify` ,
				`d`.`value` as `description` ,
				`n`.`value` as `news_uri` ,
				`a`.`value` as `author_name`,
				v.value as views
			FROM `hts_data_create_time` `c` 
				LEFT JOIN `hts_data_create_time` `m` ON (`c`.`id` = `m`.`id`)
				LEFT JOIN `hts_data_title` `t` ON (`c`.`id` = `t`.`id`)
				LEFT JOIN `hts_data_description` `d` ON (`c`.`id` = `d`.`id`)
				LEFT JOIN `hts_data_child` `n` ON (`c`.`id` = `n`.`id` AND `n`.`value` LIKE 'http://{$GLOBALS['cms']['conferences_host']}/news%')
				LEFT JOIN `hts_data_author_name` `a` ON (`c`.`id` = `a`.`id`)
				LEFT JOIN hts_data_views v ON (c.id = v.id)
			WHERE 
				c.value>=$time0 && c.value<=$time9 AND c.id LIKE '$path/thread%'
			ORDER BY `m`.`value`") as $topic)
		{
			$posts = $hts->dbh->get("SELECT COUNT(*) FROM `hts_data_child` WHERE `id` LIKE '{$topic['tid']}' AND `value` LIKE '%post%';");
			$posts = max(0, $posts-1);
		
			$date = short_time($topic['modify']);

			$topics[] = array(
				'uri' 			=> preg_replace("!^(.+/)thread(\d+/)$!", "$1$2", $topic['tid']),
				'title' 		=> $topic['title'], 
				'date' 			=> $date,
				'description'	=> $topic['description'],
				'answers'		=> $posts,
				'news_uri' 		=> $topic['news_uri'],
				'author_name' 	=> $topic['author_name'],
				'views' 		=> intval($topic['views']),
			);
		}		

		foreach(split(' ', $tpl_vars) as $var)
		{
			$data[$var] = $$var;
		}

		$data['conferences_uri'] = "$path";

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/conferences-notable/", $data);

		return true;
    }
?>
