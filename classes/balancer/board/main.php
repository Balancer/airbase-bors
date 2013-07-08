<?php

bors_function_include('time/smart_time');

class balancer_board_main extends balancer_board_page
{
	function title() { return ec("Форумы Balancer'а"); }
	function nav_name() { return ec('форумы'); }
	function parents() { return array('http://www.balancer.ru/'); }
	function template() { return 'forum/wide.html'; }

	function pre_show()
	{
		base_object::add_template_data_array('head_append', '<script type="text/javascript" src="http://vkontakte.ru/js/api/share.js?9" charset="windows-1251"></script>');

		return parent::pre_show();
	}
/*
	function global_data()
	{
		return array_merge(parent::global_data(), array(
			'right_column' => 'xfile:forum/main-right.html',
		));
	}
*/
	function local_data()
	{
		$new_topics = objects_array('balancer_board_topic', array(
			'order' => '-create_time',
			'limit' => 10,
			'closed' => 0,
			'num_replies>=' => 0,
			'is_public' => 1,
		));

		$youtube_objects = bors_find_all('balancer_board_posts_object', array(
			'target_class_name' => 'bors_external_youtube',
			'target_score>' => 2,
			'order' => '-target_create_time',
			'limit' => 20,
		));

		$top_visit_topics = objects_array('balancer_board_topic', array(
				'num_views>=' => 10,
				'last_visit - first_visit > 600',
				'*set' => '(86400*num_views)/(last_visit-first_visit) AS views_per_day',
				'order' => '(86400*num_views)/(last_visit-first_visit) DESC',
				'forum_id NOT IN' => array(37),
				'limit' => 20,
		));

		srand();
		usort($youtube_objects, create_function('$x, $y', 'return rand(0, $y->target_score()+1) - rand(0, $x->target_score()+1);'));
//		var_dump($youtube_objects[0]->data);
//		bors_objects_preload($new_topics, 'first_post_id', 'balancer_board_post', 'first_post');
		bors_objects_preload(array_merge($new_topics, $top_visit_topics), 'forum_id', 'balancer_board_forum', 'forum');

		return array(
			'updated_topics' => bors_find_all('balancer_board_topic', array(
				'order' => '-last_post_create_time',
				'limit' => 20,
				'is_public' => 1,
			)),

			'new_topics' => $new_topics,
			'top_visit_topics' => $top_visit_topics,

			'top_tags' => objects_array('common_keyword', array(
				'targets_count>' => 50,
				'order' => '-targets_count',
				'limit' => 50,
				'by_id' => true,
			)),

			'last_youtube' => $youtube_objects[0]->target_object_id(),// 'SzJA2mF14fA',
			'last_youtube_post' => object_load('balancer_board_post', $youtube_objects[0]->post_id()),

			'best_of_days' => objects_first('bors_votes_thumb', array(
				'create_time>' => time()-86400*3,
				'group' => 'target_class_name,target_object_id',
				'order' => 'SUM(score) DESC',
			)),

			'last_post' => objects_first('balancer_board_post', array(
				'inner_join' => array('balancer_board_topic ON topic_id = balancer_board_topic.id'),
				'is_public' => 1,
				'create_time>' => time()-86400,
				'order' => '-create_time',
				'owner_id>' => 0,
			)),

			'best_votes_by_authors' => bors_find_all('bors_votes_thumb', array(
				'select' => array('SUM(score) as sum'),
				'create_time>' => time()-86400*30,
				'group' => 'target_user_id',
				'order' => 'SUM(score) DESC',
				'limit' => 10,
			)),

			'best_voted_users' => bors_find_all('balancer_board_user2', array(
				'*set' => '((pos + 1.9208) / (pos + neg) - 1.96 * SQRT((pos * neg) / (pos + neg) + 0.9604) / (pos + neg)) / (1 + 3.8416 / (pos + neg))   AS ci_lower_bound',
				'inner_join' => 'AB_BORS.v_thumb_votes_sum_7 ON target_user_id = id',
				'pos + neg > 0',
				'order' => 'ci_lower_bound DESC',
				'limit' => 20,
			)),

		);
	}
}
