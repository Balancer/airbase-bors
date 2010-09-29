<?php

class user_main extends base_page
{
	function can_be_empty() { return false; }
	function loaded() { return $this->user() != NULL; }

		function template()
		{
			template_noindex();
			return 'forum/_header.html';
		}

		var $user = NULL;

		function title() { return $this->user()->title().ec(": Информация"); }
		function nav_name() { return $this->user()->title(); }

		function user()
		{
			if($this->user === NULL)
				$this->user = class_load('balancer_board_user', $this->id());

			return $this->user;
		}

		function parents()
		{
			return array("http://balancer.ru/users/");
		}

		function url() { return "http://balancer.ru/user/".$this->id()."/"; }

	function cache_static() { return rand(600, 1200); }

	function local_data()
	{
		$by_forums = $this->db('punbb')->select_array('posts', 'forum_id, count(*) AS `count`', array(
			'posts.poster_id=' => $this->id(), 
			'posts.posted>' => time()-86400,
			'inner_join' => 'topics ON topics.id = posts.topic_id',
			'group' => 'forum_id',
			'order' => 'COUNT(*) DESC',
		));

		$by_forums_for_month = $this->db('punbb')->select_array('posts', 'forum_id, count(*) AS `count`', array(
			'posts.poster_id=' => $this->id(), 
			'posts.posted>' => time()-86400*30,
			'inner_join' => 'topics ON topics.id = posts.topic_id',
			'group' => 'forum_id',
			'order' => 'COUNT(*) DESC',
		));

		$best = objects_array('bors_votes_thumb', array(
				'target_user_id' => $this->id(),
				'group' => 'target_class_name,target_object_id',
				'having' => 'SUM(score) > 0',
				'order' => 'SUM(score) DESC',
				'limit' => 20,
		));

		$best_of_month = objects_array('bors_votes_thumb', array(
				'target_user_id' => $this->id(),
				'create_time>' => time()-86400*30,
				'group' => 'target_class_name,target_object_id',
				'having' => 'SUM(score) > 0',
				'order' => 'SUM(score) DESC',
				'limit' => 20,
		));

		bors_objects_targets_preload($best);
		bors_objects_targets_preload($best_of_month);

		return array(
			'best' => $best,
			'best_of_month' => $best_of_month,
			'user' => $this->user(), 
			'owner' => $this->user(), 
			'messages_today' => objects_count('forum_post', array('owner_id' => $this->id(), 'create_time>' => time()-86400)),
			'messages_today_by_forums' => $by_forums,
			'messages_month_by_forums' => $by_forums_for_month,
			'today_total' => objects_count('balancer_board_post', array(
				'owner_id' => $this->id(),
				'create_time>' => time()-86400,
			)),
			'tomonth_total' => objects_count('balancer_board_post', array(
				'owner_id' => $this->id(),
				'create_time>' => time()-86400*30,
			)),
		);
	}
}
