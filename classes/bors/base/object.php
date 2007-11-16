<?php

require_once('classes/inc/names.php');

class_include('def_empty');

class base_object extends def_empty
{
	function rss_body()
	{
		if($body = $this->description())
			return $this->lcml($body);
		
		if($body = $this->source())
			return $this->lcml($body);
		
		return $this->body();
	}

	function rss_title() { return $this->title(); }

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

	var $stb_cr_type = NULL;
	function set_cr_type($cr_type, $db_update) { $this->set("cr_type", $cr_type, $db_update); }
	function cr_type() { return $this->stb_cr_type ? $this->stb_cr_type : 'save_cr'; }

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
}
