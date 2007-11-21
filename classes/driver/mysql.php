<?php

class driver_mysql extends DataBase
{
	// "Пустой" конструктор, чтобы не передавать в DataBase параметр $page
	function __construct($db) { parent::__construct($db); }

	function select($table, $field, $where_map)
	{
		$where = array();
		foreach($where_map as $f => $v)
			$where[] = $f . '\'' . addslashes($v) . '\'';

		return $this->get("SELECT $field FROM $table WHERE ".join(' AND ', $where));
	}

	function select_array($table, $field, $where_map)
	{
		$where = array();
		foreach($where_map as $f => $v)
			$where[] = $f . '\'' . addslashes($v) . '\'';

		return $this->get_array("SELECT $field FROM $table WHERE ".join(' AND ', $where), false);
	}
}
