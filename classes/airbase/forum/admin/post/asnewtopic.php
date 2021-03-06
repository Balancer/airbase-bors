<?php

class airbase_forum_admin_post_asnewtopic extends bors_page
{
	function config_class() { return 'airbase_forum_admin_config'; }
	function title() { return ec('Вынесение сообщения в виде новой темы'); }
	function nav_name() { return ec('в новую тему'); }
	function post() { return bors_load('balancer_board_post', $this->id()); }
	function target_forum_id() { return $this->post()->topic()->forum_id(); }
	function new_topic_title()
	{
		$post = $this->post();
		if($blog = $post->blog())
			return $blog->title();

		return $post->topic()->title();
	}

	function new_topic_keywords_string()
	{
		$post = $this->post();
		if($blog = $post->blog())
			return $blog->keywords_string();

		return $post->topic()->keywords_string();
	}

//	function new_topic_description() { return ec('Перенос из темы «').$this->post()->topic()->title().ec('»'); }
	function dont_move_with_tree() { return false; }
	function access_engine() { return 'airbase_forum_admin_access_split'; }

	function pre_action(&$data)
	{
		if(!$this->post())
			return bors_message(ec('Хрень какая-то. Не найден постинг ').intval($this->id()));

		if($data['original_topic_id'] != $this->post()->topic_id())
			return bors_message(ec('Это сообщение уже было перенесено, пока Вы готовились к той же операции'));

		if($this->check_data($data) === true)
			return true;

		$old_topic = $this->post()->topic();
		$new_topic = bors_new('balancer_board_topic', array(
			'forum_id' => $data['target_forum_id'],
			'title' => $data['new_topic_title'],
			'description' => $data['new_topic_description'],
		));

		$new_topic->set_keywords_string($data['new_topic_keywords_string'], true);

		if($old_topic && $old_topic->id() != $new_topic->id())
		{
			balancer_board_action::add($new_topic, "Тема создана из {$old_topic->titled_link()}");
			balancer_board_action::add($old_topic, "Перенос в новую тему {$new_topic->titled_link()}");
		}

		if(empty($data['dont_move_with_tree']))
			$this->post()->move_tree_to_topic($new_topic->id());
		else
			$this->post()->move_to_topic($new_topic->id());

		return go($new_topic->url());
	}

	function check_value_conditions()
	{
		return array(
			'target_forum_id'         => ec("!=0|Не указан форум"),
			'new_topic_title'     => ec("!=''|Не задан заголовок новой темы"),
		);
	}
}
