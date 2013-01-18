<?php

class balancer_board_posts_object extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return config('punbb.database', 'AB_FORUMS'); }
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

	static function register($object, $params = array())
	{
		if(!($post = defval($params, 'self')))
			return;

		if(is_array($object))
			list($object_class_name, $object_id) = $object;
		else
			list($object_class_name, $object_id) = array($object->class_name(), $object->id());

		if($post->class_name() != 'balancer_board_post' && $post->class_name() != 'forum_post')
		{
			debug_hidden_log('objects_register_not_yet', "Try to register {$post} width $object_class_name($object_id)");
			return;
		}

		$object_class_id = class_name_to_id($object_class_name);

		if(bors_find_first('balancer_board_posts_object', array(
			'post_id' => $post->id(),
//			'target_class_name' => $object_class_name,
			'target_class_id' => $object_class_id,
			'target_object_id' => $object_id,
		)))
			return;

		bors_new('balancer_board_posts_object', array(
			'post_id' => $post->id(),
			'target_class_id' => $object_class_id,
			'target_class_name' => $object_class_name,
			'target_object_id' => $object_id,
			'target_create_time' => $post->create_time(),
			'target_score' => $post->score(),
		));
	}
}
