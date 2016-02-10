<?php

class balancer_board_posts_cache extends balancer_board_object_db
{
	function ignore_on_new_instance() { return true; }

	function table_name() { return 'posts_cache'; }

	function table_fields()
	{
		return array(
			'id',
			'body' => array('type' => 'html'),
			'body_ts' => array('name' => 'UNIX_TIMESTAMP(`body_ts`)'),
			'is_body_temporary',
		);
	}
}
