<?php

class MySqlStorage extends def_empty
{
		var $dbhs;
		var $mysql_map;

		function MySqlStorage()
		{
		}

		function load($object)
		{
//			echo "Try load ".get_class($object)."({$object->id()})<br />\n";
			if(!$object->id() || is_object($object->id()))
				return false;
		
			$was_loaded = false;
			global $mysql_map;

//			echo "MySqlStorage.load: <b>{$object->internal_uri()}</b>, size=".sizeof(get_object_vars($object))."; cnt=".(++$count)."<br />";

			$def_db = $object->main_db_storage();

			if(!$def_db)
				$def_db = $GLOBALS['cms']['mysql_database'];

			$def_table = $object->main_table_storage();

			global $MySqlStorage_data_cache;

			$fields = array();

			if(method_exists($object, 'fields_first')  && $field_names = $object->fields_first())
			{
				foreach(split(' ', $field_names) as $var_name)
				{
					unset($fields[$var_name]);
					$fields[$var_name] = $object->$var_name;
				}
			}
			
			$fields = array_merge($fields, get_object_vars($object));
		
			$hash = md5(serialize($fields));
			if(!($data = @$MySqlStorage_data_cache[$hash]))
			{
			  $data    = array();
			  foreach($fields as $field => $value)
			  {
//				echo "--- load $field<br />\n";
				if(!preg_match('!^stba?_(.+)$!', $field, $m))
					continue;
					
				$name = $m[1];

//				$stb_total++;

//				echo get_class($object)."->$name<br />\n";

				$field_storage_method_name = "field_{$name}_storage";
				
//				echo "field_storage: {$field_storage_method_name} at ".get_class($object)." check</br>\n";
				
				if(!method_exists($object, $field_storage_method_name) && !$object->autofield($name))
					continue;

//				echo "field_storage: {$field_storage_method_name} ok!</br>\n";

				$map = $object->$field_storage_method_name();
//				else
//					$map = @$mysql_map[$name];

//				echo "=== got $map ===</br>";

				// Выделяем имя функции постобработки, передаваемом в виде
				// 'WWW.News.Header(ID)|html_entity_decode($str)'
				// --------------------^^^^^^^^^^^^^^^^^^^^^^^^^-
				$pfunc = "";
				if(preg_match("!^(.+)\|(.+)$!", $map, $m))
				{
					$map	= $m[1];
					$pfunc	= $m[2];
				}

//				echo "=== p: $map ===</br>";

				// Выделяем имя SQL-функции, передаваемом в виде
				// 'UNIX_TIMESTAMP(WWW.News.Date(ID))
				// -^^^^^^^^^^^^^^^-----------------^
				$sfunc = "";
				if(preg_match("!^(\w+)\((.+\(.+\))\)$!", $map, $m))
				{
					$map	= $m[2];
					$sfunc	= $m[1];
				}

//				echo "=== s: $map ===</br>";
				
				$db		= '';
				$table	= '';
//				echo "$map </br>";
				if(preg_match("!^(\w+)\.(\w+)\.((\w+)\(([^\(\)]+)\))$!", $map, $m))
				{
					$db		= $m[1];
					$table	= $m[2];
					$map	= $m[3];
				}

				if(preg_match("!^(\w+)\.((\w+)\(([^\(\)]+)\))$!", $map, $m))
				{
					$table	= $m[1];
					$map	= $m[2];
				}

//				echo $map."</br>";

				if(!preg_match("!^(\w+)\(([^\(\)]+)\)$!", $map, $m))
					continue;
					
				list($dummy, $field, $id_field) = $m;

				if($pfunc)
					$name = "$name|$pfunc";

				if($sfunc)
					$field = "$sfunc($field)";

				if($db == '')
					$db = $def_db;
					
				if($table == '')
					$table = $def_table;

				if($table == '')
					$table = $object->main_table_storage($name);

				$data[$db][$table][$id_field][$field] = $name;
			  }
			  ksort($data);
			  $MySqlStorage_data_cache[$hash] = $data;
			}

			$oid = addslashes($object->id());

//			echo "<xmp>"; print_r(serialize($data)); echo "</xmp>";

			global $MySqlStorage_queries_cache;
			foreach($data as $db_name => $tables)
			{
//				if(!$db_name)
//					$db_name = $def_db;

				$dbh = &new DataBase($db_name);
				
				$hash = md5(serialize($tables));
				$x = @$MySqlStorage_queries_cache[$db_name][$hash];
				if(!$x)
				{					
//					ksort($tables);
//				echo "<xmp>"; print_r($tables); echo "</xmp>";
				
					$tab_count = 0;
					$select = array();
					$from = "";
					$where = "";
					$first_name = "";
					$added = array();
				
					foreach($tables as $table_name => $ids)
					{
//						if(!$table_name)
//							exit($table_name = $def_table);

						if(!$table_name)
							continue;
						
						foreach($ids as $id_field => $field_list)
						{
							if(empty($added[$table_name.'-'.$id_field]))
							{
								$added[$table_name.'-'.$id_field] = 1;
							
								$current_tab = "`t".($tab_count++)."`";
								if(empty($from))
								{
									$from = 'FROM `'.$table_name.'` AS '.$current_tab;
									$where = 'WHERE '.make_id_field($current_tab, $id_field);
								}
								else
									$from .= ' LEFT JOIN `'.$table_name.'` AS '.$current_tab.' ON ('.make_id_field($current_tab, $id_field).')';
							}

							foreach($field_list as $field => $name)
							{
								if(preg_match('!^(\w+)\((\w+)\)$!', $field, $m))
									$select[] = $m[1].'('.$current_tab.'.'.$m[2].') AS `'.$name.'`';
								else
									$select[] = $current_tab.'.'.($field == $name ? $field : $field.' AS `'.$name.'`');

								$first_name = $name;
							}
						}
					}
					
					$query = 'SELECT '.join(',', $select).' '.$from.' '.$where;
					$MySqlStorage_queries_cache[$db_name][$hash] = array($query, $first_name);
//					echo "New: <b>$query</b><br />";
				}
				else
				{
					list($query, $first_name) = $x;
//					echo "Cached: <b>$query</b><br />";
				}
//				$GLOBALS['log_level'] = 10;
//				if(empty($select))
//					return;
					
				$result = $dbh->get(str_replace('%MySqlStorageOID%', $oid, $query), false);
//				$GLOBALS['log_level'] = 2;
//				echo "res = "; print_r($result); echo "<br />";
					
				if(count($result) > 1)
				{
					foreach($result as $name => $value)
					{
						if(preg_match('!^(.+)\|(.+)$!', $name, $m))
						{
							$name	= $m[1];
							$value = $this->do_func($m[2], $value);
						}
						$set_method = 'set_'.$name;
						$object->$set_method($value, false);
						if($value)
							$was_loaded = true;
					}
				}
				else
				{
					if(preg_match('!^(.+)\|(.+)$!', $first_name, $m))
					{
						$first_name	= $m[1];
						$result = $this->do_func($m[2], $result);
					}
					$set_method = 'set_'.$first_name;
					$object->$set_method($result, false);
					if($result)
						$was_loaded = true;
				}
			}
			
//			echo "<b>{$object->url()} loaded = {$was_loaded}</b><br />";
			return $was_loaded;
		}

