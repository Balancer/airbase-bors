<?php

class balancer_board_topics_similar extends base_js
{
	function body_data()
	{
		$topic = bors_load('balancer_board_topic', $this->id());
		$topic_ids = common_keyword::best_topic($topic->keywords_string(), array(), false, 10, 0.5, array($topic->id()));
		$topics = bors_find_all('balancer_board_topic', array('id IN' => $topic_ids, 'by_id' => true));
		$list = array();
		foreach($topic_ids as $tid)
			$list[] = $t = $topics[$tid];

		return compact('list');
	}

	function cache_static() { return rand(3600,7200); }
}
