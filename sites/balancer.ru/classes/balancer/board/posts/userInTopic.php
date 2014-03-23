<?php

class balancer_board_posts_userInTopic extends balancer_board_posts_list
{
	function title() { return ec('Сообщения пользователя ').$this->user()->title().ec(' в теме ').$this->topic()->title(); }
	function nav_name() { return ec('Сообщения пользователя ').$this->user()->title(); }

	function can_be_empty() { return false; }
	function is_loaded() { return $this->topic() != NULL; }

	function parents() { return array($this->topic()->url()); }

	static function id_prepare($id)
	{
		return str_replace('-posts-in-topic-', '-', $id);
	}

	function __construct($id)
	{
		list($user_id, $topic_id) = explode('-', $id);
		$this->set_user_id($user_id, false);
		$this->set_topic_id($topic_id, false);

		parent::__construct($id);
	}

	function auto_objects()
	{
		return array(
			'user' => 'balancer_board_user(user_id)',
			'topic' => 'balancer_board_topic(topic_id)',
		);
	}

	function forum() { return $this->topic()->forum(); }

	function where()
	{
		return array_merge(parent::where(), array(
			'owner_id' => $this->user_id(),
			'topic_id' => $this->topic_id(),
		));
	}

	function pre_show()
	{
		template_noindex();
		return false;
	}

	function can_read() { return $this->forum()->can_read(); }
}
