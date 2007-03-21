<?
	require_once('Bors.php');

	class BorsBaseObject
	{
		var $id = NULL;
		var $initial_id = NULL;
		var $methods_added = false;
		function id() { return $this->id; }
		function set_id($id) { $this->id = $id; }

		function BorsBaseObject($id = NULL, $noload = false)
		{
			// Если не указан ID, но нет признака отсутствия загрузки, то создаётся новый объект в БД.

			$this->id = $this->initial_id = $id;
	
			if(!$this->methods_added)
			{
				foreach(get_object_vars($this) as $field => $value)
				{
					if(substr($field, 0, 4) != 'stb_')
						continue;
					
					$name = substr($field, 4);
					
					if(method_exists($this, $name))
						continue;

					$this->addMethod("function $name() { return \$this->$field; }");
					$this->addMethod("function set_$name(\$value, \$db_update = true) { \$this-set(\"\$value\", \$value, \$db_update); }");
				}
			}
			
			if($noload)
				return;
			
			$this->load();
		}

		var $changed_fields = array();
		function set($field, $value, $db_update = true)
		{
			$field_name = "stb_$field";

			if($db_update && $this->$field_name != $value)
				$changed_fields[$field] = $field_name;

			$this->$field_name = $value;
		}

		var $stb_create_time = NULL;
		function set_create_time($unix_time, $db_update=true) { $this->set("create_time", $unix_time, $db_update); }
		function create_time()
		{
			if($this->stb_create_time)
				return $this->stb_create_time;
			if($this->stb_modify_time)
				return $this->stb_modify_time;
			return time(); 
		}

		var $stb_modify_time = NULL;
		function set_modify_time($unix_time, $db_update=true) { $this->set("modify_time", $unix_time, $db_update); }
		function modify_time()
		{
			if($this->stb_modify_time)
				return $this->stb_modify_time;

			return time(); 
		}

		var $stb_title = '';
		function title() { return $this->stb_title; }
		function set_title($new_title, $db_update=true) { $this->set("title", $new_title, $db_update); }

		var $page = '';
		function page() { return $this->stb_page; }
		function set_page($page) { $this->set("page", $page, false); }

		function uri() 
		{ 
			require_once("funcs/modules/uri.php");
			return strftime("/%Y/%m/%d/", $this->modify_time()).$this->type()."-".$this->id()."/".translite_uri_simple($this->title()).".html"; 
		}

		function parents() { return array(); }

		var $loaded = false;
		function load()
		{
			global $bors;
			
			$bors->config()->storage()->load($this);
			$loaded = true;
		}

		function save()
		{
			global $bors;
			
			$bors->config()->storage()->save($this);
		}

		function addMethod(  $code ) 
		{
			$cname = uniqid("class");
			eval( "class ${cname} { ${code} }" );        
			aggregate_methods( $this , $cname );
		}

		function addField($name) 
		{
			$cname = uniqid("class");
			eval( "class ${cname} { var \$$name; }" );        
			aggregate_properties( $this , $cname );
		}
		
		function storage_register($var_name, $sql_field)
		{
			$this->addField("stb_$var_name");
			$this->addMethods("function field_$name_storage() { return '$sql_field'; }");
		}
	}
