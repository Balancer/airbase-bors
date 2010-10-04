<?php

class balancer_board_mobile_config extends balancer_board_config
{
	function config_data()
	{
		return array_merge(parent::config_data(array(
			'access_engine' => 'balancer_board_mobile_access',
		)));
	}
}
