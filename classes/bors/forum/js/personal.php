<?php

class forum_js_personal extends base_js
{
	var $owner;

	function __construct($id)
	{
		parent::__construct($id);
		$this->owner = class_load('forum_user', $this->id());
	}

	function cache_static() { return 7*86400; }
		
	function url()
	{
		return "http://balancer.ru/user/".$this->id()."/personal.js";
	}

	function owner() { return $this->owner; }
}
