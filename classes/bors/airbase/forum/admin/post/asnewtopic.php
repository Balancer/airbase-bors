<?php

class airbase_forum_admin_post_asnewtopic extends base_page
{
	function config_class() { return 'airbase_forum_admin_config'; }
	function title() { return ec('Вынесение сообщения в виде новой темы'); }
	function nav_name() { return ec('в новую тему'); }
	function post() { return object_load('forum_post', $this->id()); }
	function target_forum_id() { return $this->post()->topic()->forum_id(); }
	function new_topic_title() { return $this->post()->topic()->title(); }
	function dont_move_with_tree() { return false; }
	function access_engine() { return 'airbase_forum_admin_access_split'; }
	
	function pre_action($data)
	{
		if($data['original_topic_id'] != $this->post()->topic_id())
			return bors_message(ec('Это сообщение уже было перенесено, пока Вы готовились к той же операции'));
	
		if($this->check_data($data) === true)
			return true;
			
		$old_topic = $this->post()->topic();
		$new_topic = object_new('forum_topic');
		$new_topic->set_forum_id($data['target_forum_id'], true);
		$new_topic->set_title($data['new_topic_title'], true);
		$new_topic->new_instance();
		$new_topic->store();

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
