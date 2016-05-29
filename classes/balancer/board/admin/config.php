<?php

class balancer_board_admin_config extends bors_config
{
	function object_data()
	{
		return array_merge(parent::object_data(), array(
			'template' => 'forum/page.html',
			'access_engine' => 'airbase_forum_admin_access_split',
//			'access_engine' => 'balancer_board_admin_access',
			'app' => bors_load(balancer_board_app::class, NULL),
		));
	}

	function page_data()
	{
		return array_merge(parent::page_data(), array(
			'skip_ad' => true,
		));
	}

	function pre_show()
	{
		twitter_bootstrap::load();
		return parent::pre_show();
	}
}
