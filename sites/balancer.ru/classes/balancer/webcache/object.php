<?php

class balancer_webcache_object extends bors_object_db
{
	function db_name() { return 'BALANCER'; }
	function table_name() { return 'webcache'; }

	function table_fields()
	{
		return array(
			'id',
			'original_url_full',
			'original_url_key',
			'handler_class_name',
			'handler_id',
			'local_url',
			'create_ts' => array('name' => 'UNIX_TIMESTAMP(`create_ts`)'),
			'modify_ts' => array('name' => 'UNIX_TIMESTAMP(`modify_ts`)'),
		);
	}
}
