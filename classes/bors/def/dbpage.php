<?php

class def_dbpage extends base_page
{
	var $db;

	function parents()
	{
//		echo "match=".print_r($this->match, true)."<br />\n";
		return array("http://{$this->match[1]}{$this->match[2]}");
	}

	function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }
	function id_field() { return 'id'; }

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
	
	function __construct($id)
	{
		$driver = $this->db_driver();
		$this->db = &new $driver($this->main_db_storage());
		$id = $this->uri2id($id);
			
		parent::__construct($id);

		if(!($qlist = $this->_global_queries()))
			return;
				
		foreach($qlist as $qname => $q)
		{
			if(isset($GLOBALS['cms']['templates']['data'][$qname]))
				continue;
			
			$cache = NULL;
			if(preg_match("!^(.+)\|(\d+)$!s", $q, $m))
			{
				$q		= $m[1];
				$cache	= $m[2];
			}
					
			$GLOBALS['cms']['templates']['data'][$qname] = $this->db->get_array($q, false, $cache);
		}
	}

	function db_driver() { return 'driver_mysql'; }
	
	function edit_link() { return $this->uri."?edit"; }
	function storage_engine() { return 'storage_db_mysql'; }

	function fields() { return array($this->main_db_storage() => $this->main_db_fields()); }
	function main_db_fields()
	{
		return array(
			$this->main_table_storage() => $this->main_table_fields(),
		);
	}
}

