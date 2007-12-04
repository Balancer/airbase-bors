<?php

class storage_db_mysql_smart
{
	function load(&$object, $common_where = '', $only_count = false)
	{
//		echo "Try load ".get_class($object)."({$object->id()})<br />\n";
		if(!($common_where || $only_count) && (!$object->id() || is_object($object->id())))
			return false;

		$oid = addslashes($object->id());
		
		$was_loaded = false;
		$result = array();

//		echo "MySqlStorage.load: <b>{$object->internal_uri()}</b>, size=".sizeof(get_object_vars($object))."; cnt=".(++$count)."<br />";

//		echo get_class($object); print_d($object->fields());

		foreach($object->fields() as $db => $tables)
		{
			$tab_count = 0;
			$select = array();
			$from = '';
			$where = $common_where;
			$first_name = '';
			$added = array();
			$main_id_name = '';

			$dbh = &new DataBase($db);

			foreach($tables as $table_name => $fields)
			{
			
				if(preg_match('!^(\w+)\((\w+)\)$!', $table_name, $m))
				{
					$table_name	= $m[1];
					$def_id		= $m[2];
				}
				else
					$def_id		= 'id';

				if(empty($main_id_name))
					$main_id_name = $def_id;

//				echo "<small>Do $table_name => ".print_r($fields, true)." with id = '{$def_id}'</small><br/>\n";

				foreach($fields as $property => $field)
				{
					if(is_numeric($property))
						$property = $field;
					
					// Выделяем имя функции постобработки, передаваемом в виде
					// 'WWW.News.Header(ID)|html_entity_decode($str)'
					// --------------------^^^^^^^^^^^^^^^^^^^^^^^^^-
					if(preg_match('!^(.+)\|(.+)$!', $field, $m))
					{
						$field		= $m[1];
						$php_func	= '|'.$m[2];
					}
					else
						$php_func 	= '';

//					echo "=== p: $field =|= $php_func ===</br>";

					// Выделяем имя SQL-функции, передаваемом в виде
					// 'UNIX_TIMESTAMP(WWW.News.Date(ID))
					// -^^^^^^^^^^^^^^^-----------------^
					$sql_func	= false;

					if(preg_match('!^(\w+) \( ([\w\.]+\(.+\)) \)$!x', $field, $m))
					{
						$field		= $m[2];
						$sql_func	= $m[1];
					}

					if(preg_match('!^(\w+) \( ([\w\.]+) \)$!x', $field, $m))
					{
						$field		= $m[2];
						$sql_func	= $m[1];
					}

//					echo "=== s: '$field' sf: $sql_func ===</br>";
				
					if(preg_match('!^(\w+)\(([^\(\)]+)\)$!x', $field, $m))
					{
						$id_field = $m[2];
						$field = $m[1];
					}
					else
						$id_field = $def_id;

					if(empty($added[$table_name.'-'.$id_field]))
					{
						$added[$table_name.'-'.$id_field] = true;
						
						$current_tab = "`tab".($tab_count++)."`";
						if(empty($from))
						{
							$from = 'FROM `'.$table_name.'` AS '.$current_tab;
							if(!$where && !$only_count)
								$where = 'WHERE '.make_id_field($current_tab, $id_field, $oid);
						}
						else
							$from .= ' LEFT JOIN `'.$table_name.'` AS '.$current_tab.' ON ('.make_id_field($current_tab, $id_field, $oid).')';
					}

					if($sql_func)
						$select[] = "{$sql_func}({$current_tab}.{$field}) AS `{$property}{$php_func}`";
					else
						$select[] = "{$current_tab}.".($field == $property ? $field : "{$field} AS `{$property}{$php_func}`");
				}
			}
			
			if($common_where)
				$select[] = "`tab0`.".($main_id_name != 'id' ? "`{$main_id_name}` as id" : 'id');

//			set_loglevel(10);
			if($only_count)
				return intval($dbh->get('SELECT COUNT(*) '.$from.' '.$where, false));
			else
				$dbh->query('SELECT '.join(',', $select).' '.$from.' '.$where, false);
//			set_loglevel(2);
				
			while($row = $dbh->fetch_row())
			{
				foreach($row as $name => $value)
				{
					if(preg_match('!^(.+)\|(.+)$!', $name, $m))
					{
						$name	= $m[1];
						$value = $this->do_func($m[2], $value);
					}
					
					$object->{"set_$name"}($value, false);

					$was_loaded = true;
				}

				if($common_where)
				{
					$result[] = $object;
					$class = get_class($object);
					$object = &new $class(NULL);
				}
			}

			return $common_where ? $result : $was_loaded;
		}
	}

	function do_func($func, $str)
	{
//		echo "Do $func('$str')";
		if(!$func)
			return $str;
		
		if(function_exists($func))
			return $func($str);
			
		$func = str_replace('$$$', '$str');
		eval("\$value = $func;");
		return $value;
	}

