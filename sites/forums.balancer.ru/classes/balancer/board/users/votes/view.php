<?php

class balancer_board_users_votes_view extends balancer_board_meta_main
{
	var $auto_map = true;
	function title()
	{
		return 'Оценки сообщений пользователя '.$this->user();
	}

	var $nav_name = 'оценки';

	var $main_class = 'airbase_vote';

	function where()
	{
		return array('target_user_id' => $this->id());
	}

	function group()
	{
		return 'target_class_id,target_object_id';
	}

	var $order = '-create_time';
	var $items_per_page = 50;

	function parents() { return array('http://forums.balancer.ru/users/'.$this->id().'/'); }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'user' => 'airbase_user(id)',
		));
	}

	function on_items_load(&$items)
	{
		parent::on_items_load($items);
		bors_objects_targets_preload($items);
		$posts = bors_field_array_extract($items, 'target');
		bors_objects_preload($posts, 'topic_id', 'balancer_board_topic', 'topic');
		bors_objects_preload($posts, 'id', 'balancer_board_posts_cache', 'cache');
		$topics = bors_field_array_extract($posts, 'topic');
		bors_objects_preload($topics, 'forum_id', 'balancer_board_forum', 'forum');
		$forums = bors_field_array_extract($topics, 'forum');
		bors_objects_preload($forums, 'category_id', 'balancer_board_category', 'category');
	}
}
