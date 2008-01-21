<?php

require_once('classes/inc/mysql.php');

class driver_mysql extends DataBase
{
	// "Пустой" конструктор, чтобы не передавать в DataBase параметр $page
	function __construct($db) { parent::__construct($db); }

	function select($table, $field, $where_map = array())
	{
		if($order = @$where_map['order'])
		{
			$order = "ORDER BY {$order}";
			unset($where_map['order']);
		}

		if(!empty($where_map['table']))
		{
			$table = $where_map['table'];
			unset($where_map['table']);
		}
			
		$where = array();
		foreach($where_map as $f => $v)
		{
			if(preg_match('!^\((\w+)\)(.+)$!', $f, $m))
				$where[] = $m[1] . $m[2] . $v;
			else
				$where[] = $f . '\'' . addslashes($v) . '\'';
		}
		
		if($where)
			$where = "WHERE ".join(' AND ', $where);
		else
			$where = "";
		
		return $this->get("SELECT $field FROM $table $where $order LIMIT 1");
	}

	function delete($table, $where)
	{
		$this->query("DELETE FROM `".addslashes($table)."` ".mysql_where_compile($where));
	}

	function select_array($table, $field, $where_map)
	{
		if($order = @$where_map['order'])
		{
			$order = "ORDER BY {$order}";
			unset($where_map['order']);
		}

		if($limit = @$where_map['limit'])
		{
			$limit = "LIMIT {$limit}";
			unset($where_map['limit']);
		}
		
		$where = array();
		foreach($where_map as $f => $v)
		{
			if(preg_match('!^\((\w+)\)(.+)$!', $f, $m))
				$where[] = $m[1] . $m[2] . $v;
			else
				$where[] = $f . '\'' . addslashes($v) . '\'';
		}

		return $this->get_array("SELECT $field FROM $table WHERE ".join(' AND ', $where)." $order $limit", false);
	}
}
