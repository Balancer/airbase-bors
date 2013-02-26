<?php

class balancer_board_topics_visit extends balancer_board_object_db
{
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
			'is_disabled',
		);
	}
}
