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
}
