<?
	hts_data_prehandler("topic_titles/", array(
			'body'		=> 'balancer_plugins_search_topic_titles_body',
			'title'		=> 'balancer_plugins_search_topic_titles_title',
			'nav_name'	=> 'balancer_plugins_search_topic_titles_nav_name',
			'template'	=> "{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html",
		));

    function balancer_plugins_search_topic_titles_body($uri, $m)
	{
		include_once("funcs/search/index.php");
		$GLOBALS['cms']['cache_disabled'] = true;
		
		$topics = false;
		if($query = @$_GET['q'])
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
			$topics = find_in_topics($query);
		
			if($topics)
				$topics = $db->get_array("
					SELECT t.*, f.forum_name
					FROM topics t
						LEFT JOIN forums f ON (t.forum_id = f.id)
					WHERE t.id IN (".join(",", $topics).") 
					$order");
		}
		
        include_once("funcs/templates/assign.php");
		return template_assign_data("topics.html", array('topics'=>$topics, 'q'=>$query));
	}

    function balancer_plugins_search_topic_titles_title($uri, $m)
	{
		return empty($_GET['q']) ? ec("Поиск в заголовках форумов") : $_GET['q'];
	}

    function balancer_plugins_search_topic_titles_nav_name($uri, $m)
	{
		return "в заголовках форума: ".$_GET['q'];
	}
