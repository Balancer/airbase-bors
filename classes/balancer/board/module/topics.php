<?php

// Вывод списка топиков на манер форума

class balancer_board_module_topics extends bors_module
{
	function body_data()
	{
		$topics = $this->arg('topics');

//		$me_id = bors()->user_id();

		bors_objects_preload($topics, 'forum_id', 'balancer_board_forum', 'forum');
		bors_objects_preload($topics, 'owner_id', 'balancer_board_user',  'owner');
		bors_objects_preload($topics, 'image_id', 'airbase_image');

/*
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
*/
		return array_merge(parent::body_data(), array(
			'topics' => $topics,
			'real_topic_visits' => $this->arg('real_topic_visits'), // Учитывать точное время визита, считать, что не посещалось, если нет в таблице визитов. Иначе — считаются только обновления с последней сессии.
		));
	}
}
