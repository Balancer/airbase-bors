<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/(\w+/)?thread(\d+)/?$!", 'handler_thread');

    function handler_thread($uri, $m=array())
	{
		require_once('funcs/templates/show.php');
		include_once('funcs/design/page_split.php');
		include_once('funcs/datetime.php');

		$posts_per_page = 1500;
		foreach(split(' ','caching modify_time news_uri source posts this_page_uri title user_first_name user_last_name') as $var)
			$tpl_vars[] = $var;
	
        $hts = new DataBaseHTS;
		$uri = $this_page_uri = $hts->normalize_uri($uri);
		$GLOBALS['title'] = $title = $hts->get_data($uri, 'title');

		$hts->viewses_inc($uri);

		$caching = false;
		$modify_time = time();
		$source = '';

		$us = new User;
		$user_first_name = $us->data('first_name');
		$user_last_name = $us->data('last_name');

		include('inc/add_current_user_info.php');
		include('inc/access_vars.php');

		$posts = array();

		$news_uri = $hts->get_data($uri, 'child', NULL, false, false, 'value', "value LIKE 'http://{$GLOBALS['cms']['conferences_host']}/news%' AND id");

		foreach($hts->dbh->get_array("
			SELECT 	`c`.`value` as `pid`, 
					`m`.`value` as `modify`,
					`s`.`value` as `source`,
					`an`.`value` as `author_name`,
					t.value as title
			FROM `hts_data_child` `c` 
				LEFT JOIN `hts_data_modify_time` `m` ON (`c`.`value` = `m`.`id`)
				LEFT JOIN `hts_data_source` `s` ON (`c`.`value` = `s`.`id`)
				LEFT JOIN `hts_data_author_name` `an` ON (`c`.`value` = `an`.`id`)
				LEFT JOIN `hts_data_title` `t` ON (`c`.`value` = `t`.`id`)
			WHERE `c`.`id` LIKE '$uri' 
			 	AND `c`.`value` LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/post%'
			ORDER BY `m`.`value`
			LIMIT $posts_per_page;") as $post)
		{
//			echo "<xmp>Parse: '".print_r($post['source'],true)."'</xmp>";
		
			$post_data = array(
				'uri' => $post['pid'], // URI постинга! Не всей темы.
				'body' => lcml($post['source'], array('cr_type'=>'save_cr')),
				'date' => full_time($post['modify']),
				'author_name' => $post['author_name'],
				'title' => $post['title'],
			);
			
			$posts[] = $post_data;
		}		


		foreach($tpl_vars as $var)
		{
//			echo "$var = ".print_r($$var, true)."<br />";
			$data[$var] = $$var;
		}

		$data['conferences_uri'] = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}";
		include_once("funcs/actions/favorites.php");
		$data['is_favorite'] = cms_funcs_action_is_favorite($uri, $us);

		$GLOBALS['page_data_preset']['nav_name'][$uri] = $title;
		$GLOBALS['page_data_preset']['parent'][$uri] = array("http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/");

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/forum-topic/", $data);

		$hts->update_data($uri, 'subscribe', array('visited'=>time()), "id='".addslashes($us->get_page())."' AND value");

		return true;
    }
?>
