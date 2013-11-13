<?php

class balancer_board_admin_config extends bors_config
{
	function object_data()
	{
		return array_merge(parent::object_data(), array(
			'template' => 'forum/page.html',
			'access_engine' => 'airbase_forum_admin_access_split',
//			'access_engine' => 'balancer_board_admin_access',
		));
	}

	function page_data()
	{
		return array_merge(parent::page_data(), array(
			'skip_ad' => true,
		));
	}

	function view_data()
	{
		return array_merge(parent::view_data(), array(
			'project' => bors_load('balancer_board_project', NULL),
		));
	}

	function pre_show()
	{
		twitter_bootstrap::load();
		return parent::pre_show();
	}
}
