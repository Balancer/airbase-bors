<?php

class balancer_board_users_own extends balancer_board_paged
{
	function main_class() { return 'balancer_board_topic'; }
	function where() { return array(
		'owner_id' => $this->id(),
	); }

	function title()    { return 'Все темы, созданные пользователем '.$this->user()->title(); }
	function nav_name() { return 'все темы'; }
	function parents()  { return array($this->user()->url()); }

	function can_be_empty() { return false; }
	function loaded()
	{
		return (bool) $this->user();
	}

	function auto_objects()
	{
		return array(
			'user' => 'balancer_board_user(id)',
		);
	}
}
