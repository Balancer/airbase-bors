<?php

class user_salt extends base_object_db
{
	function main_db() { return 'AB_BORS'; }
	function main_table()   { return 'user_salt'; }
	function main_table_fields()
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
