<?php

class balancer_board_users_votes_main extends balancer_board_meta_main
{
	var $auto_map = true;

	var $main_class = 'bors_votes_thumb';
	var $title = 'Оценки сообщений пользователей';
	var $nav_name = 'оценки';

	function group()
	{
		return 'target_class_id,target_object_id';
	}

	var $order = '-create_time';
	var $items_per_page = 50;
}
