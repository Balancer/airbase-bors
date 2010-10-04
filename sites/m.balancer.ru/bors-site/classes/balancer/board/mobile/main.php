<?php

class balancer_board_mobile_main extends balancer_board_mobile_page
{
	var $title_ec = "Форумы Balancer'а, мобильная версия";
	var $nav_name_ec = "мобильные форумы";
	function parents() { return array('/'); }

	function can_read() { return true; }

	function local_data()
	{
		return array(
			'categories' => objects_array('balancer_board_mobile_category', array(
				'(categories.parent IS NULL OR categories.parent = 0)',
				'inner_join' => 'balancer_board_forum ON balancer_board_forum.category_id = balancer_board_mobile_category.id',
				'group' => 'categories.id',
				'order' => 'SUM(num_posts) DESC',
			)),
		);
	}
}
