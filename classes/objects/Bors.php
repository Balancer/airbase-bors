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
			$this->changed_objects[$obj->internal_uri()] = $obj;
		}
		
		function changed_save()
		{
			if(empty($this->changed_objects))
				return;
				
			foreach($this->changed_objects as $name => $obj)
			{
//				echo "<b>Update $name</b><br />";
			
				if(!$obj->id())
					$obj->new_instance();
				
				$obj->cache_clean();
				
				$this->config()->storage()->save($obj);
			}
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

	function class_include($class_name, $local_path = "")
	{
		if(!empty($GLOBALS['bors_data']['class_included'][$class_name]))
			return true;
	
		$class_path = "";
		$class_file = $class_name;


		if(preg_match("!^(.+/)([^/]+)$!", str_replace("_", "/", $class_name), $m))
		{
			$class_path = $m[1];
			$class_file = $m[2];
		}

		foreach(array(BORS_INCLUDE_LOCAL, BORS_INCLUDE.'/vhosts/'.$_SERVER['HTTP_HOST'], BORS_INCLUDE, $local_path) as $dir)
		{
			if(file_exists($file_name = "$dir/classes/$class_path$class_file.php"))
			{
				require_once($file_name);
				$GLOBALS['bors_data']['class_included'][$class_name] = true;
				return true;
			}
			
			if(file_exists($file_name = "$dir/classes/bors/$class_path$class_file.php"))
			{
				require_once($file_name);
				$GLOBALS['bors_data']['class_included'][$class_name] = true;
				return true;
			}
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
		if(method_exists($object, 'id'))
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

	function pure_class_load($class_name, $id, $page, $use_cache = true, $local_path = NULL)
	{
//		echo "Pure load {$class_name}({$id})<br />\n";
	
		if(!class_include($class_name, $local_path))
			return NULL;

		if($use_cache && $obj = load_cached_object($class_name, $id, $page))
			return $obj;

		$obj = &new $class_name($id);
//		echo "Pure load {$class_name}({$id}), loaded={$obj->loaded()}, storage engine = {$obj->storage_engine()}<br />\n";
		if($id 
				&& !$obj->loaded() 
				&& $obj->storage_engine() 
				&& method_exists($obj, 'can_be_empty') 
				&& !$obj->can_be_empty())
			return NULL;
		
		if($page > 1)
			$obj->set_page($page);

		if($use_cache)
			save_cached_object($obj);
			
		return $obj;
	}

	function class_load($class, $id = NULL, $page=1, $use_http = true)
	{
		if(preg_match("!^/!", $class))
			$class = 'http://'.$_SERVER['HTTP_HOST'].$class;
	
		if(preg_match("!^(\d+)/$!", $id, $m))
			$id = $m[1];
	
//		echo "class_load('$class', '$id')<br />";
	
		if(preg_match("!^(\w+)://.+!", $class, $m))
		{
			if(preg_match("!^http://!", $class))
			{
				if($obj = class_load_by_url($class, $page))
					return $obj;

				if($use_http && $obj = class_internal_uri_load($class))
					return $obj;
			}
			elseif($obj = class_internal_uri_load($class))
				return $obj;
		}
	
		if(preg_match("!^\w+$!", $class))
			return pure_class_load($class, $id, $page);
		else
			return NULL;
	}

	function class_load_by_url($url, $page=1)
	{
		if($obj = class_load_by_vhosts_url($url, $page))
			return $obj;
		
		return class_load_by_local_url($url, $page);
	}

	function class_load_by_local_url($url, $page)
	{
		$obj = @$GLOBALS['bors_data']['classes_by_uri'][$url];
		if(!empty($obj))
			return $obj;
			
		if(empty($GLOBALS['bors_map']))
			return NULL;

		foreach($GLOBALS['bors_map'] as $pair)
		{
			if(!preg_match('!^(.*)\s*=>\s*(.+)$!', $pair, $match))
				exit(ec("Ошибка формата bors_map: {$pair}"));
			
			$url_pattern = trim($match[1]);
			$class_path  = trim($match[2]);

			if(preg_match("!\\\\\?!", $url_pattern))
				$check_url = $url."?".$_SERVER['QUERY_STRING'];
			else
				$check_url = $url;
		
//			echo "Check $url_pattern to $url for $class_path -- !^http://({$_SERVER['HTTP_HOST']}){$url_pattern}\$!<br />\n";
			if(preg_match("!^http://({$_SERVER['HTTP_HOST']})$url_pattern$!", $check_url, $match))
			{
				$id = $url;
				$page = 1;
				
				if(preg_match("!^redirect:(.+)$!", $class_path, $m))
				{
					$class_path = $m[1];
					$redirect = true;
				}
				else
					$redirect = false;
				
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
						
//				echo "Try load {$class_path}('{$id}')<br />\n";
			
				if($obj = pure_class_load($class_path, $id, $page))
				{
					$obj->set_match($match);
					
					if($redirect)
					{
						go($obj->url($page), true);
						exit("Redirect");
					}
					
					return $obj;
				}
				
			}
		}

		return NULL;
	}

	function class_load_by_vhosts_url($url, $page)
	{
		$data = parse_url($url);
		
		if(empty($data['host']))
		{
			debug(ec("Ошибка. Попытка загрузить класс из URL неверного формата: ").$url, 1);
			return NULL;
		}
		
		global $bors_data;
		
		$obj = @$bors_data['classes_by_uri'][$url];
		if(!empty($obj))
			return $obj;
			
//		print_r($bors_data['vhosts']);

		if(empty($bors_data['vhosts'][$data['host']]))
			return NULL;

		$host_data = $bors_data['vhosts'][$data['host']];
		
		foreach($host_data['bors_map'] as $pair)
		{
			if(!preg_match('!^(.*)\s*=>\s*(.+)$!', $pair, $match))
				exit(ec("Ошибка формата bors_map[{$data['host']}]: {$pair}"));
			
			$url_pattern = trim($match[1]);
			$class_path  = trim($match[2]);

			if(preg_match("!\\\\\?!", $url_pattern))
				$check_url = $url."?".$_SERVER['QUERY_STRING'];
			else
				$check_url = $url;

//			echo "Check vhost $url_pattern to $url for $class_path -- !^http://({$_SERVER['HTTP_HOST']}){$url_pattern}\$!<br />\n";
			if(preg_match("!^http://({$data['host']})$url_pattern$!", $check_url, $match))
			{
				if(preg_match("!^redirect:(.+)$!", $class_path, $m))
				{
					$class_path = $m[1];
					$redirect = true;
				}
				else
					$redirect = false;
			
				$id = $url;
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

//				echo "$class_path($id)";
						
				if($obj = pure_class_load($class_path, $id, $page, true, $host_data['bors_local']))
				{
					$obj->set_match($match);
					$bors_data['classes_by_uri'][$url] = $obj;
					
					if($redirect)
					{
						go($obj->url($page), true);
						exit("Redirect");
					}
					
					return $obj;
				}
			}
		}

		return NULL;
	}
