<?php

class balancer_board_actor_topic extends bors_page
{
	function public_title()
	{
		$post  = $this->args('object');
		$user  = $this->args('user');
		$topic = $post->topic();
		return "В теме {$topic->titled_link_ex(array('page' => 'new'))} есть <a href=\"{$post->url_in_container()}\">новое сообщение</a> от пользователя {$post->owner()->titled_link()}";
	}

	function personal_title()
	{
		$post  = $this->args('object');
		$user  = $this->args('user');
		$topic = $post->topic();
		return "Обновлена тема {$topic->titled_link_ex(array('page' => 'new'))}";
	}

	function public_text()
	{
		$post  = $this->args('object');
		return strip_text($post->body(), 256);
	}

	function personal_text()
	{
		$post  = $this->args('object');
		$user  = $this->args('user');
		$topic = $post->topic();
		return "<b><a href=\"{$post->url_in_container()}\">{$post->owner()->title()}</a>: </b>".strip_text($post->body(), 256);
	}

	function color() { return 'topic'; }
}
