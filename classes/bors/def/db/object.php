<?php

class_include('def_empty');

class def_db_object extends def_empty
{
	function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }

	var $db;

	var $match;
	function set_match($match) { $this->match = $match;	}

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

	var $stb_modify_time = NULL;
	function set_modify_time($unix_time, $db_update) { $this->set("modify_time", $unix_time, $db_update); }
	function modify_time($exactly = false)
	{
		if($exactly || $this->stb_modify_time)
			return $this->stb_modify_time;

		return time(); 
	}

	var $stb_create_time = NULL;
	function set_create_time($unix_time, $db_update) { $this->set("create_time", intval($unix_time), $db_update); }
	function create_time($exactly = false)
	{
		if($exactly || $this->stb_create_time)
			return $this->stb_create_time;

		if($this->stb_modify_time)
			return $this->stb_modify_time;

		return time(); 
	}

	function preShowProcess() {	return false; }
	function preParseProcess() {	return false; }
	function is_cache_disabled() { return true; }

	function set($field, $value, $db_update)
	{
		global $bors;
			
		$field_name = "stb_$field";

		if($db_update && $this->$field_name != $value)
		{
			$this->changed_fields[$field] = $field_name;
			$bors->add_changed_object($this);
		}

		$this->$field_name = $value;
	}

	function render_engine() { return 'render_page'; }
	function template_vars() { return 'body source'; }
	function template_local_vars() { return 'create_time description id modify_time nav_name title'; }

	var $stb_description = NULL;
	function set_description($description, $db_update) { $this->set("description", $description, $db_update); }
	function description() { return $this->stb_description; }
}
