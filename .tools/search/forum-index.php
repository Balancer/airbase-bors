<?php
//	setlocale(LC_ALL, 'ru_RU.KOI8-R');

	$_SERVER['DOCUMENT_ROOT'] = "/var/www/balancer.ru/htdocs";
	$_SERVER['HTTP_HOST'] = "balancer.ru";

	require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');
	require_once('obsolete/DataBase.php');
	require_once('classes/inc/bors.php');
	
	config_set('mysql_persistent', true);
	config_set('mysql_disable_autoselect_db', false);

//	$GLOBALS['cms']['mysql_server'] = 'www.avias.loca';

	include_once('engines/search.php');

	$db = new DataBase(config('search_db'));
//	$pundb = new DataBase('punbb');
//	$min = $db->get('SELECT MIN(class_id) FROM bors_search_titles')-1;

	// 1193541:
//	for($i = $max; $i>0; $i--)
//	set_loglevel(9);
	while(true)
	{
		$pid = $db->get('SELECT MAX(class_id) FROM bors_search_source_9 WHERE class_name=1');
		
		if(!$pid)
			break;

		echo "pid=$pid\n";
		
		$post = bors_load('forum_post', $pid);

		if(!$post)
		{
			for($sub=0; $sub<10; $sub++)
				$db->query("DELETE FROM bors_search_source_{$sub}
							WHERE class_name = 1
								AND class_id = {$pid}");
			continue;
		}
		
		echo "tid=".$post->topic_id()."\n";

		$topic = bors_load('balancer_board_topic', $post->topic_id());
		echo $topic->id()."\n";

		$GLOBALS['bors']->_main_obj=$topic;
		for($p=1; $p<= $topic->total_pages(); $p++)
		{
			$topic->set_page($p);
			bors_search_object_index($topic, 'ignore', $db);
			echo dc("{$topic->id()}: {$topic->title()} [{$p}], (".sizeof($GLOBALS['bors_search_get_word_id_cache']).")\n");
			usleep(300);
//			echo dc("{$obj->search_source()}\n\n\n");
		}
		if(sizeof($GLOBALS['bors_search_get_word_id_cache']) > 25000)
			unset($GLOBALS['bors_search_get_word_id_cache']);

		for($sub=0; $sub<10; $sub++)
			$db->query("DELETE FROM bors_search_source_{$sub}
						WHERE class_name = 1
							AND class_id IN (".join(",", $topic->get_all_posts_id()).")");
			
//		echo "DELETE FROM bors_search_source_0 		                        WHERE class_name = 1 								                            AND class_id IN (".join(",", $topic->get_all_posts_id()).")";
	}
