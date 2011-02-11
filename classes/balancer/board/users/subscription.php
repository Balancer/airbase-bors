<?php

class balancer_board_users_subscription extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'punbb'; }
	function table_name() { return 'subscriptions'; }
	function table_fields()
	{
		return array(
			'id' => 'CONCAT(user_id,"-",topic_id)',
			'user_id',
			'topic_id',
		);
	}

	function ignore_on_new_instance()  { return true; }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'user' => 'balancer_board_user(user_id)',
		));
	}
}
