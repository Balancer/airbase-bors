<?php

class forum_user_js_setvars extends bors_js
{
/*
	top.me_can_move=1
	top.me_is_coordinator=1
*/

//	При переводе на статический форум сделать сброс по изменению свойств юзера. Или прав доступа. Разгрести.
//	function cache_static() { return config('static_forum') ? rand(3*86400, 7*86400) : 0; }

	function _access_engine_def() { return bors_access_public::class; }

	function cache_static() { return rand(300, 600); }

	function url() { return "/user/".$this->id()."/setvars.js"; }

	function user() { return bors_load('bors_user', $this->id()); }

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
