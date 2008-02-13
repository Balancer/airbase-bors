<?php

class base_page_db extends def_dbpage
{
//	function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }
	function fields_first() { return NULL; }

	function select($field, $where_map) { return $this->db->select($this->main_table_storage(), $field, $where_map); }
	function select_array($field, $where_map) { return $this->db->select_array($this->main_table_storage(), $field, $where_map); }

	function storage_engine() { return 'storage_db_mysql'; }

	function _global_queries() { return array(); }
}
