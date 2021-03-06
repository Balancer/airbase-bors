<?php

class balancer_board_action extends balancer_board_object_db
{
	var $class_title = 'действие';

	function table_name() { return 'board_actions'; }
	function table_fields()
	{
		return array(
			'id',
			'create_time',
			'owner_id',
			'target_class_name',
			'target_object_id',
			'message_raw' => 'message',
			'is_moderatorial',
			'is_public',
		);
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'owner' => 'airbase_user(owner_id)',
		));
	}

	function auto_targets()
	{
		return array_merge(parent::auto_targets(), array(
			'target' => 'target_class_name(target_object_id)',
		));
	}

	function replace_on_new_instance() { return true; }

	function is_new() { return $this->create_time() > time() - 3600*12; }
	function type_class()
	{
		$cls = array();
		if($this->is_moderatorial())
			$cls[] = 'red';
		if($this->is_new())
			$cls[] = 'b';

		return $cls ? ' class="'.join(' ', $cls).'"' : '';
	}

	static function add($object, $message, $is_moderatorial = false, $is_public = true)
	{
//		bors_debug::syslog('__0test-message', "{$object}: {$message}");
		bors_new('balancer_board_action', array(
			'target_class_name' => $object->class_name(),
			'target_object_id' => $object->id(),
			'message_raw' => $message,
			'is_moderatorial' => $is_moderatorial,
			'is_public' => $is_public,
		));
	}

	function message()
	{
//<li class="red">marata [17.10.2010 11:00]: Предупреждение пользователю KILLO: <a href="http://www.balancer.ru/support/2008/09/p2262128.html">Обсуждение модераториалов [KILLO#17.10.10 05:38]</a></li>
		return preg_replace('!(Предупреждение пользователю).+?:( <a href=".+?">).+?\[(.+?)\]!', '$1$2$3', $this->message_raw());
	}

	function item_list_fields()
	{
		return [
			'ctime' => 'Дата',
			'owner' => 'Владелец',
			'target()->titled_link()' => 'Объект',
			'message' => 'Сообщение',
		];
	}
}
