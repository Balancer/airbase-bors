<?php

class balancer_board_posts_object extends balancer_board_object_db
{
	function table_name() { return 'board_objects'; }
	function table_fields()
	{
		return array(
			'id',
			'post_id',
			'user_id',
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

	function auto_targets()
	{
		return array(
			'target' => 'target_class_name(target_object_id)',
		);
	}

	function ignore_on_new_instance() { return true; }

	static function register_object($post, $object)
	{
		if(is_numeric($post))
			$post = bors_load('balancer_board_post', $post);

		return self::register($object, array('self' => $post));
	}

	static function register($object, $params = array())
	{
		if(!$object)
		{
			debug_hidden_log('post-objects-error', "Try to register empty object with ".print_r($params, true));
			return;
		}

		if(!($post = defval($params, 'self')))
			return;

//		if(config('is_developer')) { var_dump($object, $post); exit('register'); }

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
			'user_id' => $post->owner_id(),
			'target_class_id' => $object_class_id,
			'target_class_name' => $object_class_name,
			'target_object_id' => $object_id,
			'target_create_time' => $post->create_time(),
			'target_score' => $post->score(),
		));
	}

	static function find_containers($object)
	{
//		echo "Find containers for {$object->debug_title()}<br/>";
		$xrefs = bors_find_all('balancer_board_posts_object', array(
			'target_class_id' => $object->class_id(),
//			'target_class_name' => $object_class_name,
			'target_object_id' => $object->id(),
		));
//var_dump($xrefs);
		$containers = array();
		foreach($xrefs as $xref)
			$containers[] = $xref->post();

		usort($containers, function($x, $y) { return $x->create_time() - $y->create_time();});

		return $containers;
	}
}
