<?php

class forum_tools_post_moveTree extends base_page
{
	function can_be_empty() { return true; }

	function dont_move_tree() { return false; }

	function parents() { return array('forum_post://'.$this->post()->id()); }
	
	function main_db() { return 'punbb'; }

	var $post, $topic;
	function post() { return $this->post; }
	function topic() { return $this->topic; }
	
	function __construct($id)
	{
		$this->db = &new driver_mysql($this->main_db());
		
		$this->post = object_load('forum_post', intval($id));
		$this->topic = $this->post()->topic();
		return parent::__construct($id);
	}

	function can_cache() { return false; }

	function local_data()
	{
		$topics = array();
		if(!empty($_SESSION['bba_last_target_topic_id']))
			$topics[] = object_load('forum_topic', $_SESSION['bba_last_target_topic_id']);
	
		$topics = array_merge($topics, objects_array('forum_topic', array('order' => '-last_post' , 'limit' => 100)));
	
		return array(
			'last_topics' => $topics,
		);
	}

	function title() { return ec('Перемещение сообщения<br /><i>').$this->post->title().'</i>'; }
	function nav_name() { return ec('Перемещение сообщения'); }

	function target_topic_id() { return @$_SESSION['bba_last_target_topic_id']; }
	function target_post_id() { return ''; }

	function url() { return 'http://balancer.ru/admin/forum/post/'.$this->id().'/move-tree'; }

	function template() { return "forum/_header.html"; }

	function access_engine() { return "forum_access_move"; }

	function pre_parse()
	{
		session_register('bba_last_target_topic_id');
	}

	function on_action_by_topic_id(&$data)
	{
		$tid = @$data['target_topic_id'];
		if(preg_match('!\d+/t(\d+)!', $tid, $m))
			$tid = $m[1];
		elseif(preg_match('!\?id=(\d+)!', $tid, $m))
			$tid = $m[1];

		$tid = intval($tid);
	
		$new_topic = object_load('forum_topic', $tid, array('no_load_cache' => true));
		if(!$new_topic || !$new_topic->id())
			return bors_message(ec('Тема с номером ').$tid.ec(' не существует'));

		$_SESSION['bba_last_target_topic_id'] = $tid;

		$this->topic = $new_topic;

		$old_topic = $this->post()->topic();

		if(empty($data['dont_move_tree']))
			$this->post()->move_tree_to_topic($new_topic->id());
		else
			$this->post()->move_to_topic($new_topic->id());

		if($old_topic->id() != $new_topic->id())
		{
			balancer_board_action::add($new_topic, "Перенос сообщений из {$old_topic->titled_url()}");
			balancer_board_action::add($old_topic, "Перенос сообщений в {$new_topic->titled_url()}");
		}
		
		return bors_message_tpl("moveTree.has_moved.html", $this, array(
			'title' => ec('Сообщения успешно перенесены'),
			'old_topic' => $old_topic,
			'new_topic' => $new_topic,
		)); 
	}

	function on_action_by_post_id(&$data)
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
		return $this->on_action_by_topic_id($data);
	}
}
