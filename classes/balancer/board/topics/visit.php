<?php

class balancer_board_topics_visit extends balancer_board_object_db
{
	function replace_on_new_instance() { return true; }
	function table_name() { return 'topic_visits'; }
	function table_fields()
	{
		return array(
			'id', // => 'user_id,topic_id',
			'user_id',
			'target_class_id',
			'target_object_id' => 'topic_id',
			'last_visit',
			'count',
			'first_visit',
			'last_post_id',
			'is_disabled',
			'create_time' => 'UNIX_TIMESTAMP(`create_ts`)',
			'modify_time' => 'UNIX_TIMESTAMP(`modify_ts`)',
		);
	}

	static function last_topic_user_visit($user_id, $topic_id)
	{
		$v = bors_find_first('balancer_board_topics_visit', [
			'user_id' => $user_id,
			//FIXME: добавить проверку класса
			'target_object_id' => $topic_id,
		]);

		return $v;
	}
}
