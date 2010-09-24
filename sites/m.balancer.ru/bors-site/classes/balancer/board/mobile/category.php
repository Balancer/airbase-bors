<?php

class balancer_board_mobile_category extends balancer_board_category
{
	function extends_class() { return 'forum_category'; }

	function url() { return '/c'.$this->id(); }

	function parents() { return array('/'); }

	function template() { return 'xfile:balancer/board/mobile/index.html'; }

	function local_data()
	{
		return array(
			'forums' => objects_array('balancer_board_mobile_forum', array(
				'category_id' => $this->id(),
				'order' => '-num_posts',
			)),
		);
	}
}
