<?php

class balancer_board_forums_persons extends balancer_board_forums_view
{
	static function id_prepare() { return 79; }

	function body_data()
	{
//		var_dump($this->items_per_page());
		return parent::body_data();
	}
}
