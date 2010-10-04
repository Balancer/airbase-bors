<?php

class balancer_board_mobile_post extends balancer_board_post
{
	function extends_class() { return 'forum_post'; }

	function url() { return '/p'.$this->id(); }
	function topic() { return bors_load('balancer_board_mobile_topic', $this->topic_id()); }

	function previous_post()
	{
		return bors_find_first('balancer_board_mobile_post', array(
			'topic_id' => $this->topic_id(),
			'id<>' => $this->id(),
			'(create_time <= '.intval($this->create_time()).' AND sort_order <= '.intval($this->sort_order()).')',
			'order' => '-sort_order,-create_time',
		));
	}

	function next_post()
	{
		return bors_find_first('balancer_board_mobile_post', array(
			'topic_id' => $this->topic_id(),
			'id<>' => $this->id(),
			'(create_time >= '.intval($this->create_time()).' AND sort_order >= '.intval($this->sort_order()).')',
			'order' => 'sort_order,create_time',
		));
	}
}
