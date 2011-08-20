<?php

class balancer_board_ban extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'punbb'; }
	function table_name() { return 'bans'; }
	function table_fields()
	{
		return array(
			'id',
			'create_time',
			'username',
			'ip',
			'email',
			'message',
			'expire',
			'moderator_id',
			'target_uri',
		);
	}

	static function ban($user, $ip, $expire = false, $target = NULL)
	{
		$ban = object_new_instance('balancer_board_ban', array(
			'username' => object_property($user, 'title'),
			'ip' => $ip,
			'email' => object_property($user, 'email'),
			'message' => ec('Автоматический бессрочный бан за рассылку спама'),
			'expire' => $expire ? $expire + time() : NULL,
			'moderator_id' => bors()->user_id(),
			'target_uri' => $target ? $target->internal_uri_ascii() : NULL,
		));
	}

	static function find_by_name($username)
	{
		static $cache = array();
		if(array_key_exists($username, $cache))
			return $cache[$username];

		return $cache[$username] = bors_find_first('balancer_board_ban', array(
			'username' => $username,
			'(expire IS NULL OR expire >'.time().')',
			'order' => '-id',
		));
	}
}
