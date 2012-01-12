<?php

class balancer_board_action extends base_page_db
{
	function main_db() { return config('punbb.database', 'punbb'); }
	function main_table() { return 'board_actions'; }
	function main_table_fields()
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
//		debug_hidden_log('__0test-message', "{$object}: {$message}");
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
//<li class="red">marata [17.10.2010 11:00]: Предупреждение пользователю KILLO: <a href="http://balancer.ru/support/2008/09/p2262128.html">Обсуждение модераториалов [KILLO#17.10.10 05:38]</a></li>
		return preg_replace('!(Предупреждение пользователю).+?:( <a href=".+?">).+?\[(.+?)\]!', '$1$2$3', $this->message_raw());
	}
}

