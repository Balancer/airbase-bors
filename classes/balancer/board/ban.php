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
		);
	}

	static function ban($user, $ip, $expire = false)
	{
		$ban = object_new_instance('balancer_board_ban', array(
			'username' => $user->title(),
			'ip' => $ip,
			'email' => $user->email(),
			'message' => ec('Автоматический бессрочный бан за рассылку спама'),
			'expire' => $expire ? $expire + time() : NULL,
			'moderator_id' => bors()->user_id(),
		));
	}
}
