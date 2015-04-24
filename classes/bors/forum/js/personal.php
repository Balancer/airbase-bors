<?php

class forum_js_personal extends bors_jsh
{
	var $owner;

	function __construct($id)
	{
		parent::__construct($id);
		$this->owner = bors_load('balancer_board_user', $this->id());
	}

	function cache_static() { return config('static_forum') ? rand(3600, 7200) : 0; }

	function url()
	{
		return "/user/".$this->id()."/personal.js";
	}

	function owner() { return $this->owner; }

	function body_data()
	{
		return array_merge(parent::body_data(), array(
			'owner' => $this->owner(),
		));
	}
}
