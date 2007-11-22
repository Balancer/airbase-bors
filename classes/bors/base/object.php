<?php

require_once('classes/inc/names.php');
require_once('classes/inc/bors.php');

class base_object extends def_empty
{
	var $match;
	function set_match($match) { $this->match = $match;	}

	function parents() { return array("http://{$this->match[1]}{$this->match[2]}"); }

	function rss_body()
	{
		if($body = $this->description())
			return $this->lcml($body);
		
		if($body = $this->source())
			return $this->lcml($body);
		
		return $this->body();
	}

	function rss_title() { return $this->title(); }

	function __construct($id, $page=1)
	{
		parent::__construct($id, $page);
	
		if($storage_engine = $this->storage_engine())
			if(object_load($storage_engine)->load($this) !== false || $this->can_be_empty())
				$this->_loaded = true;
			
		if($data_provider = $this->data_provider())
			object_load($data_provider, $this)->fill();
	}

	function lcml($text)
	{
		if(!$text)
			return;
	
		$ch = &new Cache();
		if($ch->get('base_object-lcml', $text) && 0)
			return $ch->last();

		return $ch->set(lcml($text,
			array(
				'cr_type' => $this->cr_type(),
				'sharp_not_comment' => $this->sharp_not_comment(),
				'html_disable' => $this->html_disable(),
		)), 7*86400);
	}

	function sharp_not_comment() { return true; }
	function html_disable() { return true; }

	var $_class_id;
	function class_id()
	{
		if(empty($this->_class_id))
			$this->_class_id = class_name_to_id($this);

		return $this->_class_id;
	}

	function class_title() { return get_class($this); }

	static function add_template_data($var_name, $value) { return $GLOBALS['cms']['templates']['data'][$var_name] = $value; }
	
	static function template_data($var_name) { return @$GLOBALS['cms']['templates']['data'][$var_name]; }

	static function add_template_data_array($var_name, $value)
	{
		if(preg_match('!^(.+)\[(.+)\]$!', $var_name, $m))
			$GLOBALS['cms']['templates']['data'][$m[1]][$m[2]] = $value;
		else
			$GLOBALS['cms']['templates']['data'][$var_name][] = $value;
	}

	function __call($method, $params)
	{
		if(preg_match('!^autofield!', $method))
			return NULL;
//			debug_exit(ec("Неопределённый метод $method в классе ".get_class($this)));
	
		$field   = $method;
		$setting = false;
		if(preg_match('!^set_(\w+)$!', $method, $match))
		{
			$field   = $match[1];
			$setting = true;
		}

		if(preg_match('!^field_(\w+)_storage$!', $method, $match))
		{
			if($field = $this->autofield($match[1]))
				return $field;
			
			echo "<xmp>";
			debug_print_backtrace();
			echo "</xmp>";
			exit("__call[".__LINE__."]: undefined method '$method' for class '".get_class($this)."'");
		}
		
		$field_storage = "field_{$field}_storage";

		if(!method_exists($this, $field_storage) && !$this->autofield($field))
		{
			echo "<xmp>";
			debug_print_backtrace();
			echo "</xmp>";
			exit("__call[".__LINE__."]: undefined method '$method' for class '".get_class($this)."'");
		}

		if($setting)
			return $this->set($field, $params[0], $params[1]);
		else
			return $this->get_property($field);
	}

	function get_property($name)
	{
		if(property_exists($this, $p="stba_{$name}"))
			return $this->$p;

		if(property_exists($this, $p="stb_{$name}"))
			return $this->$p;
		
		debug_exit("Try to get undefined properties ".get_class($this).".$name");
	}

	function preParseProcess() { return false; }

	function set($field, $value, $db_update)
	{
//		echo "set ".get_class($this).".{$field} = $value<br/>\n";
		global $bors;
			
		$field_name = "stb_$field";
		if(!property_exists($this, $field_name))
			$field_name = "stba_$field";

		if($db_update && $this->$field_name != $value)
		{
			$this->changed_fields[$field] = $field_name;
			$bors->add_changed_object($this);
		}

		$this->$field_name = $value;
	}

	function render_engine() { return false; }
	function is_cache_disabled() { return true; }
	function template_vars() { return 'body source'; }
	function template_local_vars() { return 'create_time description id modify_time nav_name title'; }

	var $stb_create_time = NULL;
	function set_create_time($unix_time, $db_update) { $this->set("create_time", intval($unix_time), $db_update); }
	function create_time($exactly = false)
	{
		if($exactly || $this->stb_create_time)
			return $this->stb_create_time;

		if($this->stb_modify_time)
			return $this->stb_modify_time;

		return time(); 
	}

	var $stb_modify_time = NULL;
	function set_modify_time($unix_time, $db_update) { $this->set("modify_time", $unix_time, $db_update); }
	function modify_time($exactly = false)
	{
		if($exactly || $this->stb_modify_time)
			return $this->stb_modify_time;

		return time(); 
	}

	var $stb_title = '';
	function title() { return $this->stb_title; }
	function set_title($new_title, $db_update) { $this->set("title", $new_title, $db_update); }

	var $stb_description = NULL;
	function set_description($description, $db_update) { $this->set("description", $description, $db_update); }
	function description() { return $this->stb_description; }

	var $stb_nav_name = NULL;
	function set_nav_name($nav_name, $db_update) { $this->set("nav_name", $nav_name, $db_update); }
	function nav_name() { return !empty($this->stb_nav_name) ? $this->stb_nav_name : $this->title(); }

	var $stb_template = NULL;
	function set_template($template, $db_update) { $this->set("template", $template, $db_update); }
	function template() { return $this->stb_template ? $this->stb_template : @$GLOBALS['cms']['default_template']; }

	function cache_static() { return 0; }
	
	function titled_url() { return '<a href="'.$this->url($this->page())."\">{$this->title()}</a>"; }

	function set_fields($array, $db_update_flag, $fields_list = NULL)
	{
		if(!$this->id())
			$this->new_instance();
		
		if($fields_list)
		{
			foreach(split(' ', $fields_list) as $key)
			{
				$method = "set_$key";
				$this->$method(@$array[$key], $db_update_flag);
			}
		}
		else
		{
			foreach($array as $key => $val)
			{
				$method = "set_$key";
//				echo "Set $key to $val<br />";
				if(method_exists($this, $method) || $this->autofield($key))
					$this->$method($val, $db_update_flag);
			}
		}
		
		if($db_update_flag)
		{
			global $bors;
			$bors->changed_save();
		}
	}

	function data_provider() { return NULL; }

	var $_autofields;
	function autofield($field)
	{
		if(method_exists($this, $method = "field_{$field}_storage"))
			return $this->$method();

		if(empty($this->_autofields))
		{
			$_autofields = array();
		
			foreach(split(' ', $this->autofields()) as $f)
			{
				$id	  = 'id';
				if(preg_match('!^(\w+)\((\w+)\)(.*?)$!', $f, $match))
				{
					$f  = $match[1].$match[3];
					$id = $match[2];
				}

				$name = $f;
				if(preg_match('!^(\w+)\->(\w+)$!', $f, $match))
				{
					$f    = $match[1];
					$name = $match[2];
				}
				$this->_autofields[$name] = "{$f}({$id})";
			}
		}
		
		if($res = @$this->_autofields[$field])
			return $res;

		if(property_exists($this, $p = "stbf_{$field}"))
			return $this->$p;
			
		if(property_exists($this, "stba_{$field}"))
			return "{$field}(id)";

		return NULL;
	}
}
