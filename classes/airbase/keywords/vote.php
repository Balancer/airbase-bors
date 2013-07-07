<?php

class airbase_keywords_vote extends base_object_db
{
	function db_name() { return 'AB_BORS'; }
	function table_name() { return 'keywords_votes'; }

	function table_fields() { return array('id', 'keyword_id', 'object_class_id', 'object_id', 'user_id', 'vote', 'create_time'); }
}
