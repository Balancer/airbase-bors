<?php

class balancer_board_subscription extends balancer_board_object_db
{
	function table_name() { return 'subscriptions'; }

	function ignore_on_new_instance() { return true; }

	function table_fields()
	{
		return array(
			'id' => 'user_id,topic_id',
			'user_id' => array('class' => 'balancer_board_user'),
			'topic_id' => array('class' => 'balancer_board_topic', 'have_null' => true),
			'create_time' => array('name' => 'UNIX_TIMESTAMP(`create_ts`)'),
			'modify_time' => array('name' => 'UNIX_TIMESTAMP(`modify_ts`)'),
			'owner_id',
			'last_editor_id',
			'last_editor_ip',
			'last_editor_ua',
		);
	}
}
