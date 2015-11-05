<?php

class balancer_board_stat_forums_posted extends balancer_board_page
{
	var $title = 'Количество сообщений по форумам';
	var $nav_name = 'активность по форумам';
	var $auto_map = true;
	function template() { return 'xfile:forum/_header.html'; }

	function body_data()
	{
		bors_use("http://code.highcharts.com/stock/highstock.js");
		bors_use("http://code.highcharts.com/modules/exporting.js");

		$dbh = new driver_mysql('AB_FORUMS');

		$posts_stat = $dbh->select_array('posts', '`year`,
				MONTH(FROM_UNIXTIME(posts.posted)) AS month,
				forum_id,
				posts.posted AS ts,
				COUNT(*) AS count',
			array(
				'inner_join' => 'topics ON posts.topic_id = topics.id',
				'year>' => date('Y', time() - 86400*365.24*3),
				'posts.posted<' => time(),
				'group' => '`posts`.`year`, MONTH(FROM_UNIXTIME(`posts`.`posted`)), forum_id',
				'having' => 'COUNT(*) > 1000',
				'order' => '`posts`.`posted`',
			)
		);

		$mstart = $posts_stat[0]['ts'];

		$posts_by_month = array();

		foreach($posts_stat as $x)
		{
			$fid = $x['forum_id'];
			if(empty($posts_by_month[$fid]))
			{
				$posts_by_month[$fid] = array(
					'forum' => bors_load('balancer_board_forum', $fid),
					'data' => array(),
				);
			}

			$posts_by_month[$fid]['data'][] = $x;
		}

		$posts_stat = $dbh->select_array('posts', '`year`,
				forum_id,
				posts.posted AS ts,
				COUNT(*) AS count',
			array(
				'inner_join' => 'topics ON posts.topic_id = topics.id',
				'posts.posted>' => strtotime('1999-01-01'),
				'posts.posted<=' => time(),
				'group' => '`posts`.`year`, forum_id',
				'having' => 'COUNT(*) > 3000',
				'order' => '`posts`.`posted`',
			)
		);

		$ystart = $posts_stat[0]['ts'];

		$posts_by_year = array();

		foreach($posts_stat as $x)
		{
			$fid = $x['forum_id'];
			if(empty($posts_by_year[$fid]))
			{
				$posts_by_year[$fid] = array(
					'forum' => bors_load('balancer_board_forum', $fid),
					'data' => array(),
				);
			}

			$posts_by_year[$fid]['data'][] = $x;
		}

		return array_merge(parent::body_data(), compact('mstart', 'ystart', 'posts_by_month', 'posts_by_year'));
	}

 	function cache_static() { return rand(3600, 7200); }
}
