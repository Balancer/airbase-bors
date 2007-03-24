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

				if(!preg_match("!^(\w+)\.(\w+).(\w+)\(([^\(\)]+)\)$!", $map, $m))
					continue;
				
				list($dummy, $db, $table, $field, $id_field) = $m;

				$data[$db][$table][$id_field][$field] = $name;
			}

			$oid = addslashes($object->id());

//			print_r($data); echo "<br />";

			foreach($data as $db_name => $tables)
			{
				$dbh = &new DataBase($db_name);
				
				$tab_count = 0;
				$select = array();
				$from = "";
				$where = "";
				$first_name = "";
				$added = array();
				
				foreach($tables as $table_name => $ids)
				{
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
							$select[] = $current_tab.".`$field` AS `$name`";
							$first_name = $name;
						}
					}
					
					$result = $dbh->get("SELECT ".join(",", $select)." $from $where", false);
					
					if(is_array($result))
					{
						foreach($result as $name => $value)
						{
							$set_method = "set_".$name;
							$object->$set_method($value, false);
						}
					}
					else
					{
						$set_method = "set_".$first_name;
						$object->$set_method($result, false);
					}
					
				}
			}
			
//			$GLOBALS['log_level'] = 2;
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
	
	$mysql_map['create_time']	= 'WWW.hts_data_create_time.value(id)';
	$mysql_map['name'] 			= 'WWW.hts_data_title.value(id)';

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
