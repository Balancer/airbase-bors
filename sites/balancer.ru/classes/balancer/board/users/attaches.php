<?php

class balancer_board_users_attaches extends balancer_board_paginated
{
	function can_be_empty() { return false; }
	function is_loaded() { return (bool) $this->user(); }

	function main_class() { return 'balancer_board_attach'; }

	function where()
	{
		return array(
			'owner_id' => $this->id(),
		);
	}

	function order() { return '-post_id'; }

	function items_per_page() { return 20; }

	function title()    { return 'Все файлы пользователя '.$this->user()->title(); }
	function nav_name() { return 'все файлы'; }
	function parents()  { return array($this->user()->url()); }

	function auto_objects()
	{
		return array(
			'user' => 'balancer_board_user(id)',
		);
	}

	function preload_objects()
	{
		return array_merge(parent::preload_objects(), array(
			'post' => 'balancer_board_post(post_id)',
		));
	}

	function on_items_load(&$items)
	{
		parent::on_items_load($items);
		$posts = bors_field_array_extract($items, 'post');
		bors_objects_preload($posts, 'topic_id', 'balancer_board_topic', 'topic');
	}
}
