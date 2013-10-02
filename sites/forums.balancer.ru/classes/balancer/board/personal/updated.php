<?php

class balancer_board_personal_updated extends balancer_board_page
{
	var $must_be_user = true;

	function title() { return ec('Обновившиеся темы, в которые Вы заходили в последнее время'); }
	function nav_name() { return ec('обновлённые темы'); }
	function auto_map() { return true; }

	function items_per_page() { return 50; }

	function body_data()
	{
		$me_id = bors()->user_id();

		$first_visit = bors_find_first('balancer_board_topics_visit', array(
			'order' => 'last_visit',
		))->last_visit();

		$topics = bors_find_all('balancer_board_topic', array(
			'*set' => 'topic_visits.last_visit AS joined_last_visit',
			'inner_join' => 'topic_visits ON (topic_visits.topic_id = balancer_board_topic.id AND topic_visits.is_disabled = 0)',
			'topic_visits.user_id=' => $me_id,
			'topic_visits.last_visit < topics.last_post',
			'order' => '-last_post',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
			'by_id' => true,
		));

		bors_objects_preload($topics, 'forum_id', 'balancer_board_forum', 'forum');
		bors_objects_preload($topics, 'owner_id', 'balancer_board_user',  'owner');
		bors_objects_preload($topics, 'image_id', 'airbase_image');

		// Если включить эту выборку прямо в загрузку topics, то работает медленнее.
		$counts = bors_find_all('balancer_board_topic', array(
			'*set' => 'COUNT(*) AS updated_count, MIN(`posts`.id) as first_post_id',
			'inner_join' => array(
				"balancer_board_topics_visit ON (balancer_board_topic.id = balancer_board_topics_visit.target_object_id AND target_class_id = 2 AND user_id = $me_id)",
				"balancer_board_post ON (balancer_board_post.topic_id = balancer_board_topic.id AND balancer_board_post.create_time > `topic_visits`.last_visit)",
			),
			'balancer_board_topic.id IN' => array_keys($topics),
			'group' => 'balancer_board_topic.id'
		));

		bors_objects_preload($counts, 'first_post_id', 'balancer_board_post',  'first_post');

		foreach($counts as $x)
		{
			$topics[$x->id()]->set_attr('first_post', $x->first_post());
			$topics[$x->id()]->set_attr('updated_count', $x->updated_count());
		}

		return compact('topics');
	}

	function total_items()
	{
		return bors_count('balancer_board_topic', array(
			'inner_join' => 'topic_visits ON (topic_visits.topic_id = balancer_board_topic.id)',
			'topic_visits.user_id=' => bors()->user_id(),
			'topic_visits.last_visit < topics.last_post',
			'topic_visits.is_disabled=' => false,
//				'topic_visits.last_visit>' => time()-86400*31,
		));
	}

	function pre_show()
	{
		if(!bors()->user())
			return bors_message('Извините, страница доступна только для авторизованных пользователей');

		return parent::pre_show();
	}
}
