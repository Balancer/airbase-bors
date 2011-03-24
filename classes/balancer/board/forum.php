<?php

class balancer_board_forum extends forum_forum
{
	function extends_class() { return 'forum_forum'; }

	function last_topics($limit)
	{
		return bors_find_all('balancer_board_topic', array(
			'forum_id' => $this->id(),
			'order' => '-last_post_create_time',
			'limit' => $limit,
		));
	}
}
