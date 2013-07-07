<?php

class user_salt extends base_object_db
{
	function db_name() { return 'AB_BORS'; }
	function table_name()   { return 'user_salt'; }
	function table_fields()
	{
		return array(
			'id',
			'user_id',
			'uid',
			'describe',
			'last_access',
		);
	}

	function uri2id($uid)
	{
		return $this->select('id', array('uid=' => $uid));
	}
}
