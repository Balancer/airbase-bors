<?php

class balancer_board_updated extends balancer_board_paginated
{
	function title() { return ec('Системные события'); }
	function is_auto_url_mapped_class() { return true; }
	function nav_name() { return ec('обновления'); }
	function template() { return 'forum/_header.html'; }

	//TODO: вернуть -modify_time после исправления преобразований, чтобы не было UNIX_TIMESTAMP(`modify_time`) ... ORDER BY modify_time
	function order() { return '`modify_time` DESC'; }

	function main_class() { return 'bal_event'; }
	function where()
	{
		return array_merge(parent::where(), array(
			'user_class_id' => 0,
			'user_id' => 0,
		));
	}

	function items_per_page() { return 25; }
//	function is_reversed() { return true; }
}
