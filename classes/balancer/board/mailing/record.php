<?php

class balancer_board_mailing_record extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'AB_BORS'; }
	function table_name() { return 'bors_mailing'; }
	function table_fields()
	{
		return array(
			'id',
			'target_class_name',
			'target_object_id',
			'target_user_id',
			'create_time',
		);
	}
/*
	function auto_objects()
	{
		return array(
			'target' =>
		);
	}
*/
}

