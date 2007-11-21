<?php
require_once('Bors.php');

class BorsBaseObject extends base_object
{
		var $id = NULL;
		var $initial_id = NULL;
		var $changed_fields = array();
		
//		var $methods_added = false;

		function id() { return $this->id; }
		function set_id($id) { $this->id = $id; }
		function main_db_storage(){ return ''; }
		function main_table_storage(){ return ''; }

		function new_instance() { exit("Try to get new empty instance"); }

		var $_loaded = false;
		function loaded() { return $this->_loaded; }

		function base_url() { return 'http://balancer.ru/'; }

		function uri($page = NULL)
		{
			return $this->url($page);
		}
		
		function url($page = NULL)
		{
			if(preg_match("!^http://!", $this->id()))
				return $this->id();
			
			if($page < 1)
				$page = $this->page();
			
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

		function save()
		{
			global $bors;
			
			$bors->config()->storage()->save($this);
		}
/*
		function addMethod($code)
		{
			$cname = uniqid("class");
			eval("class ${cname} { ${code} }");
			aggregate_methods($this, $cname);
		}

		function addField($name) 
		{
			$cname = uniqid("class");
			eval("class ${cname} { var \$$name; }");
			aggregate_methods($this, $cname);
		}
		
		function storage_register($var_name, $sql_field)
		{
			print_r($this);
//			$this->addField("stb_$var_name");
			$this->addMethod("function field_{$var_name}_storage() { return '$sql_field'; }");
		}
*/

		function cache_parents() { return array(); }

		function cache_clean()
		{
			global $cleaned;

			if(empty($cleaned))
				$cleaned = array();
				
			$this->cache_clean_self();
			foreach($this->cache_parents() as $parent_cache)
				if($parent_cache && empty($cleaned[$parent_cache->internal_uri()]))
				{
					$cleaned[$parent_cache->internal_uri()] = 1;
					$parent_cache->cache_clean();
				}
		}

		function cache_clean_self()
		{
			include_once('include/classes/cache/CacheStaticFile.php');
			CacheStaticFile::clean($this->internal_uri());
			CacheStaticFile::clean($this->url());
		}

		var $stb_owner_id = NULL;
		function set_owner_id($owner_id, $db_update) { $this->set("owner_id", $owner_id, $db_update); }
		function owner_id() { return $this->stb_owner_id; }

		function owner() { return class_load('forum_user', $this->owner_id()); }

		function preShowProcess() {	return false; }

		function cache_life_time() { return 0; }

		function cache_groups() { return ""; }

		function body()
		{
			if($body_engine = $this->body_engine())
			{
				$be = class_load($body_engine);
				return $be->body($this);
			}
			
			global $me;
		
			if($this->need_access_level() > 1 && $this->need_access_level() > $me->get("level"))
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
					class_load('cache_group', $group)->register($this);

			return $content;
		}
		
		function cacheable_body() { return ec("Содержимое страницы отсутствует"); }

		function need_access_level() { return 0; }
	
		function class_name() { return get_class($this); }
		function uri_name()   { return get_class($this); }

	function config_class() { return ''; }

	function storage_engine() { return ''; }
	function body_engine() { return ''; }

	function search_source() { return strip_tags($this->body()); }
	function auto_search_index() { return true; }
}
