<?php

class balancer_board_users_attaches extends balancer_board_paged
{
	function can_be_empty() { return false; }
	function loaded() { return (bool) $this->user(); }

	function main_class() { return 'balancer_board_attach'; }
	function where() { return array(
		'owner_id' => $this->id(),
	); }

	function order() { return '-post_id'; }

	function items_per_page() { return 20; }

	function title()    { return 'Все файлы пользователя '.$this->user()->title(); }
	function nav_name() { return 'все файлы'; }
	function parents()  { return array($this->user()->url()); }

	function auto_objects()
	{
		return array(
			'user' => 'balancer_board_user(id)',
		);
	}
}
