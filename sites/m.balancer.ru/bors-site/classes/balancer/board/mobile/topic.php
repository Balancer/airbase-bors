<?php

class balancer_board_mobile_topic extends balancer_board_topic
{
	function extends_class_name() { return 'forum_post'; }

	function url($page = NULL) { return '/t'.$this->id().($page > 1 ? ".$page" : ""); }

	function parents() { return array($this->forum()->url()); }

	function forum() { return bors_load('balancer_board_mobile_forum', $this->forum_id()); }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
//			'forum' => 'balancer_board_mobile_forum(forum_id)',
			'first_post' => 'balancer_board_mobile_post(first_post_id)',
			'last_post' => 'balancer_board_mobile_post(last_post_id)',
			'new_post' => 'balancer_board_mobile_post(new_post_id)',
		));
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
}