	function save($object)
	{
		global $back_functions;
		
//		echo "Save ".get_class($object)."({$object->id()})";
		
		if(!$object->id() || is_object($object->id()) || empty($object->changed_fields))
			return false;

		$oid = addslashes($object->id());
			
		foreach($object->fields() as $db => $tables)
		{
			$tab_count = 0;
			$set = array();
			$update = '';
			$where = '';
			$added = array();

			$dbh = &new DataBase($db);

			foreach($tables as $table_name => $fields)
			{
				if(preg_match('!^(\w+)\((\w+)\)$!', $table_name, $m))
				{
					$table_name	= $m[1];
					$def_id		= $m[2];
				}
				else
					$def_id		= 'id';

				foreach($fields as $property => $field)
				{
					if(is_numeric($property))
						$property = $field;

					if(empty($object->changed_fields[$property]))
						continue;

					$value = $object->$property();
					
					// Выделяем имя функции постобработки, передаваемом в виде
					// 'WWW.News.Header(ID)|html_entity_decode($str)'
					// --------------------^^^^^^^^^^^^^^^^^^^^^^^^^-
					if(preg_match('!^(.+)\|(.+)$!', $field, $m))
					{
						$field		= $m[1];
						$value	= $back_functions[$m[2]]($value);
					}

//					echo "=== p: $field =|= $php_func ===</br>";

					// Выделяем имя SQL-функции, передаваемом в виде
					// 'UNIX_TIMESTAMP(WWW.News.Date(ID))
					// -^^^^^^^^^^^^^^^-----------------^
					$sql_func	= false;
					
					if(preg_match('!^(\w+) \( ([\w\.]+\(.+\)) \)$!x', $field, $m))
					{
						$field		= $m[2];
						$sql_func	= $back_functions[$m[1]];
					}

					if(preg_match('!^(\w+) \( ([\w\.]+) \)$!x', $field, $m))
					{
						$field		= $m[2];
						$sql_func	= $back_functions[$m[1]];
					}

//					echo "=== s: $field sf: $sql_func ===</br>\n";
				
					if(preg_match('!^(\w+) \( ([^\(\)]+) \)$!x', $field, $m))
					{
						$id_field = $m[2];
						$field = $m[1];
					}
					else
						$id_field = $def_id;
						
					if(empty($added[$table_name.'-'.$id_field]))
					{
						$added[$table_name.'-'.$id_field] = true;
						
						$current_tab = "`tab".($tab_count++)."`";
						if(empty($update))
						{
							$update = 'UPDATE `'.$table_name.'` AS '.$current_tab;
							$where = 'WHERE '.make_id_field($current_tab, $id_field, $oid);
						}
						else
							$update .= ' LEFT JOIN `'.$table_name.'` AS '.$current_tab.' ON ('.make_id_field($current_tab, $id_field, $oid).')';
					}
				
					if($sql_func)
						$set["raw {$current_tab}.{$field}"] = "{$sql_func}('".addslashes($value)."')";
					else
						$set["{$current_tab}.{$field}"] = $value;
				}
			}
			$dbh->query($update.$dbh->make_string_set($set).' '.$where, false);
		}				
		$object->changed_fields = array();
//		exit();
	}

	function create($object, $data = array(), $replace = false)
	{
		global $back_functions;

		$oid = $object->id();
		
		foreach($object->fields() as $db => $tables)
		{
//			echo "Database: $db; tables="; print_r($tables); echo "<br />\n";
			$dbh = &new DataBase($db);

			$data = array();

			foreach($tables as $table_name => $fields)
			{
//				echo "Table: $table_name<br />\n";
				if(preg_match('!^(\w+)\((\w+)\)$!', $table_name, $m))
				{
					$table_name	= $m[1];
					$def_id		= $m[2];
				}
				else
					$def_id		= 'id';

				foreach($fields as $property => $field)
				{
					if(is_numeric($property))
						$property = $field;

					if(empty($object->changed_fields[$property]))
						continue;

					$value = isset($data[$property]) ? $data[$property] : $object->$property();
					
					// Выделяем имя функции постобработки, передаваемом в виде
					// 'WWW.News.Header(ID)|html_entity_decode($str)'
					// --------------------^^^^^^^^^^^^^^^^^^^^^^^^^-
					if(preg_match('!^(.+)\|(.+)$!', $field, $m))
					{
						$field		= $m[1];
						$value	= $back_functions[$m[2]]($value);
					}

//					echo "=== p: $field =|= $php_func ===</br>";

					// Выделяем имя SQL-функции, передаваемом в виде
					// 'UNIX_TIMESTAMP(WWW.News.Date(ID))
					// -^^^^^^^^^^^^^^^-----------------^
					$sql_func	= false;
					
					if(preg_match('!^(\w+) \( ([\w\.]+\(.+\)) \)$!x', $field, $m))
					{
						$field		= $m[2];
						$sql_func	= $back_functions[$m[1]];
					}

					if(preg_match('!^(\w+) \( ([\w\.]+) \)$!x', $field, $m))
					{
						$field		= $m[2];
						$sql_func	= $back_functions[$m[1]];
					}

//					echo "=== s: $field sf: $sql_func ===</br>";
				
					if(preg_match('!^(\w+) \( ([^\(\)]+) \)$!x', $field, $m))
					{
						$id_field = $m[2];
						$field = $m[1];
					}
					else
						$id_field = $def_id;

					$data[$table_name][$field] = $value;
				}						

				if($oid)
					$data[$table_name][$def_id] = $oid;

				if($replace)
					$dbh->replace($table_name, $data[$table_name]);
				else
					$dbh->insert($table_name, $data[$table_name]);
					
				if(empty($oid))
					$object->set_id($oid = $dbh->last_id());

			}
		}				
		$object->changed_fields = array();
	}
}

global $back_functions;
$back_functions = array(
	'html_entity_decode' => 'htmlspecialchars',
	'UNIX_TIMESTAMP' => 'FROM_UNIXTIME',
);
