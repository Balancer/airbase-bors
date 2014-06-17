<?php

class forum_tools_post_moveTree extends balancer_board_admin_page
{
	function title() { return ec('Перемещение сообщения<br /><i><small>').$this->post->title().'</small></i>'; }
	function nav_name() { return ec('Перемещение сообщения'); }

	function dont_move_tree() { return false; }

	function parents() { return array('balancer_board_post://'.$this->post()->id()); }

	function db_name() { return config('punbb.database', 'AB_FORUMS'); }

	var $post, $topic;
	function post() { return $this->post; }
	function topic() { return $this->topic; }

	function __construct($id)
	{
		$this->db = new driver_mysql($this->db_name());
		if($id)
		{
			$this->post = bors_load('balancer_board_post', intval($id));
			$this->topic = $this->post->topic();
		}
		return parent::__construct($id);
	}

	function can_cache() { return false; }

	function body_data()
	{
		$latest_topics = bors_list::make('balancer_board_topic', array(
			'order' => '-modify_time',
			'limit' => 50,
		));

		uasort($latest_topics, function($x, $y) { return strcasecmp(preg_replace('/[^\wа-яё]/u', '', bors_lower($x)), preg_replace('/[^\wа-яё]/u', '', bors_lower($y))); });

		return array_merge(parent::body_data(), array(
			'last_topics' => $latest_topics,
		));
	}


	function target_topic_id() { return session_var('bba_last_target_topic_id'); }
	function target_post_id() { return ''; }

	function url() { return 'http://www.balancer.ru/admin/forum/post/'.$this->id().'/move-tree'; }

	function access_engine() { return "forum_access_move"; }

	function on_action_by_topic_id(&$data)
	{
		twitter_bootstrap::load();

		$tid = @$data['target_topic_id'];
		if(preg_match('!\d+/t(\d+)!', $tid, $m))
			$tid = $m[1];
		elseif(preg_match('!\?id=(\d+)!', $tid, $m))
			$tid = $m[1];

		$tid = intval($tid);

		$new_topic = bors_load('balancer_board_topic', $tid, array('no_load_cache' => true));
		if(!$new_topic || !$new_topic->id())
			return bors_message(ec('Тема с номером ').$tid.ec(' не существует'), array(
				'template' => 'xfile:bootstrap/index.html',
			));

		set_session_var('bba_last_target_topic_id', $tid);

		$this->topic = $new_topic;

		$old_topic = $this->post()->topic();

		if(in_array($new_topic->forum_id(), array(37 /* отстойник */)))
		{
//		if(in_array(bors()->user_id(), array(64854 /* dmirg78 */)) && !$new_topic->forum()->is_public())
			return bors_message('Запрещён снос сообщений в отстойник');
		}

		if(empty($data['dont_move_tree']))
			$this->post()->move_tree_to_topic($new_topic->id());
		else
			$this->post()->move_to_topic($new_topic->id());

		if($old_topic->id() != $new_topic->id())
		{
			balancer_board_action::add($new_topic, "Перенос сообщений из {$old_topic->titled_link()}");
			balancer_board_action::add($old_topic, "Перенос сообщений в {$new_topic->titled_link()}");
		}

		$new_post = bors_load_ex('balancer_board_post', $this->post()->id(), array('no_load_cache' => true));

		return bors_message_tpl("moveTree.has_moved.html", $this, array(
			'post' => $new_post,
			'title' => ec('Результат переноса сообщений'),
			'old_topic' => $old_topic,
			'new_topic' => $new_topic,
			'template' => 'xfile:bootstrap/index.html',
			'save_session' => true, // Не очищать параметры сессии
		)); 
	}

	function on_action_by_post_id(&$data)
	{
		twitter_bootstrap::load();

		$pid = @$data['target_post_id'];
		if(preg_match('!p(\d+)$!', $pid, $m))
			$pid = $m[1];
		elseif(preg_match('!post\-(\d+)!', $pid, $m))
			$pid = $m[1];
		elseif(preg_match('!\?pid=(\d+)!', $pid, $m))
			$pid = $m[1];
		elseif(preg_match('!/p(\d+)\.html$!', $pid, $m))
			$pid = $m[1];

		$pid = intval($pid);

		$new_post = bors_load('balancer_board_post', $pid);
		if(!$new_post || !$new_post->id())
			return bors_message(ec('Сообщение с номером ').$pid.ec(' не существует'), array(
				'template' => 'xfile:bootstrap/index.html',
			));

		$data['target_topic_id'] = $new_post->topic()->id();
		return $this->on_action_by_topic_id($data);
	}

	function pre_show()
	{
		jquery_select2::appear_ajax("'#target_topic_id'", 'balancer_board_topic', array(
			'order' => '-modify_time',
			'title_field' => 'title_with_forum',
		));

		return parent::pre_show();
	}
}
