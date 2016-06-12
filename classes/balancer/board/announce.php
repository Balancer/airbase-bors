<?php

class balancer_board_announce extends bors_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'AB_RESOURCES'; }
	function table_name() { return 'announces'; }

	function replace_on_new_instance() { return true; }

	function table_fields()
	{
		return [
			'id',
			'announce_uuid',
			'create_time' => 'UNIX_TIMESTAMP(`create_ts`)',
			'title',
			'description' => ['type' => 'bbcode'],
			'announce_url' => 'url',
			'image_url',
		];
	}
}
