<?php

class users_toprep extends base_page
{
	function template()
	{
		template_noindex();
		return 'forum/_header.html';
	}

	function title() { return ec("Репутации пользователей форума"); }
	function nav_name() { return ec("репутации"); }

	function parents() { return array("http://balancer.ru/users/"); }

	function local_template_data_set()
	{
		$latest = objects_array('airbase_user_reputation', array('order' => '-create_time', 'limit' => 50));
		$user_ids = array();
		foreach($latest as $rep)
			$user_ids[$rep->user_id()] = $user_ids[$rep->voter_id()] = true;
		
		return array(
			'high' => objects_array('bors_user', array('order' => '-reputation', 'limit' => 50)),
			'low' => objects_array('bors_user', array('order' => 'reputation', 'limit' => 50)),

			'pure_high' => objects_array('bors_user', array('order' => '-pure_reputation', 'limit' => 50)),
			'pure_low' => objects_array('bors_user', array('order' => 'pure_reputation', 'limit' => 50)),
			
			'latest' => $latest,
			'users'	=> objects_array('forum_user', array('id IN' => array_keys($user_ids), 'by_id' => true)),
		);
	}

	function url() { return "http://balancer.ru/users/toprep/"; }

	function cache_static() { return 600; }
}
