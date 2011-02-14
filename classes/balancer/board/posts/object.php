<?php

class balancer_board_posts_object extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'punbb'; }
	function table_name() { return 'board_objects'; }
	function table_fields()
	{
		return array(
			'id',
			'post_id',
			'target_class_id',
			'target_class_name',
			'target_object_id',
			'target_create_time',
			'target_score',
		);
	}

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_post(post_id)',
		);
	}

	function ignore_on_new_instance() { return true; }
}
