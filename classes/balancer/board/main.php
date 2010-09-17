<?php

class balancer_board_main extends base_page
{
	function title() { return ec("Форумы Balancer'а"); }
	function nav_name() { return ec('форумы'); }
	function parents() { return array('http://balancer.ru/'); }
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

//		bors_objects_preload($new_topics, 'first_post_id', 'balancer_board_post', 'first_post');
		bors_objects_preload($new_topics, 'forum_id', 'balancer_board_forum', 'forum');

		return array(
			'updated_topics' => objects_array('balancer_board_topic', array(
				'order' => '-modify_time',
				'limit' => 15,
				'is_public' => 1,
			)),

			'new_topics' => $new_topics,

			'top_tags' => objects_array('common_keyword', array(
				'targets_count>' => 50,
				'order' => '-targets_count',
				'limit' => 50,
				'by_id' => true,
			)),

			'last_youtube' => 'SzJA2mF14fA', //bors_server_var('last_youtube'),
			'last_youtube_post' => object_load('balancer_board_post', 2260895),

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

			'best_votes_by_authors' => objects_array('bors_votes_thumb', array(
				'select' => array('SUM(score) as sum'),
				'create_time>' => time()-86400*7,
				'group' => 'target_user_id',
				'order' => 'SUM(score) DESC',
				'limit' => 10,
			)),

		);
	}
}
