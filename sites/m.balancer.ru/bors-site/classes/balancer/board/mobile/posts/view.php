<?php

class balancer_board_mobile_posts_view extends balancer_board_mobile_page
{
	function title() { return $this->post()->nav_name(); }
	function topic_id() { return $this->post()->topic_id(); }

	function parents() { return array($this->topic()); }

	function can_read() { return $this->topic()->is_public(); }

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_mobile_post(id)',
			'topic' => 'balancer_board_mobile_topic(topic_id)',
		);
	}

	function body_data()
	{
		return array(
//			'first' => $this->topic()->first_post(),
			'prev' => $this->post()->previous_post(),
			'next' => $this->post()->next_post(),
//			'last' => $this->topic()->last_post(),
		);
	}
}
