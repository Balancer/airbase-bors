<?
	hts_data_prehandler("topic_titles/", array(
			'body'		=> 'balancer_plugins_search_topic_titles_body',
			'title'		=> 'balancer_plugins_search_topic_titles_title',
			'nav_name'	=> 'balancer_plugins_search_topic_titles_nav_name',
			'template'	=> "{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html",
		));

    function balancer_plugins_search_topic_titles_body($uri, $m)
	{
//		return ec("Временно закрыт");
	
		include_once("engines/search.php");
		$GLOBALS['cms']['cache_disabled'] = true;
		
		$result = array();
		
		if($query = norm(@$_GET['q']))
		{
			$order="";
			if(empty($_GET['order']))
				$_GET['order'] = "ud";

			switch($_GET['order'])
			{
				case 'ud': $order = " ORDER BY t.last_post DESC"; break;
				case 'ca': $order = " ORDER BY t.posted "; break;
				case 'cd': $order = " ORDER BY t.posted DESC"; break;
				case 'rd': $order = " ORDER BY t.num_replies DESC"; break;
				case 'vd': $order = " ORDER BY t.num_views DESC"; break;
			}
		
			$db = &new DataBase('punbb');
//			$GLOBALS['log_level'] = 10;
			if(@$_GET['type'] == 'm')
				$objects = bors_search_in_bodies($query);
			else
				$objects = bors_search_in_titles($query);
//			$GLOBALS['log_level'] = 2;

/*			$result = array();
			foreach($objects as $obj)
				if(empty($result[$tid=$obj->topic()->id()]))
					$result[$tid] = $obj;*/
		}
		
        include_once("engines/smarty/assign.php");
		return template_assign_data("topics.html", array('objects'=>@$objects, 'q'=>$query, 'type' => @$_GET['type']));
	}

    function balancer_plugins_search_topic_titles_title($uri, $m)
	{
		return empty($_GET['q']) ? ec("Поиск в заголовках форумов") : norm($_GET['q']);
	}

    function balancer_plugins_search_topic_titles_nav_name($uri, $m)
	{
		return "в заголовках форума: ".norm($_GET['q']);
	}

	function norm($query)
	{
		if(preg_match('!%!', $query))
			$query = urldecode($query);

		return $query;
	}
