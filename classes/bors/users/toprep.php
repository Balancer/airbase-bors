<?php

class users_toprep extends base_page
{
	function template()
	{
		templates_noindex();
		return 'forum/_header.html';
	}

	function title() { return ec("Репутации пользователей форума"); }
	function nav_name() { return ec("репутации"); }

	function parents() { return array("http://balancer.ru/users/"); }

	function local_template_data_set()
	{
		return array(
			'high' => objects_array('bors_user', array('order' => '-reputation', 'limit' => 50)),
			'low' => objects_array('bors_user', array('order' => 'reputation', 'limit' => 50)),

			'pure_high' => objects_array('bors_user', array('order' => '-pure_reputation', 'limit' => 50)),
			'pure_low' => objects_array('bors_user', array('order' => 'pure_reputation', 'limit' => 50)),
		);
	}

	function url() { return "http://balancer.ru/users/toprep/"; }

	function cache_static() { return 3600; }
}
