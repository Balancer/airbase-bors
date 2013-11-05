<?php

class balancer_board_posts_list extends balancer_board_paginated
{
	function main_class() { return 'balancer_board_post'; }
	function order() { return 'create_time'; }

	function template()
	{
		template_jquery();
		return 'xfile:forum/_header.html';
	}

	function class_file() { return __FILE__; }
	function items_per_page() { return 25; }

	function access() { return $this; }
	function can_read() { template_noindex(); return bors()->user(); }

//	function where() { return array(); }

	function post_html($p)
	{
		return $p->html(array('strip' => 8192));
	}
}
