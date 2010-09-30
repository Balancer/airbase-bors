<?php

class balancer_board_stat_main extends bors_page
{
	function title() { return ec('Статистика форумов'); }
	function nav_name() { return ec('статистика'); }
	function template() { return 'xfile:forum/_header.html'; }

	function body_data()
	{
		$dbh = new driver_mysql('punbb');
/*		$forums_by_topics = $dbh->select_array('topics', 'forum_id, COUNT(*) as updated', array(
			'last_post>' => time() - 30*86400,
			'group' => 'forum_id',
			'order' => '-updated',
		));
*/
		$topics_by_posts = $dbh->select_array('posts', 'topic_id, COUNT(*) as updated', array(
			'posted>' => time() - 30*86400,
			'group' => 'topic_id',
			'order' => '-updated',
//			'having' => 'updated > 1',
//			'limit' => 200,
		));

//		$forum_ids = array();
//		foreach($forums_by_topics as $x)
//			$forum_ids = $x['forum_id'];

		$topics_ids = array();
		foreach($topics_by_posts as $x)
		{
			$topics_ids[] = $x['topic_id'];
			$topics_count[$x['topic_id']] = $x['updated'];
		}

		$forums_stat = array();
		$topics = objects_array('balancer_board_topic', array('id IN' => $topics_ids, 'by_id' => true));
		foreach($topics as $x)
			@$forums_stat[$x->forum_id()] += $topics_count[$x->id()] ;

		arsort($forums_stat);
		$forums_stat = array_slice($forums_stat, 0, 50, true);

		$forums = objects_array('balancer_board_forum', array('id IN' => array_keys($forums_stat), 'by_id' => true));

//		foreach($forums_by_topics as $x)
//			if(!empty($forums[$x['forum_id']]))
//				$forums[$x['forum_id']]->set_attr('updated', $x['updated']);

		return array(
			'forums' => $forums,
			'forums_stat' => $forums_stat,
		);
	}
}
