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
}
