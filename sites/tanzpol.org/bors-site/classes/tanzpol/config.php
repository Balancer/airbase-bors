<?php

class tanzpol_config extends bors_config
{
	function template_data()
	{
		return array_merge(parent::template_data(), array(
			'bottom_counters' => 'xfile:tanzpol/yandex-metrika.html',
		));
	}
}