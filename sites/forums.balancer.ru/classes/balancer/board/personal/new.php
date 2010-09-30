<?php

class balancer_board_personal_new extends bors_paginated
{
	function title() { return ec('Темы, обновившиеся с Вашего последнего захода'); }
	function nav_name() { return ec('обновившиеся'); }
	function is_auto_url_mapped_class() { return true; }
	function template() { return 'forum/_header.html'; }

	function items_per_page() { return 50; }

	function main_class() { return 'balancer_board_topic'; }
	function order() { return '-last_post_create_time'; }

	function on_items_load($items)
	{
		bors_objects_preload($items, 'forum_id', 'balancer_board_forum', 'forum');
		bors_objects_preload($items, 'first_post_id', 'balancer_board_post', 'first_post');
		bors_objects_preload($items, 'last_post_id', 'balancer_board_post', 'last_post');
	}

	function where()
	{
		$user = bors()->user();
		return array_merge(parent::where(), array(
			'forum_id NOT IN' => array(37,190,191),
			'modify_time>' => $user->previous_session_end(),
		));
	}

	function url_engine() { return 'url_calling2'; }
}
