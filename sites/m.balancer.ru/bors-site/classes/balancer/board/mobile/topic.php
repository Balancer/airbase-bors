<?php

class balancer_board_mobile_topic extends balancer_board_topic
{
	function extends_class() { return 'forum_topic'; }

	function url() { return '/t'.$this->id(); }

	function parents() { return array($this->forum()->url()); }

	function auto_objects()
	{
		return array(
			'forum' => 'balancer_board_mobile_forum(forum_id)',
		);
	}

	function local_data()
	{
		return array(
			'posts' => objects_array('balancer_board_mobile_post', array(
				'topic_id' => $this->id(),
				'page' => $this->page(),
				'per_page' => $this->items_per_page(),
				'order' => 'create_time',
			)),
		);
	}

	function items_per_page() { return 10; }
}
