<?php

class balancer_board_objects_video extends bors_paginated
{
	var $title_ec = 'Все сообщения с видеороликами';
	var $nav_name_ec = 'видео';
	var $is_auto_url_mapped_class = true;

	var $main_class = 'balancer_board_posts_object';

	function config_class() { return 'balancer_board_config'; }
	function items_per_page() { return 10; }

//	function is_reversed() { return true; }

	function pre_show()
	{
		template_noindex();
		return parent::pre_show();
	}

	function where()
	{
		return array_merge(parent::where(), array(
			'target_class_name' => 'bors_external_youtube',
		));
	}

	function order() { return '-target_create_time'; }
}
