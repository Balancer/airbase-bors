<?php

class livestreet_topics_content extends bors_object_db
{
	function db_name() { return 'LIVESTREET'; }
	function table_name() { return 'ls_topic_content'; }

	function table_fields()
	{
		return array(
			'id' => 'topic_id',
			'body' => 'topic_text',
			'topic_text_short' => array('type' => 'bbcode'),
			'source' => 'topic_text_source',
			'topic_extra' => array('type' => 'bbcode'),
		);
	}

	function __dev()
	{
		var_dump(bors_load(__CLASS__, 95));
	}
}
