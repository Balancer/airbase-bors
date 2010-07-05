<?php

class balancer_board_posts_list extends base_page_paged
{
	function main_class() { return 'forum_post'; }
	function order() { return 'create_time'; }

	function template()
	{
		template_jquery();
		return 'templates/forum/_header.html';
	}

	function class_file() { return __FILE__; }
	function items_per_page() { return 25; }

	function access() { return $this; }
	function can_read() { template_noindex(); return bors()->user(); }
}
