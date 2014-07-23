<?php

class balancer_board_personal_updated extends balancer_board_page
{
	var $must_be_user = true;

	function title() { return ec('Обновившиеся темы, в которые Вы заходили в последнее время'); }
	function nav_name() { return ec('обновлённые темы'); }
	function auto_map() { return true; }

	function items_per_page() { return 50; }

	function topics()
	{
		$topics = bors_find_all('balancer_board_topic', array(
			'*set' => 'topic_visits.last_visit AS joined_last_visit',
			'inner_join' => "topic_visits ON (topic_visits.topic_id = balancer_board_topic.id AND topic_visits.user_id=".bors()->user_id().")",
			'topic_visits.is_disabled=' => false,
			'topic_visits.last_visit < topics.last_post',
			'order' => '-last_post',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
			'by_id' => true,
		));

		return $topics;
	}

	function body_data()
	{
		$me_id = bors()->user_id();

		$first_visit = bors_find_first('balancer_board_topics_visit', array(
			'order' => 'last_visit',
		))->last_visit();

		$topics = $this->topics();

		bors_objects_preload($topics, 'forum_id', 'balancer_board_forum', 'forum');
		bors_objects_preload($topics, 'owner_id', 'balancer_board_user',  'owner');
		bors_objects_preload($topics, 'image_id', 'airbase_image');

		$images = bors_field_array_extract($topics, 'image');
		bors_objects_preload($images, 'id_96x96', 'bors_image_thumb', 'thumbnail_96x96', true);

		// Если включить эту выборку прямо в загрузку topics, то работает медленнее.
		$counts = bors_find_all('balancer_board_topic', array(
			'*set' => 'COUNT(*) AS updated_count, MIN(`posts`.id) as first_post_id',
			'inner_join' => array(
				"balancer_board_topics_visit ON (balancer_board_topic.id = balancer_board_topics_visit.target_object_id)",
				"balancer_board_post ON (balancer_board_post.topic_id = balancer_board_topic.id)",
			),
			'balancer_board_topic.id IN' => array_keys($topics),
			'target_class_id' => 2,
			'user_id' => $me_id,
			'balancer_board_post.create_time > `topic_visits`.last_visit',
			'group' => 'balancer_board_topic.id'
		));

		bors_objects_preload($counts, 'first_post_id', 'balancer_board_post',  'first_post');

		$posts = array();
		$visited_ids = array();
		foreach($counts as $x)
		{
			$posts[] = $x->first_post();
			$topics[$x->id()]->set_attr('first_post', $x->first_post());
			$topics[$x->id()]->set_attr('updated_count', $x->updated_count());
			$visited_ids[] = $x->id();
		}

		$unread_counts = bors_find_all('balancer_board_topic', array(
			'*set' => 'COUNT(*) AS updated_count, MIN(`posts`.id) as first_post_id',
			'inner_join' => 'balancer_board_post ON (balancer_board_post.topic_id = balancer_board_topic.id)',
			'balancer_board_topic.id IN' => array_keys($topics),
			'topics.id NOT IN' => $visited_ids,
			'balancer_board_post.create_time > ' => time() - 86400*31,
			'group' => 'balancer_board_topic.id'
		));

		foreach($unread_counts as $x)
		{
			$posts[] = $x->first_post();
			$topics[$x->id()]->set_attr('first_post', $x->first_post());
			$topics[$x->id()]->set_attr('updated_count', $x->updated_count());
		}

		bors_objects_preload($posts, 'id', 'balancer_board_posts_cache', 'cache');

		$answers_count = bors_count('balancer_board_posts_pure', array(
			'answer_to_user_id' => $me_id,
			'posts.poster_id<>' => $me_id,
			'order' => '-create_time',
			'inner_join' => array("topics t ON t.id = posts.topic_id"),
			'left_join' => array("topic_visits v ON (v.topic_id = t.id AND v.user_id=$me_id)"),
			'((v.last_visit IS NULL AND posts.posted > '.(time()-30*86400).') OR (v.last_visit < posts.posted))',
			'posts.posted>' =>  time()-600*86400,
		));

		return compact('topics', 'answers_count');
	}

	function total_items()
	{
		return bors_count('balancer_board_topic', array(
			'inner_join' => "topic_visits ON (topic_visits.topic_id = balancer_board_topic.id AND topic_visits.user_id=".bors()->user_id().")",
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

	function is_public_access() { return true; }
}
