<?php

class forum_user_js_setvars extends bors_js
{
	function cache_static() { return config('static_forum') ? rand(3*86400, 7*86400) : 0; }

	function url() { return "/user/".$this->id()."/setvars.js"; }

	function user() { return object_load('bors_user', $this->id()); }

	function body_data()
	{
		if($profile_hash = @$_COOKIE['client_profile_hash'])
			$profile = bors_find_first('balancer_board_user_client_profile', array('cookie_hash' => $profile_hash));
		else
			$profile = false;

		return array(
			'me' => $this->user() ? $this->user() : NULL,
			'profile' => $profile,
		);
	}
}
