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

	function class_include($class_full_name)
	{
		if($class_name = @$GLOBALS['bors_data']['class_included'][$class_full_name])
			return $class_name;
	
		$class_path = "";
		$class_name = $class_full_name;

		if(preg_match("!^(.+/)([^/]+)$!", $class_full_name, $m))
		{
			$class_path = $m[1];
			$class_name = $m[2];
		}

		foreach(array(BORS_INCLUDE_LOCAL, BORS_INCLUDE.'/vhosts/'.$_SERVER['HTTP_HOST'], BORS_INCLUDE) as $dir)
			if(file_exists($file_name = "$dir/classes/bors/$class_path$class_name.php"))
			{
				include_once($file_name);
				return $GLOBALS['bors_data']['class_included'][$class_full_name] = $class_name;
			}

		return false;
	}

	function class_uri_load($class_full_name, $uri)
	{
		$class_name = class_include($class_full_name);
		
		if(!$class_name)
			return NULL;

		$id = call_user_func(array($class_name, 'uri_to_id'), $uri);
		
		$cls = @$GLOBALS['bors_data']['classes'][$class_full_name][$id];

		if(!$cls)
			$cls = &new $class_name($id);
			
		$GLOBALS['bors_data']['classes'][$class_full_name][$id][1] = $cls;
		$GLOBALS['bors_data']['classes_by_uri'][$cls->internal_uri()] = $cls;
		$GLOBALS['bors_data']['classes_by_uri'][$cls->uri()] = $cls;
		return $cls;
	}

	function class_load($class, $id=NULL, $page=1)
	{
		if(preg_match("!^(\d+)/$!", $id, $m))
			$id = $m[1];
	
//		echo "class_load('$class', '$id')<br />";
	
		if(preg_match("!^borspage!", $class, $m))
			return borsclass_uri_load($id, $page);

		if(preg_match("!^http://!", $class, $m))
			return borsclass_uri_load($class, $page);

		if(preg_match("!^([\w/]+)://(.*[^/])(/?)$!", $class, $m))
		{
			$class = $m[1];
			$id = $m[2];
		}

		if($id == NULL)
			list($class, $id) = split("-", $class);
	
		if(!$page)
			$page = 1;

		$class_path = $class;
		$cls = @$GLOBALS['bors_data']['classes'][$class_path][$id][$page];
		
		if(!$cls)
		{
			$path = "";
			if(preg_match("!(.+/)([^/]+)!", $class, $m))
			{
				$path = $m[1];
				$class = $m[2];
			}
			
			include_once("classes/bors/$path$class.php");
			if(class_exists($class))
				$cls = &new $class($id);

//			echo "classes/bors/$path$class.php<Br/>";
//			echo "path=$path, class=$class, obj=".$cls;

			if($cls)
			{
				$GLOBALS['bors_data']['classes'][$class_path][$id][$page] = $cls;
				if(method_exists($cls, 'uri'))
					$GLOBALS['bors_data']['classes_by_uri'][$cls->uri()] = $cls;
				if(method_exists($cls, 'internal_uri'))
					$GLOBALS['bors_data']['classes_by_uri'][$cls->internal_uri()] = $cls;
			}
		}

		if(!empty($cls) && $page > 1)
			$cls->set_page($page);
	
		return $cls;
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
				include_once("classes/bors/$class_path.php");
//					error_reporting($errrep_save);
				if(class_exists($class))
				{
//						echo "<b>Yes!</b><br />";
					$obj = &new $class($id);
					$obj->set_page($page);
					$GLOBALS['bors_data']['classes_by_uri'][$uri] = $obj;
					$GLOBALS['bors_data']['classes_by_uri'][$obj->internal_uri()] = $obj;
					$GLOBALS['bors_data']['classes'][$class_path][$id][$page] = $obj;
					return $obj;
				}
			}
		}

		return NULL;
	}
