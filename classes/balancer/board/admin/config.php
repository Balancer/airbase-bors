<?php

class balancer_board_admin_config extends bors_config
{
	function config_data()
	{
		return array(
			'template' => 'forum/page.html',
			'access_engine' => 'airbase_forum_admin_access_split',
//			'access_engine' => 'balancer_board_admin_access',
		);
	}

	function template_data()
	{
		return array(
			'skip_ad' => true,
		);
	}
}
