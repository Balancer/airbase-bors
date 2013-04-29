<?php

class balancer_board_ajax_actions_unwatch extends bors_js
{
	function unwatch()
	{
		$v = bors_find_first('balancer_board_topics_visit', array(
			'user_id' => bors()->user_id(),
			'topic_id' => $this->arg('topic_id'),
		));

		if($v)
			$v->set_is_disabled(true);
	}
}
