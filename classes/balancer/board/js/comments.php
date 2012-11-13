<?php

class balancer_board_js_comments extends base_js
{
	function local_data()
	{
		$topic = object_load('balancer_board_topic', $this->id());
//		$total_posts = objects_count('forum_post', array('topic_id' => $this->id()));
		$last_posts = objects_array('forum_post', array(
				'topic_id' => $this->id(),
				'order' => '-create_time',
				'limit' => 10,
				'id<>' => $topic->first_post_id(),
		));

		return array(
			'topic' => $topic,
			'last_posts' => $last_posts,
		);
	}
}
