<?
class_include('def_empty');

class def_object extends def_empty
{
		var $changed_fields = array();
		var $match;
		
		function set_match($match) { $this->match = $match;	}

		function main_db_storage(){ return ''; }
		function main_table_storage(){ return ''; }

		function new_instance() { exit("Try to get new empty instance"); }

		function __construct($id = NULL, $noload = false)
		{
			parent::__construct($id);
		
			if($noload || !$this->id)
				return;
			
			$this->load();
		}

		function set($field, $value, $db_update)
		{
			global $bors;
			
			$field_name = "stb_$field";

			if($db_update && $this->$field_name != $value)
			{
				$this->changed_fields[$field] = $field_name;
				$bors->add_changed_object($this);
			}

			$this->$field_name = $value;
		}


		var $stb_create_time = NULL;
		function set_create_time($unix_time, $db_update) { $this->set("create_time", intval($unix_time), $db_update); }
		function create_time()
		{
			if($this->stb_create_time)
				return $this->stb_create_time;

			if($this->stb_modify_time)
				return $this->stb_modify_time;

			return time(); 
		}

		var $stb_modify_time = NULL;
		function set_modify_time($unix_time, $db_update) { $this->set("modify_time", $unix_time, $db_update); }
		function modify_time()
		{
			if($this->stb_modify_time)
				return $this->stb_modify_time;

			return time(); 
		}

		var $stb_title = '';
		function title() { return $this->stb_title/* ? $this->stb_title : $this->internal_uri()*/; }
		function set_title($new_title, $db_update) { $this->set("title", $new_title, $db_update); }

		function base_url() { return "http://{$_SERVER['HTTP_HOST']}/"; }

		//TODO: Устарело, после убирания вызовов - нафиг.
		function uri($page = 1)
		{
			return $this->url($page);
		}
		
		function url($page = 1)
		{
			if($page < 1)
				$page = $this->page();
		
			if(preg_match("!^http://!", $this->id()))
				return $this->id();
				
			require_once("funcs/modules/uri.php");
			$uri = $this->base_url().strftime("%Y/%m/%d/", $this->modify_time());
			$uri .= $this->uri_name()."-".$this->id();

			if($page > 1)
				$uri .= ",$page";

			$uri .= "--".translite_uri_simple($this->title()).".html"; 
			return $uri;
		}

		function internal_uri()
		{
			if(preg_match("!^http://!", $this->id()))
				return $this->id();

			return  $this->class_name().'://'.$this->id().'/'; 
		}

		function parents() { return array(); }
		
		function parent()
		{
//			echo "Parent for ".$this->internal_uri()." is ";
			$res = array();
			foreach($this->parents() as $x)
			{
				if($obj = class_load($x[0], $x[1]))
					$res[] = $obj->internal_uri();
//				print_r($x);
			}
			
			return $res;
		}

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

		function cache_parents() { return array(); }

		function cache_clean()
		{
			global $cleaned;

			if(empty($cleaned))
				$cleaned = array();
				
			$this->cache_clean_self();
			foreach($this->cache_parents() as $parent_cache)
				if(empty($cleaned[$parent_cache->internal_uri()]))
				{
					$cleaned[$parent_cache->internal_uri()] = 1;
					$parent_cache->cache_clean();
				}
		}

		function cache_clean_self()
		{
			include_once('include/classes/cache/CacheStaticFile.php');
			CacheStaticFile::clean($this->internal_uri());
		}

		function template_vars()
		{
			return 'body source';
		}

		function template_local_vars()
		{
			return 'create_time description id modify_time nav_name title';
		}
		
		function is_cache_disabled() { return true; }

		var $stb_description = NULL;
		function set_description($description, $db_update) { $this->set("description", $description, $db_update); }
		function description() { return $this->stb_description; }

		var $stb_nav_name = NULL;
		function set_nav_name($nav_name, $db_update) { $this->set("nav_name", $nav_name, $db_update); }
		function nav_name() { return !empty($this->stb_nav_name) ? $this->stb_nav_name : $this->title(); }

		var $stb_source = NULL;
		function set_source($source, $db_update) { $this->set("source", $source, $db_update); }
		function source() { return $this->stb_source; }

		var $stb_template = NULL;
		function set_template($template, $db_update) { $this->set("template", $template, $db_update); }
        function template() { return $this->stb_template ? $this->stb_template : @$GLOBALS['cms']['default_template']; }

		var $stb_owner_id = NULL;
		function set_owner_id($owner_id, $db_update) { $this->set("owner_id", $owner_id, $db_update); }
		function owner_id() { return $this->stb_owner_id; }

		function owner() { return class_load('forum_user', $this->owner_id()); }

		var $stb_cr_type = NULL;
		function set_cr_type($cr_type, $db_update) { $this->set("cr_type", $cr_type, $db_update); }
		function cr_type() { return $this->stb_cr_type; }
	
		var $stb_level = NULL;
		function set_level($level, $db_update) { $this->set("level", $level, $db_update); }
		function level() { return $this->stb_level; }

		function preShowProcess() {	return false; }

		function cache_life_time() { return 0; }

		function cache_groups() { return ""; }

		function body()
		{
			global $me;
		
			if($this->need_access_level() > $me->get("level"))
			{
				require_once("funcs/modules/messages.php");
				return error_message(ec("У Вас недостаточный уровень доступа для этой страницы. Ваш уровень ").$me->get("level").ec(", требуется ").$this->need_access_level());
			}
			
			if(!$this->cache_life_time())
				return $this->cacheable_body();
			
			$ch = &new Cache();
			
			$drop_cache = $this->cache_life_time() || !empty($_GET['drop_cache']);
			
			if($ch->get('bors-cached-body-v17', $this->internal_uri()) && !$drop_cache)
			{
				$add = "\n<!-- cached; create=".strftime("%d.%m.%Y %H:%M", $ch->create_time)."; expire=".strftime("%d.%m.%Y %H:%M", $ch->expire_time)." -->";
				return $ch->last().$add;
			}

			$content = $ch->set($this->cacheable_body(), $this->cache_life_time());

			// Зарегистрируем сохранённый кеш в группах кеша, чтобы можно было чистить
			// при обновлении данных, от которых зависит наш контент
			
			foreach(split(' ', $this->cache_groups()) as $group)
				if($group)
					$ch->group_register($group, $this);

			return $content;
		}
		
		function cacheable_body() { return ec("Содержимое страницы отсутствует"); }
		function cache_static() { return 0; }

		var $stb_type_id;
		function type_id() { return $this->stb_type_id; }
		function set_type_id($type_id, $db_update) { $this->set("type_id", $type_id, $db_update); }

		function need_access_level() { return 0; }
	
		function class_name() { return get_class($this); }
		function uri_name()   { return get_class($this); }

		function preParseProcess()
		{
			return false;
		}
		
	function set_fields($array, $db_update_flag)
	{
		if(!$this->id())
			$this->new_instance();
		
		foreach($array as $key => $val)
		{
			$method = "set_$key";
//			echo "Set $key to $val<br />";
			if(method_exists($this, $method))
				$this->$method($val, $db_update_flag);
		}

		if($db_update_flag)
		{
			global $bors;
			$bors->changed_save();
		}
	}

	function config_class() { return ''; }
}