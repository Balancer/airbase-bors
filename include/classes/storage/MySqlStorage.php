<?
	$_SERVER['DOCUMENT_ROOT'] = "/var/www/bal.aviaport.ru/htdocs";
	$_SERVER['HTTP_HOST'] = "bal.aviaport.ru";

	require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');

	class MySqlStorage
	{
		var $dbh;
		var $mysql_map;

		function MySqlStorage()
		{
			$this->dbh = &new DataBase();
		}

		function save($object)
		{
			
		}
		
		function load($object)
		{
			if(!$object->id())
				return;
		
			global $mysql_map;
		
			foreach(get_object_vars($object) as $field => $value)
			{
				if(preg_match('!^stb_(.+)$!', $field, $m))
					continue;
					
				$name = $m[1];

				$field_storage_method_name = "field_{$name}_storage";
				if(method_exists($object, $field_storage_method_name))
					$map = $object->$field_storage_method_name();
				else
					$map = @$mysql_map[$name];

				if(!preg_match("!^(\w+)\.(\w+).(\w+)\((\w+)\)$!", $map, $m))
					continue;
				
				list($dummy, $db, $table, $field, $id_field) = $m;
		
				$value = $this->dbh->get("SELECT `$field` FROM  `$db`.`$table` WHERE `$id_field` = '".addslashes($object->id())."'");
				
				$set_method = "set_$name";
		
				$object->$set_method($value);
//				echo "Load $name = $value\n";
			}
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
		
				if($loaded)
				{
					$value = $this->dbh->update(
						"$db.$table","$id_field = '".addslashes($object->id())."'",
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
