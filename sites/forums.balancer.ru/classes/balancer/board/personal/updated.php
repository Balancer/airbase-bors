<?php

class balancer_board_personal_updated extends balancer_board_page
{
	function title() { return ec('Обновившиеся темы, в которые Вы заходили в последнее время'); }
	function nav_name() { return ec('обновлённые темы'); }
	function auto_map() { return true; }

	function items_per_page() { return 50; }

	function body_data()
	{
		$topics = bors_find_all('balancer_board_topic', array(
			'*set' => 'topic_visits.last_visit AS joined_last_visit',
			'inner_join' => 'topic_visits ON (topic_visits.topic_id = balancer_board_topic.id AND topic_visits.is_disabled = 0)',
			'topic_visits.user_id=' => bors()->user_id(),
			'topic_visits.last_visit < topics.last_post',
//			'topic_visits.last_visit>' => time()-86400*31,
			'order' => '-last_post',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
		));

		bors_objects_preload($topics, 'forum_id', 'balancer_board_forum', 'forum');
		bors_objects_preload($topics, 'owner_id', 'balancer_board_user',  'owner');

		return compact('topics');
	}

	function total_items()
	{
		return bors_count('balancer_board_topic', array(
			'inner_join' => 'topic_visits ON (topic_visits.topic_id = balancer_board_topic.id)',
			'topic_visits.user_id=' => bors()->user_id(),
			'topic_visits.last_visit < topics.last_post',
//				'topic_visits.last_visit>' => time()-86400*31,
		));
	}
}
