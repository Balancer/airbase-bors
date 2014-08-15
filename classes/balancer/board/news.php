<?php

class balancer_board_news extends balancer_board_paginated
{
	var $title = 'Новости на форумах Balancer.Ru';
	var $nav_name = 'новости';
	var $auto_map = true;

	var $main_class = 'balancer_board_post';

	function where()
	{
		$xrefs = bors_find_all('common_keyword_bind', [
			'keyword_id' => common_keyword::loader('новости')->id(),
			'target_class_name IN' => array('forum_topic', 'balancer_board_topic'),
			'target_create_time>' => time() - 86400*30,
		]);

		return [
			'topic_id IN' => bors_field_array_extract($xrefs, 'target_object_id'),
			'is_deleted' => false,
			'create_time>' => time() - 86400*30,
			'answer_to_id' => 0,
		];
	}

	function order()
	{
		return '-create_time';
	}

	function items_per_page() { return 25; }
}
