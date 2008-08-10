<?php

class forum_tools_post_moveTree extends base_page
{
	function can_be_empty() { return true; }

	function dont_move_tree() { return false; }

	function parents() { return array('forum_post://'.$this->post()->id()); }
	
	function main_db_storage() { return 'punbb'; }

	var $post, $topic;
	function post() { return $this->post; }
	function topic() { return $this->topic; }
	
	function __construct($id)
	{
		$this->db = &new driver_mysql($this->main_db_storage());
		
		$this->post = object_load('forum_post', intval($id));
		$this->topic = $this->post()->topic();
		return parent::__construct($id);
	}

//	var $db;

	function _queries()
	{
		return array(
			'last_topics' => 'SELECT id FROM topics ORDER BY last_post DESC LIMIT 50',
		);
	}

	function title() { return ec('Перемещение сообщения<br /><i>').$this->post->title().'</i>'; }
	function nav_name() { return ec('Перемещение сообщения'); }

	function target_topic_id() { return $this->post()->topic()->id(); }

	function target_post_id() { return $this->post()->id(); }

	function url() { return 'http://balancer.ru/forum/tools/move_post_tree/'.$this->id().'/'; }

	function template() { return "forum/_header.html"; }

	function access_engine() { return "forum_access_move"; }

	function onAction_by_topic_id(&$data)
	{
		$tid = @$data['target_topic_id'];
		if(preg_match('!topic\-(\d+)\-\-!', $tid, $m))
			$tid = $m[1];
		elseif(preg_match('!\?id=(\d+)!', $tid, $m))
			$tid = $m[1];

		$tid = intval($tid);
	
		$new_topic = object_load('forum_topic', $tid);
		if(!$new_topic || !$new_topic->id())
			return bors_message(ec('Тема с номером ').$tid.ec(' не существует'));

		$this->topic = $new_topic;
			
		if(empty($data['dont_move_tree']))
			$this->post()->move_tree_to_topic($new_topic->id());
		else
			$this->post()->move_to_topic($new_topic->id());
	   
		return bors_message_tpl("xfile:moveTree.has_moved.html", $this, array(
			'title' => ec('Сообщения успешно перенесены'),
		)); 
	}

	function onAction_by_post_id(&$data)
	{
		$pid = @$data['target_post_id'];
		if(preg_match('!post\-(\d+)!', $pid, $m))
			$pid = $m[1];
		elseif(preg_match('!\?pid=(\d+)!', $pid, $m))
			$pid = $m[1];

		$pid = intval($pid);
	
		$new_post = object_load('forum_post', $pid);
		if(!$new_post || !$new_post->id())
			return bors_message(ec('Сообщение с номером ').$pid.ec(' не существует'));

		$data['target_topic_id'] = $new_post->topic()->id();
		return $this->onAction_by_topic_id($data);
	}
}
