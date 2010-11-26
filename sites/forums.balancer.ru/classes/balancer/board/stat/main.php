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

		$topics_by_posts_prev = $dbh->select_array('posts', 'topic_id, COUNT(*) as updated', array(
			'posted>' => time() - 60*86400,
			'posted<=' => time() - 30*86400,
			'group' => 'topic_id',
			'order' => '-updated',
		));

		$topics_by_posts_prev_year = $dbh->select_array('posts', 'topic_id, COUNT(*) as updated', array(
			'posted>' => time() - 365*86400 - 30*86400,
			'posted<=' => time() - 365*86400,
			'group' => 'topic_id',
			'order' => '-updated',
		));

		$topics_by_posts_prev_10year = $dbh->select_array('posts', 'topic_id, COUNT(*) as updated', array(
			'posted>' => time() - 10*365*86400 - 30*86400,
			'posted<=' => time() - 10*365*86400,
			'group' => 'topic_id',
			'order' => '-updated',
		));

		$topics_ids = array();
		foreach($topics_by_posts as $x)
		{
			$topics_ids[] = $x['topic_id'];
			$topics_count[$x['topic_id']] = $x['updated'];
		}

		$topics_ids_prev = array();
		foreach($topics_by_posts_prev as $x)
		{
			$topics_ids_prev[] = $x['topic_id'];
			$topics_count_prev[$x['topic_id']] = $x['updated'];
		}

		$topics_ids_prev_year = array();
		foreach($topics_by_posts_prev_year as $x)
		{
			$topics_ids_prev_year[] = $x['topic_id'];
			$topics_count_prev_year[$x['topic_id']] = $x['updated'];
		}

		$topics_ids_prev_10year = array();
		foreach($topics_by_posts_prev_10year as $x)
		{
			$topics_ids_prev_10year[] = $x['topic_id'];
			$topics_count_prev_10year[$x['topic_id']] = $x['updated'];
		}

		$forums_stat = array();
		$topics = objects_array('balancer_board_topic', array('id IN' => $topics_ids, 'by_id' => true));
		$topics_prev = objects_array('balancer_board_topic', array('id IN' => $topics_ids_prev, 'by_id' => true));
		$topics_prev_year = objects_array('balancer_board_topic', array('id IN' => $topics_ids_prev_year, 'by_id' => true));
		$topics_prev_10year = objects_array('balancer_board_topic', array('id IN' => $topics_ids_prev_10year, 'by_id' => true));
		foreach($topics as $x)
			@$forums_stat[$x->forum_id()]['now'] += $topics_count[$x->id()] ;

		foreach($topics_prev as $x)
			@$forums_stat[$x->forum_id()]['prev'] += $topics_count_prev[$x->id()] ;

		foreach($topics_prev_year as $x)
			@$forums_stat[$x->forum_id()]['prev_year'] += $topics_count_prev_year[$x->id()] ;

		foreach($topics_prev_10year as $x)
			@$forums_stat[$x->forum_id()]['prev_10year'] += $topics_count_prev_10year[$x->id()] ;

		uasort($forums_stat, create_function('$x, $y', 'return @$y["now"] - @$x["now"];'));
		$forums_stat = array_slice($forums_stat, 0, 50, true);

		$forums = objects_array('balancer_board_forum', array('id IN' => array_keys($forums_stat), 'by_id' => true));

		return array(
			'forums' => $forums,
			'forums_stat' => $forums_stat,
		);
	}
}
