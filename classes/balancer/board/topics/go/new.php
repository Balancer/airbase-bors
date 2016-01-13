<?php

class balancer_board_topics_go_new extends bors_page
{
	var $auto_map = true;

	function pre_show()
	{
		$me = bors()->user();

		if($me)
			$me->utmx_update();

		// Если гость или не нашли, куда перейти
		$topic = bors_load('balancer_board_topic', $this->id());

		// Если регистрант
		if($me && $me->id() >= 2)
		{
			$first_new_post = $topic->find_first_unvisited_post($me);

			// Если мы нашли первое нечитанное сообщение, то переходим к нему
			if($first_new_post)
				return go($first_new_post->url_in_topic(NULL, true));

			// Если не нашли, то тупо переходим на последнее сообщение.
			$last_post = bors_find_first('balancer_board_posts_pure', [
				'topic_id' => $this->id(),
				'use_index' => 'by_tid_ordered',
				'order' => '-sort_order,-id',
			]);

			if($last_post)
				return go($last_post->url_in_topic(NULL, true));
		}


		return go($topic->url_ex(1));
	}
}
