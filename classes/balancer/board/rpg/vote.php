<?php

class balancer_board_rpg_vote extends balancer_board_object_db
{
	function table_name() { return 'rpg_request_votes'; }

	function ignore_on_new_instance() { return true; }

	function table_fields()
	{
		return array(
			'id',
			'request_id' => array('class' => 'balancer_board_rpg_request'),
			'user_id' => array('class' => 'balancer_board_user'),
			'score',
			'create_time' => array('name' => 'UNIX_TIMESTAMP(`create_time`)'),
			'create_ts' => array('name' => 'UNIX_TIMESTAMP(`create_ts`)'),
			'modify_ts' => array('name' => 'UNIX_TIMESTAMP(`modify_ts`)'),
			'owner_id',
			'last_editor_id',
			'last_editor_ip',
			'last_editor_ua',
		);
	}
}