		function do_func($func, $str)
		{
//			echo "Do $func('$str')";
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
			global $mysql_map;
		
			foreach($object->changed_fields as $field_name => $field)
			{
				$field_storage_method_name = "field_{$field_name}_storage";
				$map = @$mysql_map[$field_name];
				if(method_exists($object, $field_storage_method_name))
					$map = $object->$field_storage_method_name();
				else // if(method_exists($object, 'autofield') && $x = $object->autofield($field_name))
					$map = $object->autofield($field_name);

				$db = $object->main_db_storage();
				if(!$db)
					$db = $GLOBALS['cms']['mysql_database'];
								   
				$table = $object->main_table_storage();
//				echo "$map </br>";
				if(preg_match("!^(\w+)\.(\w+)\.((\w+)\(([^\(\)]+)\))$!", $map, $m))
				{
					$db		= $m[1];
					$table	= $m[2];
					$map	= $m[3];
				}

				if(preg_match("!^(\w+)\.((\w+)\(([^\(\)]+)\))$!", $map, $m))
				{
					$table	= $m[1];
					$map	= $m[2];
				}

				global $back_functions;
				$back_func = NULL;
				if(preg_match("!^(.+)\|(.+$)!", $map, $m)) // Name(ID)|html_entity_decode
				{
					$map = $m[1];
					$back_func = $back_functions[$m[2]];
				}

				$mysql_func = NULL;
				if(preg_match("!^(\w+)\((\w+\(\w+\))\)$!", $map, $m)) // Name(ID)|html_entity_decode
				{
					$map = $m[2];
					$mysql_func = $back_functions[$m[1]];
				}

//				echo $map."</br>";

				if(!preg_match("!^(\w+)\(([^\(\)]+)\)$!", $map, $m))
					continue;
					
				list($dummy, $db_field, $id_field) = $m;
		
				$dbh = &new DataBase($db);
				
				$store = array();
				$value = $back_func ? $back_func($object->$field) : $object->$field;
				if($mysql_func)
				{
					$key = $db_field;
					$dbh->normkeyval($key, $value);
					$db_field = "raw $db_field";
					$value = "$mysql_func($value)";
				}
				
				if($object->id())
				{
					$dbh->update($table, make_id_field($table, $id_field, $object->id()), array($db_field => $value));
				}
				else
				{
					$dbh->insert($table, array($db_field => $value));
					
					$object->set_id($dbh->get_last_id());
//					echo "Set id to ".$dbh->get_last_id()."<br />";
				}
			}

			$object->changed_fields = array();
		}
	}

	$mysql_map = array();
	
//	$mysql_map['create_time']	= 'hts_data_create_time.value(id)';
//	$mysql_map['name'] 			= 'hts_data_title.value(id)';

	function mysql_storage_map($class, $key, $map)
	{
		global $mysq_map;
		$mysql_map[$key] = $map;
	}

	function make_id_field($table, $id_field, $oid = '%MySqlStorageOID%')
	{
		if(strpos($id_field, '=') === false)
			return "$table.$id_field = '".addslashes($oid)."'";

		if(strpos($id_field, ' ') === false)
		{
			$out =  preg_replace("!(\w+)=(\w+)!", "$table.$1=$2", $id_field);
			$out =  preg_replace("!(\w+)='(\w+)'!", "$table.$1='$2'", $out);
		}
		else
		{
			$out =  preg_replace("!%TABLE%!", $table, $id_field);
			$out =  preg_replace("!%ID%!", addslashes($oid), $out);
		}
		
		return $out;
	}

	global $back_functions;
	$back_functions = array(
		'html_entity_decode' => 'htmlspecialchars',
		'UNIX_TIMESTAMP' => 'FROM_UNIXTIME',
	);

