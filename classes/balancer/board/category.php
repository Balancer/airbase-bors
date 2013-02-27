<?php

class balancer_board_category extends forum_category
{
	function extends_class_name() { return 'forum_category'; }

	function full_name($cats = NULL)
	{
		$result = array();
		$current_cat = $this;
		do {
			$result[] = $current_cat->nav_name();
			if($parent = $current_cat->parent_category_id())
				$current_cat = $cats ? $cats[$parent] : bors_load('balancer_board_category', $parent);
		} while($parent);

		return join(' « ', $result);
	}
}
