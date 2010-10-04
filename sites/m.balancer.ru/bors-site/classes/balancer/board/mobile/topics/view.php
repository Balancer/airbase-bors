<?php

class balancer_board_mobile_topics_view extends balancer_board_mobile_page
{
	function url($page = NULL) { return '/t'.$this->id().($page > 1 ? ".$page" : ""); }
	function title() { return $this->topic()->title(); }

	function can_read() { return $this->topic()->is_public(); }

	function parents() { return array($this->topic()->forum()->url()); }

	function auto_objects()
	{
		return array(
			'topic' => 'balancer_board_mobile_topic(id)',
		);
	}
}
