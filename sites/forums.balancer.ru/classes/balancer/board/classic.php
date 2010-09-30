<?php

class balancer_board_classic extends base_page
{
	function title() { return ec('Классический вид новых форумов'); }
	function nav_name() { return ec('классический вид'); }
	function parents() { return array('http://forums.balancer.ru/'); }
	function is_auto_url_mapped_class() { return true; }
	function template() { return 'forum/_header.html'; }

	function local_data()
	{
		return array(
			'categories' => objects_array('balancer_board_category', array(
				'parent_category_id' => 0,
				'order' => 'sort_order',
				'skip_common' => 0,
			)),
		);
	}
}
