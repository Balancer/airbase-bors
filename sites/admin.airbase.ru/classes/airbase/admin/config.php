<?php

class airbase_admin_config extends bors_admin_config
{
	function config_data()
	{
		return array_merge(parent::config_data(), array(
			'access_engine' => 'airbase_access_balancer',
		));
	}
}
