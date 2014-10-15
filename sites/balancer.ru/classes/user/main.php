<?php

require_once('inc/clients/geoip-place.php');

class user_main extends balancer_board_page
{
	function can_be_empty() { return false; }
	function is_loaded() { return $this->user() != NULL; }

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
			return array("http://www.balancer.ru/users/");
		}

		function url() { return "http://www.balancer.ru/user/".$this->id()."/"; }

	function cache_static() { return false; } // Не кешировать. Нет обработки админ-инфо

	function body_data()
	{
		$user = $this->user();

		return array_merge(parent::body_data(), array(
			'user' => $user,
			'owner' => $user,

			'friends_from' => bors_find_all('balancer_board_users_relation', array(
				'to_user_id' => $this->id(),
				'score>' => 0,
				'order' => '-score',
				'limit' => 10,
			)),
			'friends_from_quartal' => bors_find_all('balancer_board_users_relations_quartal', array(
				'to_user_id' => $this->id(),
				'score>' => 0,
				'order' => '-score',
				'limit' => 10,
			)),
			'friends_to' => bors_find_all('balancer_board_users_relation', array(
				'from_user_id' => $this->id(),
				'score>' => 0,
				'order' => '-score',
				'limit' => 10,
			)),
			'friends_to_quartal' => bors_find_all('balancer_board_users_relations_quartal', array(
				'from_user_id' => $this->id(),
				'score>' => 0,
				'order' => '-score',
				'limit' => 10,
			)),
			'enemies_from' => bors_find_all('balancer_board_users_relation', array(
				'to_user_id' => $this->id(),
				'score<' => 0,
				'order' => 'score',
				'limit' => 10,
			)),
			'enemies_from_quartal' => bors_find_all('balancer_board_users_relations_quartal', array(
				'to_user_id' => $this->id(),
				'score<' => 0,
				'order' => 'score',
				'limit' => 10,
			)),
			'enemies_to' => bors_find_all('balancer_board_users_relation', array(
				'from_user_id' => $this->id(),
				'score<' => 0,
				'order' => 'score',
				'limit' => 10,
			)),
			'enemies_to_quartal' => bors_find_all('balancer_board_users_relations_quartal', array(
				'from_user_id' => $this->id(),
				'score<' => 0,
				'order' => 'score',
				'limit' => 10,
			)),
		));
	}

	function page_data()
	{
		$db = new driver_mysql(config('punbb.database'));
		$db_bors = new driver_mysql('AB_BORS');

		$by_forums = $db->select_array('posts', 'forum_id, count(*) AS `count`', array(
			'posts.poster_id=' => $this->id(),
			'is_deleted' => false,
			'posts.posted>' => time()-86400,
			'inner_join' => 'topics ON topics.id = posts.topic_id',
			'group' => 'forum_id',
			'order' => 'COUNT(*) DESC',
		));

		$by_forums_for_month = $db->select_array('posts', 'forum_id, count(*) AS `count`', array(
			'posts.poster_id=' => $this->id(), 
			'is_deleted' => false,
			'posts.posted>' => time()-86400*30,
			'inner_join' => 'topics ON topics.id = posts.topic_id',
			'group' => 'forum_id',
			'order' => 'COUNT(*) DESC',
		));

		$by_forums_for_year = $db->select_array('posts', 'forum_id, count(*) AS `count`', array(
			'posts.poster_id=' => $this->id(), 
			'is_deleted' => false,
			'posts.posted>' => time()-86400*365,
			'inner_join' => 'topics ON topics.id = posts.topic_id',
			'group' => 'forum_id',
			'order' => 'COUNT(*) DESC',
			'limit' => 20,
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


		$user = $this->user();
		$user->set_reg_geo_ip(geoip_place($user->registration_ip()), false);

		$scores_positive = bors_find_all('bors_votes_thumb', array(
			'*set' => 'SUM(score) AS total,SUM(IF(score>0,score,0)) AS pos,SUM(IF(score<0,score,0)) AS neg',
			'target_user_id' => $this->id(),
			'create_time>' => time() - 86400*30,
			'group' => 'user_id',
			'order' => 'SUM(score) DESC',
			'limit' => 10,
		));

		$scores_negative = bors_find_all('bors_votes_thumb', array(
			'*set' => 'SUM(score) AS total,SUM(IF(score>0,score,0)) AS pos,SUM(IF(score<0,score,0)) AS neg',
			'target_user_id' => $this->id(),
			'create_time>' => time() - 86400*30,
			'group' => 'user_id',
			'order' => 'SUM(score)',
			'limit' => 10,
		));

		$votes_positive = bors_find_all('bors_votes_thumb', array(
			'*set' => 'SUM(score) AS total,SUM(IF(score>0,score,0)) AS pos,SUM(IF(score<0,score,0)) AS neg',
			'user_id' => $this->id(),
			'create_time>' => time() - 86400*30,
			'group' => 'target_user_id',
			'order' => 'SUM(score) DESC',
			'limit' => 10,
		));

		$votes_negative = bors_find_all('bors_votes_thumb', array(
			'*set' => 'SUM(score) AS total,SUM(IF(score>0,score,0)) AS pos,SUM(IF(score<0,score,0)) AS neg',
			'user_id' => $this->id(),
			'create_time>' => time() - 86400*30,
			'group' => 'target_user_id',
			'order' => 'SUM(score)',
			'limit' => 10,
		));

		bors_objects_preload($scores_positive, 'user_id', 'balancer_board_user');
		bors_objects_preload($scores_negative, 'user_id', 'balancer_board_user');
		bors_objects_preload($votes_positive, 'target_user_id', 'balancer_board_user');
		bors_objects_preload($votes_negative, 'target_user_id', 'balancer_board_user');

		if($me_id = bors()->user_id())
		{
			$pluses_from = bors_count('bors_votes_thumb', array('target_user_id' => $me_id, 'user_id' => $this->id(), 'score>' => 0));
			$minuses_from = bors_count('bors_votes_thumb', array('target_user_id' => $me_id, 'user_id' => $this->id(), 'score<' => 0));
			$pluses_to = bors_count('bors_votes_thumb', array('user_id' => $me_id, 'target_user_id' => $this->id(), 'score>' => 0));
			$minuses_to = bors_count('bors_votes_thumb', array('user_id' => $me_id, 'target_user_id' => $this->id(), 'score<' => 0));
		}

		$best_of_month	= array_filter($best_of_month,	function($p) { return !$p->target()->is_deleted();});
		$best			= array_filter($best,			function($p) { return !$p->target()->is_deleted();});

		$data = array(
			'best' => $best,
			'best_of_month' => $best_of_month,
			'messages_today' => bors_count('balancer_board_post', array('owner_id' => $this->id(), 'create_time>' => time()-86400)),
			'messages_today_by_forums' => $by_forums,
			'messages_month_by_forums' => $by_forums_for_month,
			'messages_year_by_forums' => $by_forums_for_year,
			'today_total' => bors_count('balancer_board_post', array(
				'owner_id' => $this->id(),
				'is_deleted' => false,
				'create_time>' => time()-86400,
			)),

			'tomonth_total' => bors_count('balancer_board_post', array(
				'is_deleted' => false,
				'owner_id' => $this->id(),
				'create_time>' => time()-86400*30,
			)),
			'votes_from' => bors_votes_thumb::colorize_pm(@$pluses_from, @$minuses_from),
			'votes_to'   => bors_votes_thumb::colorize_pm(@$pluses_to  , @$minuses_to  ),
			'ban' => $user->is_admin_banned(),
			'is_watcher' => (bors()->user() && (bors()->user()->is_watcher() || bors()->user()->is_admin())),
		);

		return array_merge(parent::page_data(), $data, compact(
			'scores_positive',
			'scores_negative',
			'votes_positive',
			'votes_negative'
		));
	}
}
