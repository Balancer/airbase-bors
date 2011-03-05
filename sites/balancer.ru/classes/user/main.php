<?php

require_once('inc/clients/geoip-place.php');

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

	function cache_static() { return false; } // Не кешировать. Нет обработки админ-инфо

	function page_data()
	{
		$db = new driver_mysql('punbb');
		$db_bors = new driver_mysql('BORS');

		$by_forums = $db->select_array('posts', 'forum_id, count(*) AS `count`', array(
			'posts.poster_id=' => $this->id(), 
			'posts.posted>' => time()-86400,
			'inner_join' => 'topics ON topics.id = posts.topic_id',
			'group' => 'forum_id',
			'order' => 'COUNT(*) DESC',
		));

		$by_forums_for_month = $db->select_array('posts', 'forum_id, count(*) AS `count`', array(
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

		if(bors()->user() && ($is_watcher = bors()->user()->is_watcher() || bors()->user()->is_admin()))
		{
			$interlocutors = $db->select_array('posts', 'poster_id, COUNT(*) as answers_count', array(
				'posted>' => time() - 365*86400,
				'anwer_to_user_id' => $this->id(),
				'group' => 'poster_id',
//				'order' => 'COUNT(*) DESC',
			));
			$interlocutor_stats = array();
			foreach($interlocutors as $x)
				$interlocutor_stats[$x['poster_id']] = $x['answers_count'];
//var_dump($interlocutor_stats);
			$interlocutors = bors_find_all('balancer_board_user', array('id IN' => array_keys($interlocutor_stats)));

			foreach($interlocutors as $x)
			{
				$x->set_answers($interlocutor_stats[$x->id()], false);
			}

			usort($interlocutors, create_function('$x, $y', 'return $y->answers() - $x->answers();'));

			$last_ips = $db->select_array('posts', 'poster_ip, COUNT(*) AS count', array(
				'poster_id' => $this->id(),
				'posted>' => time()-30*86400,
				'group' => 'poster_ip',
				'order' => 'MAX(posted) DESC',
			));
		}
		else
		{
			$last_ips = false;
			$interlocutors = false;
			$interlocutor_stats = false;
		}

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

		$data = array(
			'best' => $best,
			'best_of_month' => $best_of_month,
			'user' => $user,
			'owner' => $user,
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

		return array_merge(parent::page_data(), $data, compact(
			'is_watcher',
			'interlocutors',
			'interlocutor_stats',
			'last_ips',
			'scores_positive',
			'scores_negative',
			'votes_positive',
			'votes_negative'
		));
	}
}
