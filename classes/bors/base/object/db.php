<?php

class base_object_db extends base_object
{
	var $db;

	function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }

	function new_instance()
	{
		$tab = $this->main_table_storage();
		if(!$tab)
			exit("Try to gent new instance with empty main table in class ".__FILE__.":".__LINE__);
			
		$this->db->insert($tab, array());
		$this->set_id($this->db->get_last_id());
		$this->set_create_time(time(), true);
		$this->set_modify_time(time(), true);
	}

	function uri2id($id) { return $id; }
	
	function db_driver() { return 'driver_mysql'; }
	
	function __construct($id)
	{
		$driver = $this->db_driver();
		$this->db = &new $driver($this->main_db_storage());
		$id = $this->uri2id($id);
			
		parent::__construct($id);
	}
		
	function storage_engine() { return 'storage_db_mysql'; }

	function select($field, $where_map) { return $this->db->select($this->main_table_storage(), $field, $where_map); }
	function select_array($field, $where_map) { return $this->db->select_array($this->main_table_storage(), $field, $where_map); }
}
