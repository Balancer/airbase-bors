<?php

class wrk_mauth extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'WRK'; }
	function table_name() { return 'multi_auth'; }
	function table_fields()
	{
		return array(
			'user_id',
			'signature',
			'create_time',
		);
	}

	function replace_on_new_instance() { return true; }
}
