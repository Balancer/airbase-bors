<?php

class balancer_board_forums_visit extends balancer_board_object_db
{
	function db_name() { return 'AB_FORUMS'; }
	function table_name() { return 'forum_visits'; }

	function table_fields()
	{
		return array(
			'id' => 'user_id,forum_id',
			'user_id' => array('class' => 'balancer_board_user'),
			'forum_id' => array('class' => 'balancer_board_forum'),
			'first_visit',
			'last_visit',
			'count',
			'is_disabled',
		);
	}
}
