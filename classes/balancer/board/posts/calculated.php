<?php

class balancer_board_posts_calculated extends balancer_board_object_db
{
	function table_name() { return 'posts_calculated_fields'; }

	function table_fields()
	{
		return array(
			'id' => 'post_id',
			'answers_total',
			'answers_other_topics',
			'best10_ts' => array('name' => 'UNIX_TIMESTAMP(`best10_ts`)'),
		);
	}
}
