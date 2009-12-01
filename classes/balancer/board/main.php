<?php

class balancer_board_main extends base_page
{
	function title() { return ec("Форумы Balancer'а"); }
	function nav_name() { return ec('форумы'); }
	function parents() { return array('http://balancer.ru/'); }
	function template() { return 'forum/_header.html'; }

	function local_data()
	{
		$new_topics = objects_array('balancer_board_topic', array(
			'order' => '-create_time',
			'limit' => 10,
			'closed' => 0,
			'num_replies>=' => 0,
			'is_public' => 1,
		));

//		bors_objects_preload($new_topics, 'first_post_id', 'balancer_board_post', 'first_post');
		bors_objects_preload($new_topics, 'forum_id', 'balancer_board_forum', 'forum');

		return array(
			'updated_topics' => objects_array('balancer_board_topic', array(
				'order' => '-modify_time',
				'limit' => 10,
				'is_public' => 1,
			)),

			'new_topics' => $new_topics,
		);
	}
}
