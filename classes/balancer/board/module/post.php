<?php

// Тело топика со всеми заголовками, подписями и т.п.

class balancer_board_module_post extends bors_module
{
	function post()
	{
		if($this->__havefc())
			return $this->__lastc();

		if($p = $this->arg('post'))
			return $this->__setc($p);

		return $this->__setc(bors_load('balancer_board_post', $this->arg('post_id')));
	}

	function topic()
	{
		if($this->__havefc())
			return $this->__lastc();

		if($t = $this->arg('topic'))
			return $this->__setc($t);

		return $this->__setc($this->post()->topic());
	}

	function forum()
	{
		if($this->__havefc())
			return $this->__lastc();

		if($f = $this->arg('forum'))
			return $this->__setc($f);

		return $this->__setc($this->topic()->forum());
	}

	function body_template()
	{
		$post = $this->post();

		if($post->is_deleted())
			return dirname(__FILE__).'/post.deleted.html';

		if($post->is_hidden())
			return dirname(__FILE__).'/post.hidden.html';

    	if(!$this->forum()->can_read())
			return dirname(__FILE__).'/post.denied.html';

		return dirname(__FILE__).'/post.html';
	}

	function body_data()
	{
		return array(
			'post' => $this->post(),
			'topic' => $this->topic(),
			'forum' => $this->forum(),
		);
	}
}