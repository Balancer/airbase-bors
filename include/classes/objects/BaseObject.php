<?
	class BaseObject
	{
		var $id = NULL;
		var $initial_id = NULL;
		function id() { return $this->id; }
		function set_id($id) { $this->id = $id; }

		function BaseObject($id = NULL, $noload = false)
		{
			$this->id = $this->initial_id = $id;
			if($noload)
				return;
			
			$this->load();
		}

		var $changed_fields = array();
		function set($field, $value)
		{
			$field_name = "stb_$field";
			$$field_name = $value;
			$changed_fields[$field] = $field_name;
		}

		var $stb_create_time = NULL;
		function create_time() { return $this->stb_create_time; }
		function set_create_time($unix_time) { $this-set("create_time", $unix_time); }

		var $stb_name = '';
		function name() { return $this->stb_name; }
		function set_name($new_name) { $this->set("name", $new_name); }

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
	}
