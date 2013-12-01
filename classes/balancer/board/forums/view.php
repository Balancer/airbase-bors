<?php

class balancer_board_forums_view extends balancer_board_paginated
{
	var $main_class = 'balancer_board_topic';
	var $order = '-modify_time';
	function items_per_page() { return 50; }

	function forum() { return bors_load('balancer_board_forum', $this->id()); }
	function title() { return $this->forum()->title(); }
	function nav_name() { return $this->forum()->nav_name(); }

	function parents() { return $this->forum()->parents(); }

	function order() { return '`sticky` DESC, `last_post` DESC'; }

	function where()
	{
		return array(
			'num_replies>=' => 0,
			'forum_id' => $this->id(),
		);
	}
}
