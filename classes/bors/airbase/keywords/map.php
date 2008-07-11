<?php

class airbase_keywords_map extends base_object_db
{
	function main_db_storage() { return 'BORS'; }
	function main_table_storage() { return 'keywords_map'; }
	
	function main_table_fields() { return array('id', 'keyword_id', 'object_class_id' => 'class_id', 'object_id'); }
}
