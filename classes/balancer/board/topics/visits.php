<?php

class balancer_board_topics_visit extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'punbb'; }
	function table_name() { return 'topic_visits'; }
	function table_fields()
	{
		return array(
			'user_id',
			'target_class_id',
			'target_object_id' => 'topic_id',
			'last_visit',
			'count',
			'first_visit',
			'last_post_id',
		);
	}
}
