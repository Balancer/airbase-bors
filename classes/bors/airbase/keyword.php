<?php

class airbase_keyword extends base_object_db
{
	function main_db_storage() { return 'BORS'; }
	function main_table_storage() { return 'keywords'; }
	
	function main_table_fields() { return array('id', 'keyword', 'keyword_original', 'modify_time'); }
}
