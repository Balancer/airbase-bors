<?php

class balancer_board_personal_subscribes extends balancer_board_page
{
	function title() { return ec('Темы, на обновления которых Вы подписаны'); }
	function nav_name() { return ec('подписки'); }
	function auto_map() { return true; }

	function items_per_page() { return 50; }

	function pre_show()
	{
		if(!bors()->user())
			return bors_message(ec('Страница только для зарегистрированных пользователей'));

		return parent::pre_show();
	}

	function body_data()
	{
		$me_id = bors()->user_id();
		$topics = bors_find_all('balancer_board_topic', array(
			'*set' => 'IF(topic_visits.last_visit < topics.last_post, 1, 0) AS was_updated',
			'inner_join' => 'balancer_board_users_subscription ON (balancer_board_users_subscription.topic_id = balancer_board_topic.id)',
			'balancer_board_users_subscription.user_id=' => $me_id,
			'left_join' => "topic_visits ON (topic_visits.topic_id = balancer_board_topic.id AND topic_visits.is_disabled = 0 AND topic_visits.user_id=$me_id)",
			'order' => '-last_post',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
			'by_id' => true,
		));

		bors_objects_preload($topics, 'forum_id', 'balancer_board_forum', 'forum');
		bors_objects_preload($topics, 'owner_id', 'balancer_board_user',  'owner');

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
		return bors_count('balancer_board_users_subscription', array(
			'balancer_board_users_subscription.user_id=' => bors()->user_id(),
		));
	}
}
