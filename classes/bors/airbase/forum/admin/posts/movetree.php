<?php

class airbase_forum_admin_posts_movetree extends base_page
{
	function class_file() { return __FILE__; }
	function dont_move_tree() { return false; }

	function main_db() { return config('punbb.database', 'punbb'); }

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
				$this->posts = objects_array('forum_post', array('id IN' => $this->post_ids(), 'order' => 'create_time')); 
			else
				$this->posts = array();

		return $this->posts;
	}

	function local_data()
	{
		$topics = array();
		if(!empty($_SESSION['bba_last_target_topic_id']))
			$topics[] = object_load('forum_topic', $_SESSION['bba_last_target_topic_id']);

		$topics = array_merge($topics, objects_array('forum_topic', array('order' => '-last_post' , 'limit' => 200)));

		return array(
			'posts' => $this->posts(),
			'last_topics' => $topics,
		);
	}

	function title() { return ec('Перемещение отмеченных сообщений в другую тему'); }
	function nav_name() { return ec('Перемещение сообщений'); }

	function target_topic_id() { return @$_SESSION['bba_last_target_topic_id']; }
	function target_post_id() { return ''; }

	function template() { return "forum/_header.html"; }

	function access_engine() { return "forum_access_move"; }
//	function post() { }

	function pre_parse()
	{
//TODO: проверить, запоминает ли последний топик
//		session_register('bba_last_target_topic_id');
		return parent::pre_parse();
	}

	function on_action_by_topic_id(&$data)
	{
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
	
		$_SESSION['bba_last_target_topic_id'] = $tid;
	
		$new_topic = object_load('forum_topic', $tid);
		if(!$new_topic || !$new_topic->id())
			return bors_message(ec('Тема с номером ').$tid.ec(' не существует'));

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
			balancer_board_action::add($new_topic, "Перенос сообщений из {$old_topic->titled_url()}");
			balancer_board_action::add($old_topic, "Перенос сообщений в {$new_topic->titled_url()}");
		}

		return bors_message_tpl("xfile:movetree.has_moved.html", $this, array(
			'title' => ec('Сообщения успешно перенесены'),
			'old_topic' => $old_topic,
			'new_topic' => $new_topic,
		)); 
	}
	
	function can_cached() { return false; }
}
