<?
	require_once('classes/objects/Config.php');

	class Bors
	{
		var $config;
		var $changed_objects = array();
		
		function config()
		{
			return $this->config;
		}

		function Bors()
		{
			require_once('classes/objects/Config.php');
			$this->config = &new Config();
		}
		
		function add_changed_object($obj)
		{
			$this->changed_objects[$obj->uri_name()."-".$obj->id()] = $obj;
		}
		
		function changed_save()
		{
			foreach($this->changed_objects as $name => $obj)
				$this->config()->storage()->save($obj);
		}
		
		function get_html($object)
		{
			require_once('funcs/templates/bors.php');
			return template_assign_bors_object($object);
		}
		
		function show($object)
		{
			echo $this->get_html($object);
		}

		var $_main_obj = NULL;
		function main_object() { return $this->_main_obj; }
		function set_main_object($obj) { if($this->_main_obj) exit("Main obj ".$obj->interal_uri()." set error. Exists object ".$this->_main_obj->internal_uri()); $this->_main_obj = $obj; }

		function real_uri($uri)
		{
			if(!preg_match("!^([\w/]+)://(.*[^/])(/?)$!", $uri, $m))
				return "";
			if($m[1] == 'http')
				return $uri;
				
			$cls = class_load($m[1], $m[2].(preg_match("!^\d+$!", $m[2]) ? '' : '/'));
			
			if(method_exists($cls, 'uri'))
				return $cls->uri();
			else
				return $uri;
		}
	}
	
	$GLOBALS['bors'] = &new Bors();

	function class_include($class_name)
	{
//		echo "Include class '$class_name'<br />\n";
	
		if(!empty($GLOBALS['bors_data']['class_included'][$class_name]))
			return true;
	
		$class_path = "";
		$class_file = $class_name;

		if(preg_match("!^(.+/)([^/]+)$!", str_replace("_", "/", $class_name), $m))
		{
			$class_path = $m[1];
			$class_file = $m[2];
		}

		foreach(array(BORS_INCLUDE_LOCAL, BORS_INCLUDE.'/vhosts/'.$_SERVER['HTTP_HOST'], BORS_INCLUDE) as $dir)
			if(file_exists($file_name = "$dir/classes/bors/$class_path$class_file.php"))
			{
				require_once($file_name);
				$GLOBALS['bors_data']['class_included'][$class_name] = true;
				return true;
			}

		return false;
	}

	function load_cached_object($class_name, $id, $page)
	{
//		echo "Check load for $class_name('$id',$page)<br />";
		return @$GLOBALS['bors_data']['cached_objects'][$class_name][$id][$page];
	}

	function save_cached_object($object)
	{
//		echo "Save cache object ".get_class($object)."'(".$object->id()."', ".$object->page().")<br />\n";
		$GLOBALS['bors_data']['cached_objects'][get_class($object)][$object->id()][$object->page()] = $object;
	}

	function class_internal_uri_load($uri)
	{
//		echo "Load internal uri '$uri'<br />\n";
	
		if(!preg_match("!^(\w+)://(.*)$!", $uri, $m))
			return NULL;
	
		$class_name = $m[1];

		$id = $m[2];
		$page = 1;
		if(preg_match("!^(.+),(\d+)$!", $id, $m))
		{
			$id = $m[1];
			$page = $m[2];
		}

//		$id = call_user_func(array($class_name, 'uri_to_id'), $uri);

		return pure_class_load($class_name, $id, $page);
	}

	function pure_class_load($class_name, $id, $page)
	{
		if(!class_include($class_name))
			return NULL;

		if($obj = load_cached_object($class_name, $id, $page))
			return $obj;
		
		$obj = &new $class_name($id);
		
		if($page > 1)
			$obj->set_page($page);

		save_cached_object($obj);
			
		return $obj;
	}

	function class_load($class, $id = NULL, $page=1)
	{
		if(preg_match("!^/!", $class))
			$class = 'http://'.$_SERVER['HTTP_HOST'].$class;
	
		if(preg_match("!^(\d+)/$!", $id, $m))
			$id = $m[1];
	
//		echo "class_load('$class', '$id')<br />";
	
		if(preg_match("!^(\w+)://.+!", $class, $m))
		{
			if(preg_match("!^http://!", $class))
				if($obj = borsclass_uri_load($class, $page))
					return $obj;

			if($obj = class_internal_uri_load($class))
				return $obj;
		}
	
		return pure_class_load($class, $id, $page);
	}

	function borsclass_uri_load($uri, $page=1)
	{
//		echo "borsclass_uri_load($uri)<br />";
	
//		print_r($GLOBALS['bors_map']);

		$obj = @$GLOBALS['bors_data']['classes_by_uri'][$uri];
		if(!empty($obj))
			return $obj;
			
		if(empty($GLOBALS['bors_map']))
			return NULL;

		foreach($GLOBALS['bors_map'] as $uri_pattern => $class_path)
		{
//			echo "Check $uri_pattern to $uri <br />";
			if(preg_match("!^http://({$_SERVER['HTTP_HOST']})$uri_pattern$!", $uri, $match))
			{
				$id = $uri;
				$page = 1;
				
				if(preg_match("!^(.+)\((\d+),(\d+)\)$!", $class_path, $m))	
				{
					$class_path = $m[1];
					$id = $match[$m[2]+1];
					$page = max(@$match[$m[3]+1], 1);
				}
				elseif(preg_match("!^(.+)\((\d+)\)$!", $class_path, $class_match))	
				{
					$class_path = $class_match[1];
					$id = $match[$class_match[2]+1];
				}

				if(preg_match("!^(.+)/([^/]+)$!", $class_path, $m))
					$class = $m[2];
				else
					$class = $class_path;
						
//					echo "Class=$class, path=$class_path, id=$id, page=$page";
					
//					echo "<b>true to $class (in $class_path)</b><br />";
//					$errrep_save = error_reporting();
//					error_reporting($errrep_save & ~E_NOTICE);
				
				$obj = pure_class_load($class_path, $id, $page);
				if($obj)
					$obj->set_match($match);
				return $obj;
			}
		}

		return NULL;
	}
