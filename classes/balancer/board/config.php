<?php

class balancer_board_config extends bors_config
{
	function config_data()
	{
		return array_merge(parent::config_data(), array(
			'template' => 'forum/_header.html',
		));
	}
}
