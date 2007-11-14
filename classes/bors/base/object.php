<?php

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
}
