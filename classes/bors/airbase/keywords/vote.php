<?php

class airbase_keywords_vote extends base_object_db
{
	function main_db_storage() { return 'BORS'; }
	function main_table_storage() { return 'keywords_votes'; }
	
	function main_table_fields() { return array('id', 'keyword_id', 'object_class_id', 'object_id', 'user_id', 'vote', 'create_time'); }
}