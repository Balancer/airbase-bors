<?php

class balancer_board_posts_cached extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return config('punbb.database', 'AB_FORUMS'); }
	function table_name() { return 'posts_cached_fields'; }

	function table_fields()
	{
		return array(
			'id' => 'post_id',
			'flag_db' => 'flag',
			'warning_id',
			'answers_count',
			'mark_best_date',
			'best_page_num',
		);
	}

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_post(id)',
		);
	}
}
