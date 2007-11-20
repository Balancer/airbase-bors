<?php

class base_page_db extends def_dbpage
{
//	function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }
	function fields_first() { return NULL; }

	function select($field, $where_map) { return $this->db->select($this->main_table_storage(), $field, $where_map); }
	function select_array($field, $where_map) { return $this->db->select_array($this->main_table_storage(), $field, $where_map); }

	function storage_engine() { return 'storage_db_mysql'; }

	var $_autofields;
	function autofield($field)
	{
		if(method_exists($this, $method = "field_{$field}_storage"))
			return $this->$method();
	
		if(!empty($this->_autofields))
			return @$this->_autofields[$field];
			
		$_autofields = array();
		
		foreach(split(' ', $this->autofields()) as $f)
		{
			$id	  = 'id';
			if(preg_match('!^(\w+)\((\w+)\)(.*?)$!', $f, $match))
			{
				$f  = $match[1].$match[3];
				$id = $match[2];
			}

			$name = $f;
			if(preg_match('!^(\w+)\->(\w+)$!', $f, $match))
			{
				$f    = $match[1];
				$name = $match[2];
			}
			$this->_autofields[$name] = "{$f}({$id})";
		}
		
		return @$this->_autofields[$field];
	}
}
