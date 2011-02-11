<?php

class balancer_board_users_friend extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'punbb'; }
	function table_name() { return 'friends'; }
	function table_fields()
	{
		return array(
			'id' => 'CONCAT(user_id,"-",friend_id)',
			'user_id',
			'friend_id',
		);
	}

	function ignore_on_new_instance()  { return true; }
}
