<?php

	class user_main extends base_page
	{
		function template()
		{
			templates_noindex();
			return 'forum/_header.html';
		}

		var $user = NULL;
	
		function title() { return $this->user()->title().ec(": Информация"); }
		function nav_name() { return $this->user()->title(); }
		
		function user()
		{
			if($this->user === NULL)
				$this->user = class_load('forum_user', $this->id());

			return $this->user;
		}

		function parents()
		{
			return array("http://balancer.ru/users/");
		}

		function url() { return "http://balancer.ru/user/".$this->id()."/"; }

	function cache_static() { return rand(3600, 7200); }

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

		return array(
			'user' => $this->user(), 
			'owner' => $this->user(), 
			'messages_today' => objects_count('forum_post', array('owner_id' => $this->id(), 'create_time>' => time()-86400)),
			'messages_today_by_forums' => $by_forums,
			'messages_month_by_forums' => $by_forums_for_month,
		);
	}
}
