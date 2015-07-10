<?php

class balancer_board_js_comments extends base_js
{
	function body_data()
	{
		$topic = bors_load('balancer_board_topic', $this->id());
//		$total_posts = bors_count('forum_post', array('topic_id' => $this->id()));
		$last_posts = bors_find_all('balancer_board_post', array(
			'topic_id' => $this->id(),
			'order' => '-create_time',
			'limit' => 10,
			'id<>' => $topic ? $topic->first_post_id() : NULL,
		));

		return array(
			'topic' => $topic,
			'last_posts' => $last_posts,
		);
	}
}
