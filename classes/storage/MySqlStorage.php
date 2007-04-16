<?
//	$_SERVER['DOCUMENT_ROOT'] = "/var/www/bal.aviaport.ru/htdocs";
//	$_SERVER['HTTP_HOST'] = "bal.aviaport.ru";

//	require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');

	class MySqlStorage
	{
		var $dbhs;
		var $mysql_map;

		function MySqlStorage()
		{
		}

		function load($object)
		{
			if(!$object->id())
				return;
		
			global $mysql_map;

//			$GLOBALS['log_level'] = 10;

			$data    = array();
		
			foreach(get_object_vars($object) as $field => $value)
			{
				if(substr($field, 0, 4) != 'stb_')
					continue;
					
				$name = substr($field, 4);
//				echo "--- load $name<br />";

				$field_storage_method_name = "field_{$name}_storage";
				if(method_exists($object, $field_storage_method_name))
					$map = $object->$field_storage_method_name();
				else
					$map = @$mysql_map[$name];

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
				$def_db = $object->main_db_storage();
				if(!$def_db)
					$def_db = $GLOBALS['cms']['mysql_database'];
								   
				$table	= '';
				$def_table = $object->main_table_storage();
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

				if($db == $def_db) $db = '';
				if($table == $def_table) $table = '';

				$data[$db][$table][$id_field][$field] = $name;
			}

			$oid = addslashes($object->id());

			ksort($data);
//			echo "<xmp>"; print_r($data); echo "</xmp>";
			
			foreach($data as $db_name => $tables)
			{
				if(!$db_name)
					$db_name = $def_db;
					
				ksort($tables);
//				echo "<xmp>"; print_r($tables); echo "</xmp>";
				$dbh = &new DataBase($db_name);
				
				$tab_count = 0;
				$select = array();
				$from = "";
				$where = "";
				$first_name = "";
				$added = array();
				
				foreach($tables as $table_name => $ids)
				{
					if(!$table_name)
						$table_name = $def_table;
						
					foreach($ids as $id_field => $field_list)
					{
						if(empty($added["$table_name($id_field)"]))
						{
							$current_tab = "`t".($tab_count++)."`";
							if(empty($from))
							{
								$from = "FROM `$table_name` AS $current_tab";
								$where = "WHERE ".make_id_field($current_tab, $id_field, $object->id());
							}
							else
								$from .= " LEFT JOIN `$table_name` AS $current_tab ON (".make_id_field($current_tab, $id_field, $object->id()).")";
						}

						foreach($field_list as $field => $name)
						{
							if(preg_match("!^(\w+)\((\w+)\)$!", $field, $m))
								$select[] = "{$m[1]}($current_tab.{$m[2]}) AS `$name`";
							else
								$select[] = $current_tab.".$field AS `$name`";

							$first_name = $name;
						}
					}
				}

//				$GLOBALS['log_level'] = 10;
				$result = $dbh->get("SELECT ".join(",", $select)." $from $where", false);
//				$GLOBALS['log_level'] = 2;
//				echo "res = "; print_r($result); echo "<br />";
					
				if(is_array($result))
				{
					foreach($result as $name => $value)
					{
						$pfunc = "";
						if(preg_match("!^(.+)\|(.+)$!", $name, $m))
						{
							$pfunc	= $m[2];
							$name	= $m[1];
							$value = $this->do_func($pfunc, $value);
						}
						$set_method = "set_".$name;
						$object->$set_method($value, false);
					}
				}
				else
				{
					$pfunc = "";
					if(preg_match("!^(.+)\|(.+)$!", $first_name, $m))
					{
						$pfunc	= $m[2];
						$first_name	= $m[1];
						$result = $this->do_func($pfunc, $result);
					}
					$set_method = "set_".$first_name;
					$object->$set_method($result, false);
				}
			}
			
//			$GLOBALS['log_level'] = 2;
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
			if(!$object->id())
				return;
		
			global $mysql_map;
		
			foreach($object->changed_fields as $field_name => $field)
			{
				$field_storage_method_name = "field_{$field_name}_storage";
				if(method_exists($object, $field_storage_method_name))
					$map = $object->$field_storage_method_name();
				else
					$map = @$mysql_map[$field_name];

				if(!preg_match("!^(\w+)\.(\w+).(\w+)\((\w+)\)$!", $map, $m))
					continue;
				
				list($dummy, $db, $table, $db_field, $id_field) = $m;
		
				$dbh = &new DataBase($db);
				$dbh->store($table, make_id_field($table, $id_field, $object->id()),
						array(
							$id_field => $object->id(),
							$db_field => $object->$field,
						));
			}

			$object->changed_fields = array();
		}
	}

	$mysql_map = array();
	
	$mysql_map['create_time']	= 'hts_data_create_time.value(id)';
	$mysql_map['name'] 			= 'hts_data_title.value(id)';

	function mysql_storage_map($class, $key, $map)
	{
		global $mysq_map;
		$mysql_map[$key] = $map;
	}

	function make_id_field($table, $id_field, $oid)
	{
		if(strpos($id_field, '=') === false)
			return "$table.$id_field = '".addslashes($oid)."'";
		$out =  preg_replace("!(\w+)=(\w+)!", "$table.$1=$2", $id_field);
		$out =  preg_replace("!(\w+)='(\w+)'!", "$table.$1='$2'", $out);
		return $out;
	}
