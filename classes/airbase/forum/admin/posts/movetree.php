<?php

class airbase_forum_admin_posts_movetree extends balancer_board_admin_page
{
	function title() { return ec('Перемещение отмеченных сообщений в другую тему'); }
	function nav_name() { return ec('Перемещение сообщений'); }

	function dont_move_tree() { return false; }

	function db_name() { return config('punbb.database', 'AB_FORUMS'); }

	function parents()
	{
		$topics = array();
		foreach($this->posts() as $p)
			$topics[$p->topic_id()] = $p->topic();

		return $topics;
	}

	private $post_ids = false;
	function post_ids()
	{
		if($this->post_ids === false)
			$this->post_ids = array_filter(@explode(',', @$_COOKIE['selected_posts'])); 

		return $this->post_ids;
	}

	private $posts = false;
	function posts()
	{
		if($this->posts === false)
			if($this->post_ids())
				$this->posts = objects_array('balancer_board_post', array('id IN' => $this->post_ids(), 'order' => 'create_time')); 
			else
				$this->posts = array();

		return $this->posts;
	}

	function body_data()
	{
		$latest_topics = bors_list::make('balancer_board_topic', array(
			'order' => '-modify_time',
			'limit' => 50,
		));

		uasort($latest_topics, function($x, $y) { return strcasecmp(preg_replace('/[^\wа-яё]/u', '', bors_lower($x)), preg_replace('/[^\wа-яё]/u', '', bors_lower($y))); });

		return array_merge(parent::body_data(), array(
			'posts' => $this->posts(),
			'last_topics' => $latest_topics,
		));
	}


	function target_topic_id() { return session_var('bba_last_target_topic_id'); }
	function target_post_id() { return ''; }

	function access_engine() { return "forum_access_move"; }

	function on_action_by_topic_id(&$data)
	{
		twitter_bootstrap::load();

		$tid = @$data['target_topic_id'];
		if(preg_match('!\d+/t(\d+)\-\-!', $tid, $m))
			$tid = $m[1];
		elseif(preg_match('!\?id=(\d+)!', $tid, $m))
			$tid = $m[1];

		if(preg_match('!/t(\d+)\-\-!', $tid, $m))
			$tid = intval($m[1]);
		elseif(preg_match('!/t(\d+),\w+\-\-!', $tid, $m))
			$tid = intval($m[1]);
		else
			$tid = intval($tid);

		set_session_var('bba_last_target_topic_id', $tid);

		$new_topic = bors_load('balancer_board_topic', $tid);
		if(!$new_topic || !$new_topic->id())
			return bors_message(ec('Тема с номером ').$tid.ec(' не существует'), array(
				'template' => 'xfile:bootstrap/index.html',
			));

		if(in_array(bors()->user_id(), array(64854 /* dmirg78 */)) && !$new_topic->forum()->is_public())
			return bors_message('Вам запрещено переносить сообщения в закрытые форумы');

		$this->topic = $new_topic;

		$posts = $this->posts();
		if(empty($posts[0]))
			$old_topic = NULL;
		else
			$old_topic = $posts[0]->topic();

		if(empty($data['dont_move_tree']))
			foreach($posts as $post)
				$post->move_tree_to_topic($new_topic->id());
		else
			foreach($posts as $post)
				$post->move_to_topic($new_topic->id());

	   	SetCookie('selected_posts', NULL, 0, '/');

		if($old_topic && $old_topic->id() != $new_topic->id())
		{
			balancer_board_action::add($new_topic, "Перенос сообщений из {$old_topic->titled_link()}");
			balancer_board_action::add($old_topic, "Перенос сообщений в {$new_topic->titled_link()}");
		}

		return bors_message_tpl("xfile:movetree.has_moved.html", $this, array(
			'title' => ec('Результат переноса сообщений'),
			'old_topic' => $old_topic,
			'new_topic' => $new_topic,
			'template' => 'xfile:bootstrap/index.html',
			'save_session' => true, // Не очищать параметры сессии
		)); 
	}

	function can_cached() { return false; }

	function pre_show()
	{
		jquery_select2::appear_ajax("'#target_topic_id'", 'balancer_board_topic', array(
			'order' => '-modify_time',
			'title_field' => 'title_with_forum',
		));

		return parent::pre_show();
	}
}
