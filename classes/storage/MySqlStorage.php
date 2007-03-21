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

				$field_storage_method_name = "field_{$name}_storage";
				if(method_exists($object, $field_storage_method_name))
					$map = $object->$field_storage_method_name();
				else
					$map = @$mysql_map[$name];

				if(!preg_match("!^(\w+)\.(\w+).(\w+)\((\w+)\)$!", $map, $m))
					continue;
				
				list($dummy, $db, $table, $field, $id_field) = $m;

				$data[$db][$table][$id_field][$field] = $name;
		
//				echo "Load $name = $value\n";
			}

			$oid = addslashes($object->id());

			foreach($data as $db_name => $tables)
			{
				$dbh = &new DataBase($db_name);
				
				foreach($tables as $table_name => $ids)
				{
					foreach($ids as $id_field => $fields)
					{
						$result = $dbh->get("
							SELECT `".join("`,`", array_keys($fields))."` 
								FROM `$table_name` 
								WHERE `$id_field` = '$oid'", false);
					
						foreach($fields as $field => $name)
						{
							$set_method = "set_".$name;
							$object->$set_method(sizeof($fields) > 1 ? $result[$field] : $result, false);
						}
					}
				}
			}
			
//			$GLOBALS['log_level'] = 2;
		}

		function save($object)
		{
			return;
		
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
		
				if($loaded)
				{
					$value = $this->dbh->update("$db.$table","$id_field = '".addslashes($object->id())."'",
						array($db_field => $object->$field));
				}
				else // Если загрузки не было, то это новый объект.
				{
					
				}
			}

			$object->changed_fields = array();
		}
	}

	$mysql_map = array();
	
	$mysql_map['create_time']	= 'WWW.hts_data_create_time.value(id)';
	$mysql_map['name'] 			= 'WWW.hts_data_title.value(id)';

	function mysql_storage_map($class, $key, $map)
	{
		global $mysq_map;
		$mysql_map[$key] = $map;
	}
