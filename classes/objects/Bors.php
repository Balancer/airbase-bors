<?php
	require_once('classes/objects/Config.php');

	class Bors
	{
		var $config;
		var $changed_objects = array();
		
		function config()
		{
			return $this->config;
		}

		function __construct()
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
			include_once('engines/search.php');
		
			if(empty($this->changed_objects))
				return;
				
			foreach($this->changed_objects as $name => $obj)
			{
//				echo "<b>Update $name</b>, index={$obj->auto_search_index()}<br />\n";
				
//				if(!$obj->id())
//					$obj->new_instance();

				if(!$obj->id())
					continue;
//					debug_exit('emtpy id for changed object '.$obj->class_name());
				
				$obj->cache_clean();
				
				if($storage = $obj->storage_engine())
					$storage = object_load($storage);
				else
					$storage = $this->config()->storage();
				
//				echo "storage->save({$obj})<br />\n";
				$storage->save($obj);
				save_cached_object($obj);
					
				if(config('search_autoindex') && $obj->auto_search_index())
					bors_search_object_index($obj, 'replace');
			}
			
			$this->changed_objects = false;
		}
		
		function get_html($object)
		{
			require_once('funcs/templates/bors.php');
			$object->template_data_fill();
			return template_assign_bors_object($object);
		}
		
		function show($object)
		{
			echo $this->get_html($object);
		}

		var $_main_obj = NULL;
		function main_object() { return $this->_main_obj; }
		function set_main_object($obj)
		{
			if($this->_main_obj)
				debug_exit("Main obj ".$obj->internal_uri()." set error. Exists object ".$this->_main_obj->internal_uri());
			
			return $this->_main_obj = $obj; 
		}

		function real_uri($uri)
		{
			if(!preg_match("!^([\w/]+)://(.*[^/])(/?)$!", $uri, $m))
				return "";
			if($m[1] == 'http')
				return $uri;
				
			$cls = class_load($m[1], $m[2].(preg_match("!^\d+$!", $m[2]) ? '' : '/'));
			
			if(method_exists($cls, 'url'))
				return $cls->url();
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

		foreach(array(BORS_INCLUDE_LOCAL, BORS_INCLUDE.'/vhosts/'.@$_SERVER['HTTP_HOST'], BORS_INCLUDE, $local_path) as $dir)
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

	function __autoload($class_name) { class_include($class_name); }

function load_cached_object($class_name, $id, $args)
{
	if(is_object($id))
		return NULL;
			
//	if($class_name == 'forum_user' && $GLOBALS['me']->id == 10000) debug_trace();
		
//	if($GLOBALS['me']->id == 10000)  {	echo "Check load for <b>$class_name</b>('$id',".serialize($args)."<br />"; }
	if($obj = @$GLOBALS['bors_data']['cached_objects3'][$class_name][$id])
	{
//		if($GLOBALS['me']->id == 10000)  {	echo "Found <b>$class_name</b>('$id',".serialize($args)."<br />"; }
		if($obj->can_cached())
			return $obj;
	}
		
	if(config('memcached') && !is_object($id))
	{
		$memcache = &new Memcache;
		$memcache->connect(config('memcached')) or debug_exit("Could not connect memcache");
				
//		echo "got ".$class_name.'://'.$id.','.serialize($page)."<br />\n";

		if($x = @$memcache->get('bors_v17_'.$class_name.'://'.$id))
		{
//			echo "<b>got!</b><br />";
			if($x->can_cached())
				return $x;
		}
	}

	return NULL;
}

function delete_cached_object($object) { return save_cached_object($object, true); }

function save_cached_object(&$object, $delete = false)
{
	if(!method_exists($object, 'id') || is_object($object->id()))
		return;

	if(config('memcached') && $object->can_cached())
	{
		$memcache = &new Memcache;
		$memcache->connect(config('memcached')) or debug_exit("Could not connect memcache");
				
		$hash = 'bors_v17_'.get_class($object).'://'.$object->id();
			
		if($delete)
			@$memcache->delete($hash);
		else
			@$memcache->set($hash, $object, true, 600);

//			if(bors()->user()->id() == 10000) echo "memcahced [".get_class($object).'://'.$object->id().','.serialize($object->page())."]<br />";
	}

	if($delete)
		unset($GLOBALS['bors_data']['cached_objects3'][get_class($object)][$object->id()]);
	else
		$GLOBALS['bors_data']['cached_objects3'][get_class($object)][$object->id()] = $object;

//	if($GLOBALS['me']->id == 10000) echo "Save cache object <b>".get_class($object)."</b>('".$object->id()."', ".serialize($object->args()).")<br />\n";
}

	function class_internal_uri_load($uri)
	{
//		echo "Load internal uri '$uri'<br />\n";
	
		if(!preg_match("!^(\w+)://(.*)$!", $uri, $m))
			return NULL;
	
		$class_name = $m[1];

		$id = $m[2];
		$page = NULL;
		if(preg_match("!^(.+),(\d+)$!", $id, $m))
		{
			$id = $m[1];
			$page = $m[2];
		}

//		$id = call_user_func(array($class_name, 'uri_to_id'), $uri);

		return object_init($class_name, $id, array('page'=>$page));
	}

	function pure_class_load($class_name, $id, $args, $local_path = NULL)
	{
//		echo "Pure load {$class_name}({$id},".serialize($args).")<br />\n";
	
		if(is_string($id) && ($id == 'NULL'))
			$id = NULL;

		if(!class_include($class_name, $local_path))
			return NULL;

		if($obj = load_cached_object($class_name, $id, $args))
		{
		 	if(empty($args['no_load_cache']))
				return $obj;
			
			$obj->store();
//			$obj->cache_clean_self();
		}

		$obj = &new $class_name($id, $page);
		
		if($use_cache)
			save_cached_object($obj);
			
		return $obj;
	}

	function class_load($class, $id = NULL, $args=array())
	{
		if(preg_match("!^/!", $class))
			$class = 'http://'.$_SERVER['HTTP_HOST'].$class;
	
		if(!is_object($id) && preg_match("!^(\d+)/$!", $id, $m))
			$id = $m[1];
	
		if(preg_match("!^(\w+)://.+!", $class, $m))
		{
			if(preg_match("!^http://!", $class))
			{
				if(preg_match('!^(.+)#(.+)$!', $class, $m))
					$class = $m[1];
	
				if($obj = class_load_by_url($class))
					return $obj;

				if($obj = class_internal_uri_load($class))
					return $obj;
			}
			elseif($obj = class_internal_uri_load($class))
				return $obj;
		}

		if(preg_match("!^\w+$!", $class))
			return object_init($class, $id, $args);
		else
			return NULL;
	}

	function class_load_by_url($url)
	{
//		echo "Load $url<br />\n";
	
		if($obj = class_load_by_vhosts_url($url))
			return $obj;
		
		return class_load_by_local_url($url);
	}

	function class_load_by_local_url($url)
	{
		$obj = @$GLOBALS['bors_data']['classes_by_uri'][$url];
		if(!empty($obj))
			return $obj;

		if(empty($GLOBALS['bors_map']))
			return NULL;

		$url_data = parse_url($url);

//		print_d($GLOBALS['bors_map']);
		foreach($GLOBALS['bors_map'] as $pair)
		{
			if(!preg_match('!^(.*)\s*=>\s*(.+)$!', $pair, $match))
				exit(ec("Ошибка формата bors_map: {$pair}"));
			
			$url_pattern = trim($match[1]);
			$class_path  = trim($match[2]);

//			echo "Initial url=$url<br/>\n;";

			if(preg_match('!\\\?!', $url_pattern) && !preg_match('!\?!', $url) && !empty($_SERVER['QUERY_STRING']))
				$check_url = $url."?".$_SERVER['QUERY_STRING'];
			else
				$check_url = $url;
			
		
//			echo "Check $url_pattern to $url for $class_path as !^http://({$url_data['host']}){$url_pattern}\$! to {$check_url}<br />\n";
			if(preg_match("!^http://({$url_data['host']})$url_pattern$!", $check_url, $match))
			{
//				echo "<b>Ok - $class_path</b><br />"; exit();
				
				$id = NULL;
				$page = NULL;
				
				if(preg_match("!^redirect:(.+)$!", $class_path, $m))
				{
					$class_path = $m[1];
					$redirect = true;
				}
				else
					$redirect = false;
				
				// Формат вида aviaport_image_thumb(3,geometry=2)
				if(preg_match("!^(.+)\((\d+|NULL),([^)]+=[^)]+)\)$!", $class_path, $m))	
				{
					$args = array();
					foreach(split(',', $m[3]) as $pair)
						if(preg_match('!^(\w+)=(.+)$!', $pair, $mm))
							$args[$mm[1]] = $mm[2];

					$class_path = $m[1];
					$id = $match[$m[2]+1];
					
					$page = $args;
				}
				elseif(preg_match("!^(.+)\((\d+|NULL),(\d+)\)$!", $class_path, $m))	
				{
					$class_path = $m[1];
					$id = $match[$m[2]+1];
					$page = @$match[$m[3]+1];
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
						
				if($obj = object_init($class_path, $id, array('page' => $page, 'match'=>$match, 'called_url'=>$url)))
				{
					if($redirect)
					{
						echo "Redirect by $url_pattern";
						go($obj->url($page), true);
						exit("Redirect");
					}
					
					return $obj;
				}
				
			}
		}
//		exit();

		return NULL;
	}

	function class_load_by_vhosts_url($url)
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
			
//		print_d($data); print_d($bors_data['vhosts']);

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
			if(preg_match("!^http://(\Q{$data['host']}\E)$url_pattern$!", $check_url, $match))
			{
//				echo "Found: $class_path for  $check_url<br />";
			
				if(preg_match("!^redirect:(.+)$!", $class_path, $m))
				{
					$class_path = $m[1];
					$redirect = true;
				}
				else
					$redirect = false;
			
				$id = NULL;
				$page = 0;
				
				// Формат вида aviaport_image_thumb(3,geometry=2)
				if(preg_match("!^(.+) \( (\d+|NULL)( , [^)]+=[^)]+ )+ \)$!x", $class_path, $m))	
				{
					$args = array();
					foreach(split(',', $m[3]) as $pair)
<<<<<<< .mine
						if(preg_match('!^(\w+)=(.+)$!', $pair, $mm))
							$args[$mm[1]] = $match[$mm[2]+1];

=======
						if($pair)
							if(preg_match('!^(\w+)=(.+)$!', $pair, $mm))
								$args[$mm[1]] = $match[$mm[2]+1];
					
>>>>>>> .r597
					$class_path = $m[1];
					$id = $match[$m[2]+1];
					
					$page = $args;
				}
				if(preg_match("!^(.+)\((\d+|NULL),(\d+)\)$!", $class_path, $m))	
				{
					$class_path = $m[1];
					$id = $match[$m[2]+1];
					$page = intval(@$match[$m[3]+1]);
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

//				echo "$class_path($id) - $url";
						
				if($obj = object_init($class_path, $id, array(	
						'page' => $page,
						'use_cache' => true,
						'local_path' => $host_data['bors_local'],
						'match' => empty($match[2]) ? NULL : $match,
						'called_url' => $url,
					)))
				{
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

require_once('classes/inc/bors.php');
function object_init($class_name, $object_id, $args = array())
{
	if(!is_array($args))
		$args = $args ? array('page' => $args) : array();

	$obj = pure_class_load($class_name, $object_id, $args, $local_path = defval($args, 'local_path'));

	if(!$obj)
		return NULL;

<<<<<<< .mine
	if(is_array($object_page))
		$args = array_merge($args, $object_page);

	if($object_page)
		$obj->set_page($object_page);
=======
	if(method_exists($obj, 'set_page'))
		$obj->set_page(@$args['page']);
>>>>>>> .r597

	$use_cache = defval($args, 'use_cache', true);
	unset($args['local_path']);
	unset($args['use_cache']);
	unset($args['no_load_cache']);

	if(method_exists($obj, 'set_args'))
		$obj->set_args($args);
		
	if($m = defval($args, 'match'))
		$obj->set_match($m);

	if($url = defval($args, 'called_url'))
		$obj->set_called_url(preg_replace('!\?$!', '', $url));

	if(!$obj->loaded())
		$obj->init();
//	else echo get_class($obj)." already inited<br />";

	if($obj->is_only_tuner())
		return NULL;

	if(($object_id || $url)
		&& method_exists($obj, 'can_be_empty')
		&& !$obj->can_be_empty()
		&& !$obj->loaded() 
//		&& $obj->storage_engine() 
	)
		return NULL;

//	echo "{$class_name}($object_id) was loaded seccessfully} as ".get_class($obj)."<br />\n"; // exit();

	if($use_cache)
		save_cached_object($obj);
		
	return $obj;
}
