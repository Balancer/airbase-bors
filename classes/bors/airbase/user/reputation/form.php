<?php

class airbase_user_reputation_form extends base_page
{
	private $user;

	function init() { $this->user = object_load('balancer_board_user', $this->id()); return false; }
	function title() { return ec('Изменение репутации пользователя ').$this->user()->title(); }

	function loaded() { return $this->user != NULL; }

	function user() { return $this->user; }

//	function url() { return '/user/'.$this->id().'/reputation/form/'; }
}
