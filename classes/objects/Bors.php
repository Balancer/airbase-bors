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
	
		$iclass = $class;
		$cls = @$GLOBALS['bors_data']['classes'][$class][$id];
		
		if(!$cls)
		{
			$path = "";
			if(preg_match("!(.+/)([^/]+)!", $class, $m))
			{
				$path = $m[1];
				$class = $m[2];
			}
			
			$class_name = "BorsClass".ucfirst($class);
			@include_once("classes/objects/$path$class_name.php");
			if(class_exists($class_name))
			{
				$cls = &new $class_name($id);
			}
			else
			{			
				@include_once("classes/bors/$path$class.php");
				if(class_exists($class))
					$cls = &new $class($id);
			}
			if($cls)
			{
				$GLOBALS['bors_data']['classes'][$iclass][$id] = $cls;
				$GLOBALS['bors_data']['borsclasses'][$cls->internal_uri()] = $cls;
				$GLOBALS['bors_data']['borsclasses'][$cls->uri()] = $cls;
			}
		}

		if(!$page)
			$page = 1;

		if(!empty($GLOBALS['bors_data']['classes'][$iclass][$id]))
			$GLOBALS['bors_data']['classes'][$iclass][$id]->set_page($page);
	
		return $cls;
	}

	function borsclass_uri_load($uri, $page=1)
	{
//		echo "borsclass_uri_load($uri)<br />";
	
//		print_r($GLOBALS['bors_map']);

		if(empty($GLOBALS['bors_data']['borsclasses'][$uri]) && !empty($GLOBALS['bors_map']))
		{
			foreach($GLOBALS['bors_map'] as $uri_pattern => $class_path)
			{
//				echo "Check $uri_pattern to $uri <br />";
				if(preg_match("!^http://({$_SERVER['HTTP_HOST']})$uri_pattern$!", $uri, $match))
				{
					if(preg_match("!^(.+)/([^/]+)$!", $class_path, $m))
						$class = $m[2];
					else
						$class = $class_path;
//					echo "<b>true to $class (in $class_path)</b><br />";
//					$errrep_save = error_reporting();
//					error_reporting($errrep_save & ~E_NOTICE);
					include_once("classes/bors/$class_path.php");
//					error_reporting($errrep_save);
					if(class_exists($class))
					{
//						echo "<b>Yes!</b><br />";
						$cls = &new $class($uri, $match);
						$GLOBALS['bors_data']['borsclasses'][$uri] = $cls;
						$GLOBALS['bors_data']['classes'][$class_path][$uri] = $cls;
//						echo "::".$GLOBALS['bors_data']['borsclasses'][$uri]->title()." -> ".$GLOBALS['bors_data']['borsclasses'][$uri]->uri()."<br />";
						break;
					}
				}
			}
		}

		if(empty($GLOBALS['bors_data']['borsclasses'][$uri]))
			return NULL;

		if($page < 2)
			$page = 1;

		$GLOBALS['bors_data']['borsclasses'][$uri]->set_page($page);

		return $GLOBALS['bors_data']['borsclasses'][$uri];
	}
