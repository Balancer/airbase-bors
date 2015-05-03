<?php

class bal_rpc_json_tanzpol extends bors_json
{
	function data()
	{
		if(!in_array($_SERVER['REMOTE_ADDR'], config('trusted.ips')))
			return ['REMOTE_ADDR' => $_SERVER['REMOTE_ADDR']];

		$diabled_forum_ids = [37];
		$category_ids = [6,8,10,13,14,26,27];

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

		return [
//			'most_popular_posts' => self::postify($most_popular_posts, '102x102(up,crop)'),
			'most_popular_topics' => self::topicify($most_popular_topics, '102x102(up,crop)'),
			'recent_comments' => self::postify($recent_comments, '102x102(up,crop)'),
		];
	}

	static function topicify($topics, $size = '640x640(up,crop)')
	{
		$result = [];

		foreach($topics as $t)
		{
			$result[$t->id()] = [
				'title' => $t->title(),
				'snip' => $t->first_post()->snip(),
				'create_time' => $t->create_time(),
				'url' => $t->url(),
				'thumb' => $t->image()->thumbnail($size)->url(),
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
//				'answer_to_id' => $p->answer_to_id(),
//				'have_answers' => $p->have_answers(),
				'snip' => $p->snip(),
				'create_time' => $p->create_time(),
				'url' => $p->url_for_igo(),
				'thumb' => $p->image()->thumbnail($size)->url(),
			];
		}

		return $result;
	}
}
