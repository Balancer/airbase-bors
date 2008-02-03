<?php

class user_salt extends base_object_db
{
	function main_db_storage() { return 'BORS'; }
	function main_table_storage()   { return 'user_salt'; }

	function uri2id($uid)
	{
		return $this->select('id', array('uid=' => $uid));
	}

	var $stb_user_id;		function field_user_id_storage()		{ return 'user_id(id)'; }
	var $stb_uid;			function field_uid_storage()			{ return 'uid(id)'; }
	var $stb_describe;		function field_describe_storage()		{ return 'describe(id)'; }
	var $stb_last_access;	function field_last_access_storage()	{ return 'last_access(id)'; }
}
