<?php

class forum_js_personal extends base_js
{
	var $owner;

	function __construct($id)
	{
		parent::__construct($id);
		$this->owner = class_load('balancer_board_user', $this->id());
	}

	function cache_static() { return rand(3600, 7200); }

	function url()
	{
		return "/user/".$this->id()."/personal.js";
	}

	function owner() { return $this->owner; }

	function template_local_vars() { return parent::template_local_vars().' owner'; }
}
