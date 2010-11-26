<?php

class forum_user_js_setvars extends base_jss
{
	function cache_static() { return config('static_forum') ? rand(3*86400, 7*86400) : 0; }

	function url() { return "/user/".$this->id()."/setvars.js"; }

	function user() { return object_load('bors_user', $this->id()); }

	function local_template_data_set()
	{
		return array(
			'me' => $this->user() ? $this->user() : NULL,
		);
	}
}
