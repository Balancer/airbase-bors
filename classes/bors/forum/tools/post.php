<?php

class forum_tools_post extends base_page
{
	function class_file() { return __FILE__; }
	function can_be_empty() { return true; }
	
	function parents() { return array('forum_post://'.$this->id()); }

	function title() { return ec('Инструменты сообщения'); }
	function nav_name() { return ec('инструменты'); }

	function template() { return 'templates/forum/common.html'; }

	private $post = false;
	function post()
	{
		if($this->post === false)
			$this->post = object_load('forum_post', $this->id());

		return $this->post;
	}

	function owner_id()
	{
		return $this->post()->owner_id();
	}

	function access() { return $this; }

	function can_read() { templates_noindex(); return bors()->user(); }

	function can_action()
	{
		$me = bors()->user();
        if(!$me || !$me->id())
            return false;
	
		if($me->id() == 10000)
			return true;
	
		if(!$me->group()->can_move())
			return false;
	
		if(in_array(@$_GET['act'], array('owner_change')))
			return $this->post()->owner_id() == 1;
		
		return true;
	}

	function on_action_owner_change($data)
	{
		$owner_id = @$data['owner_id'];
		if(!$owner_id)
			return bors_message(ec('Не задан ID пользователя'));
		
		$owner = class_load(config('user_class'), $owner_id);
		if(!$owner)
			return bors_message(ec('Неверный ID пользователя'));

		if(empty($data['author_name']))
			$data['author_name'] = $owner->title();																							

		$post = object_load('forum_post', $this->id());
		$post->set_owner_id($owner->id(), true);
		$post->set_author_name($owner->title(), true);
	
		return go($this->url());
	}
}
