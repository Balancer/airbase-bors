<?php

class balancer_board_stat_topics_longest extends balancer_board_page
{
	var $title = 'Самые длинные топики форумов';
	var $nav_name = 'длинные';
	var $auto_map = true;

	function body_data()
	{
		$topics = bors_find_all('balancer_board_topic', [
			'order' => '-num_replies',
			'limit' => 50
		]);
		return array_merge(parent::body_data(), compact('topics'));
	}
}
