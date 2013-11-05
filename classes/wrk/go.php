<?php

class wrk_go extends balancer_board_page
{
	static function make_short_url($object)
	{
		switch($object->class_name())
		{
			case 'balancer_board_post':
			case 'forum_post':
				$class_id = 'p';
				break;
			case 'balancer_board_topic':
			case 'forum_topic':
				$class_id = 't';
				break;
			default:
				debug_hidden_log('wrk_go_error', 'Unknown class type for '.$object->debug_title());
				return false;
		}

		$object_id = $object->id();
		if(!is_numeric($object_id))
		{
			debug_hidden_log('wrk_go_error', 'Non integer object id for '.$object->debug_title());
			return false;
		}

		return 'http://wrk.ru/~'.$class_id.base_convert($object_id, 10, 36);
	}

	function pre_parse()
	{
		switch($this->id()) // Идентификатор класса
		{
			case 'p':
				$class_name = 'balancer_board_post';
				break;
			case 't':
				$class_name = 'balancer_board_topic';
				break;
			default:
				return false;
		}

		$object_id  = base_convert($this->page(), 36, 10);
		$object = object_load($class_name, $object_id);

		if($object)
			return go($object->url_in_container());

		return false;
	}
}
