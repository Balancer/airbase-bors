<?php

class users_topwarnings extends balancer_board_page
{
	function pre_show()
	{
		template_noindex();
		return parent::pre_show();
	}

	function title() { return ec("Штрафы пользователей форума"); }
	function nav_name() { return ec("штрафы"); }

	function body_data()
	{
		return array(
			'warnings_last' => bors_find_all('airbase_user_warning', array('order' => '-create_time', 'limit' => 25)),
			'top_warn_users' => bors_find_all('balancer_board_user', array(
				'last_post>' => time() - 86400*31,
				'last_post - registered >' => 86400*7,
				'order' => '86400.0*warnings_total/(last_post - registered) DESC',
				'limit' => 20)),
			'top_warn_relative_users' => @$top_warn_reltive_users,
		);
	}

	function cache_static() { return rand(300,1200); }
}
