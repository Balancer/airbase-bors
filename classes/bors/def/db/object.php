<?php

class_include('def_empty');

class def_db_object extends def_empty
{
	function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }

	var $db;

	function __construct($id)
	{
		$this->db = &new DataBase($this->main_db_storage());
		if(method_exists($this, 'uri2id'))
			$id = $this->uri2id($id);
			
		parent::__construct($id);
	}

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
}
