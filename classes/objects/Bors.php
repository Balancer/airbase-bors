<?
	require_once('Config.php');

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
			$this->config = &new Config();
		}
		
		function add_changed_object($obj)
		{
			$this->changed_objects[$obj->type()."-".$obj->id()] = $obj;
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
			if(!preg_match("!^(\w+)://(.*[^/])/?$!", $uri, $m))
				return "";
			if($m[1] == 'http')
				return $uri;
			return class_load($m[1], $m[2])->uri();
		}
	}
	
	$GLOBALS['bors'] = &new Bors();

	function class_load($class, $id=NULL, $page=1)
	{
		if($id == NULL)
			list($class, $id) = split("-", $class);
	
		if(empty($GLOBALS['bors_data']['classes'][$class][$id]))
		{
			$path = "";
			if(preg_match("!(.+/)([^/]+)!", $class, $m))
			{
				$path = $m[1];
				$class = $m[2];
			}
			
			$class_name = "BorsClass".ucfirst($class);
			require_once("classes/objects/$path$class_name.php");
			$GLOBALS['bors_data']['classes'][$class][$id] = &new $class_name($id);
		}

		if(!$page)
			$page = 1;

		$GLOBALS['bors_data']['classes'][$class][$id]->set_page($page);

		return $GLOBALS['bors_data']['classes'][$class][$id];
	}
