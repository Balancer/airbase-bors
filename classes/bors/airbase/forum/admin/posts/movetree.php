<?php

class airbase_forum_admin_posts_movetree extends base_page
{
	function class_file() { return __FILE__; }
	function dont_move_tree() { return false; }

	function main_db_storage() { return 'punbb'; }

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

	function local_template_data_set()
	{
		return array(
			'posts' => $this->posts(),
			'last_topics' => objects_array('forum_topic', array('order' => '-last_post' , 'limit' => 100)),
		);
	}

	function title() { return ec('Перемещение отмеченных сообщений в другую тему'); }
	function nav_name() { return ec('Перемещение сообщений'); }

	function target_topic_id() { return 0; }
	function target_post_id() { return 0; }

	function template() { return "forum/_header.html"; }

	function access_engine() { return "forum_access_move"; }
	function post() { return 0; }

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
	
		$new_topic = object_load('forum_topic', $tid);
		if(!$new_topic || !$new_topic->id())
			return bors_message(ec('Тема с номером ').$tid.ec(' не существует'));

		$this->topic = $new_topic;
			
		if(empty($data['dont_move_tree']))
			foreach($this->posts() as $post)
				$post->move_tree_to_topic($new_topic->id());
		else
			foreach($this->posts() as $post)
				$post->move_to_topic($new_topic->id());
	   
	   	SetCookie('selected_posts', NULL, 0, '/');

		return bors_message_tpl("xfile:movetree.has_moved.html", $this, array(
			'title' => ec('Сообщения успешно перенесены'),
			'new_topic' => $new_topic,
		)); 
	}
	
	function can_cached() { return false; }
}
