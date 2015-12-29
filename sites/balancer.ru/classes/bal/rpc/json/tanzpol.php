<?php

class bal_rpc_json_tanzpol extends bors_json
{
	const DAYS=93;

	function data()
	{
		if(!in_array($_SERVER['REMOTE_ADDR'], config('trusted.ips')))
			return ['REMOTE_ADDR' => $_SERVER['REMOTE_ADDR']];

		$diabled_forum_ids = [37];
		$category_ids = [6,8,10,13,14,26,27];

		$where_news_post = [
			'*set' => 'topics.subject as topic_title',
			'inner_join' => [
				'topics ON posts.topic_id = topics.id',
				'balancer_board_forum ON balancer_board_forum.id = forum_id',
				'balancer_board_category ON balancer_board_category.id = balancer_board_forum.category_id',
			],
			'balancer_board_forum.category_id IN' => $category_ids,
			'forum_id NOT IN' => $diabled_forum_ids,
			'create_time BETWEEN' => [time() - 86400*self::DAYS,  time()],
			'balancer_board_topic.is_public=' => true,
			'order' => '-create_time',
//			'CHARACTER_LENGTH(`source`)>512',
//			'score>=' => 1,
			'answer_to_id' => NULL,
			'by_id' => true,
		];

		$nkid = common_keyword::loader('новости')->id();
		$all_news_topic_ids = bors_field_array_extract(bors_find_all('common_keyword_bind', [
				'keyword_id' => $nkid,
				'target_class_name IN' => ['forum_topic', 'balancer_board_topic'],
				'target_modify_time>' => time() - 86400*self::DAYS,
		]), 'target_object_id');

		$most_popular_topics = bors_find_all('balancer_board_topic', [
			'inner_join' => [
				'balancer_board_forum ON balancer_board_forum.id = forum_id',
				'balancer_board_category ON balancer_board_category.id = balancer_board_forum.category_id',
			],
			'num_views>=' => 10,
			'last_visit - first_visit > 600',
			'create_time>' => time() - 86400*180,
			'*set' => '(86400*num_views)/(last_visit-first_visit) AS views_per_day',
			'order' => '(86400*num_views)/(last_visit-first_visit) DESC',
			'balancer_board_forum.is_public=' => true,
			'balancer_board_forum.category_id IN' => $category_ids,
			'forum_id NOT IN' => $diabled_forum_ids,
			'limit' => 3,
		]);

		$most_popular_posts = bors_find_all('balancer_board_post', [
			'inner_join' => [
				'balancer_board_topic ON balancer_board_topic.id = topic_id',
				'balancer_board_forum ON balancer_board_forum.id = forum_id',
				'balancer_board_category ON balancer_board_category.id = balancer_board_forum.category_id',
			],
			'answer_to_id' => 0,
			'balancer_board_post.create_time BETWEEN' => [time() - 86400*30, time()],
			'balancer_board_forum.is_public=' => true,
			'balancer_board_forum.category_id IN' => $category_ids,
			'forum_id NOT IN' => $diabled_forum_ids,
			'order' => '-have_answers',
			'limit' => 3,
		]);

		$recent_comments = bors_find_all('balancer_board_post', [
			'inner_join' => [
				'balancer_board_topic ON balancer_board_topic.id = topic_id',
				'balancer_board_forum ON balancer_board_forum.id = forum_id',
				'balancer_board_category ON balancer_board_category.id = balancer_board_forum.category_id',
			],
			'balancer_board_post.create_time BETWEEN' => [time() - 86400*7, time()],
			'order' => '-balancer_board_post.create_time',
			'balancer_board_forum.is_public=' => true,
			'balancer_board_forum.category_id IN' => $category_ids,
			'forum_id NOT IN' => $diabled_forum_ids,
			'limit' => 3,
		]);

		$top_news = self::postify(bors_find_all('balancer_board_post', array_merge($where_news_post, [
			'topic_id IN' => $all_news_topic_ids,
			'limit' => 4,
		])), '300x215(up,crop)');
		$used_keys = array_keys($top_news);

		// http://www.balancer.ru/rpc/json/find/balancer_board_post?tags=новости&fields=body,title,snip&thumb=550x330(up,crop)&img=550&limit=10
		$big_pics = self::postify(bors_find_all('balancer_board_post', array_merge($where_news_post, [
			'topic_id IN' => $all_news_topic_ids,
			'id NOT IN' => $used_keys,
			'limit' => 10,
		])), '550x330(up,crop)');
		$used_keys = array_merge($used_keys, array_keys($big_pics));

		// http://www.balancer.ru/rpc/json/find/balancer_board_post?tags=новости&fields=body,title,snip&thumb=567x330(up,crop)&img=567&limit=3
		$big_pics_mid = self::postify(bors_find_all('balancer_board_post', array_merge($where_news_post, [
			'topic_id IN' => $all_news_topic_ids,
			'limit' => 3,
			'id NOT IN' => $used_keys,
		])), '567x330(up,crop)');

		return array_merge(compact('big_pics', 'big_pics_mid', 'top_news'), [
//			'most_popular_posts' => self::postify($most_popular_posts, '102x102(up,crop)'),
			'most_popular_topics' => self::topicify($most_popular_topics, '102x102(up,crop)'),
			'recent_comments' => self::postify($recent_comments, '102x102(up,crop)'),
		]);
	}

	static function topicify($topics, $size = '640x640(up,crop)')
	{
		$result = [];

		foreach($topics as $t)
		{
			$thumb = $t->image()->thumbnail($size);

			$result[$t->id()] = [
				'title' => $t->title(),
				'snip' => $t->first_post()->snip(),
				'create_time' => $t->create_time(),
				'url' => $t->url(),
				'thumb' => $thumb->url(),
				'thumbnail_url' => $thumb->url(),
				'ths' => $size,
				'iu' => $t->image()->url(),
			];
		}

		return $result;
	}

	static function postify($posts, $size = '640x640(up,crop)')
	{
		$result = [];

		foreach($posts as $p)
		{
			$result[$p->id()] = [
				'title' => $p->topic()->title(),
				'topic_title' => $p->topic()->title(),
//				'answer_to_id' => $p->answer_to_id(),
//				'have_answers' => $p->have_answers(),
				'snip' => $p->snip(),
				'create_time' => $p->create_time(),
				'url' => $p->url_for_igo(),
				'thumb' => $p->image()->thumbnail($size)->url(),
				'thumbnail_url' => $p->image()->thumbnail($size)->url(),
			];
		}

		return $result;
	}
}
