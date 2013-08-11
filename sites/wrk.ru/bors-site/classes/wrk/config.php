<?php

class wrk_config extends bors_config
{
	function template_data()
	{
		$template_top_menu = array(
			''			=>	'главная',
			'blogs'	=>	'блоги',
			'about'	=>	'о проекте',
		);

		return array_merge(parent::template_data(), compact('template_top_menu'));
	}

	function config_data()
	{
		$template = 'xfile:wrk/light.html';

		return array_merge(parent::config_data(), compact('template'));
	}
}
