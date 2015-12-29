<?php

class balancer_board_post_sparse extends balancer_board_object_db
{
	function table_name() { return 'post_sparse_data'; }

	function table_fields()
	{
		return array(
			'id' => 'post_id,name',
			'post_id',
			'name',
			'value' => array('type' => 'bbcode'),
			'create_time' => array('name' => 'UNIX_TIMESTAMP(`create_ts`)'),
			'modify_time' => array('name' => 'UNIX_TIMESTAMP(`modify_ts`)'),
			'owner_id',
			'last_editor_id',
			'last_editor_ip',
			'last_editor_ua',
		);
	}
}
