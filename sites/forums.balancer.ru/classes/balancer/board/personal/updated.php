<?php

class balancer_board_personal_updated extends balancer_board_page
{
	function title() { return ec('Обновившиеся темы, в которые Вы заходили за последний месяц'); }
	function nav_name() { return ec('обновлённые темы'); }
	function is_auto_url_mapped_class() { return true; }

	function items_per_page() { return 50; }

	function local_data()
	{
		$topics = objects_array('balancer_board_topic', array(
			'inner_join' => 'topic_visits ON (topic_visits.topic_id = balancer_board_topic.id)',
			'topic_visits.user_id=' => bors()->user_id(),
			'topic_visits.last_visit < topics.last_post',
			'topic_visits.last_visit>' => time()-86400*30,
			'order' => '-last_post',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),

		));

		return compact('topics');
	}

	function total_items()
	{
		return objects_count('balancer_board_topic', array(
			'inner_join' => 'topic_visits ON (topic_visits.topic_id = balancer_board_topic.id)',
			'topic_visits.user_id=' => bors()->user_id(),
			'topic_visits.last_visit < topics.last_post',
			'topic_visits.last_visit>' => time()-86400*30,
		));
	}
}
