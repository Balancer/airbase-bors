<?php

class users_toprep extends balancer_board_page
{
	function template()
	{
		template_noindex();
		return 'forum/_header.html';
	}

	function title() { return ec("Репутации пользователей форума"); }
	function nav_name() { return ec("репутации"); }

	function parents() { return array("http://www.balancer.ru/users/"); }

	function body_data()
	{
		$latest = bors_find_all('airbase_user_reputation', array('is_deleted' => false, 'order' => '-create_time', 'limit' => 30));
		$user_ids = array();
		foreach($latest as $rep)
			$user_ids[$rep->user_id()] = $user_ids[$rep->voter_id()] = true;


		return array(
			'high' => bors_find_all('balancer_board_user', array('order' => '-reputation', 'limit' => 50)),
			'low' => bors_find_all('balancer_board_user', array('order' => 'reputation', 'limit' => 50)),

			'pure_high' => bors_find_all('balancer_board_user', array('order' => '-pure_reputation', 'limit' => 50)),
			'pure_low' => bors_find_all('balancer_board_user', array('order' => 'pure_reputation', 'limit' => 50)),

			'latest' => $latest,
			'users'	=> bors_find_all('balancer_board_user', array('id IN' => array_keys($user_ids), 'by_id' => true)),

			'total_votes' => bors_count('airbase_user_reputation', array('is_deleted' => false)),
		);
	}

	function url() { return "http://www.balancer.ru/users/toprep/"; }

	function cache_static() { return config('static_forum') ? 600 : 0; }
}
