<?php

class airbase_user_warning extends base_object_db
{
	function fields()
	{
		return array('punbb' => array('warnings' => array(
			'id',
			'user_id',
			'create_time' => 'time',
			'score',
			'moderator_id',
			'moderator_name',
			'referer' => 'uri',
			'source' => 'comment',
			'warn_class_id',
			'warn_object_id',
		)));
	}

	function moderator() { return object_load('forum_user', $this->moderator_id()); }
	function user() { return object_load('forum_user', $this->user_id()); }
}
