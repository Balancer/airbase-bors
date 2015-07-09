<?php

class balancer_board_mobile_categories_view extends balancer_board_mobile_page
{
	function url() { return '/c'.$this->id(); }
	function parents() { return array('/'); }
	function title() { return $this->category()->title(); }

	function can_read() { return true; }

	function auto_objects()
	{
		return array('category' => 'balancer_board_mobile_category(id)');
	}

	function body_data()
	{
		return array(
			'forums' => bors_find_all('balancer_board_mobile_forum', array(
				'category_id' => $this->id(),
				'order' => '-num_posts',
			)),
		);
	}
}
