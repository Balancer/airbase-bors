<?php

class tanzpol_config extends bors_config
{
	function page_data()
	{
		return array_merge(parent::page_data(), array(
			'bottom_counters' => 'xfile:tanzpol/yandex-metrika.html',
		));
	}
}