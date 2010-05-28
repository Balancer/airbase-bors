<?php

// http://balancer.ru/support/post?tid=69886

class balancer_board_post_edit extends bors_page
{
	function config_class() { return 'balancer_board_config'; }

	function is_new_post() { return !$this->post_id(); }
	function is_new_topic() { return !$this->topic_id(); }

	static function id_prepare($id)
	{
		require_once('inc/http.php');
		$data = query_explode($id);
		$fid = intval(defval($data, 'fid'));
		$tid = intval(defval($data, 'tid'));
		$pid = intval(defval($data, 'pid'));

		return "{$fid}-{$tid}-{$pid}";
	}

	function init()
	{
		list($fid, $tid, $pid) = explode('-', $this->id());
		$this->set_forum_id($fid, false);
		$this->set_topic_id($tid, false);
		$this->set_post_id($pid, false);

		return parent::init();
	}

	function auto_objects()
	{
		return array(
			'forum' => 'balancer_board_forum(topic_id)',
			'topic' => 'balancer_board_topic(topic_id)',
			'post' => 'balancer_board_post(post_id)',
		);
	}

	function title()
	{
		if($this->post_id())
			return ec('Редактирование сообщения «').$this->post()->nav_name().ec('»');

		if($this->topic_id())
			return ec('Ответ в тему «').$this->topic()->nav_name().ec('»');

		if($this->forum_id())
			return ec('Создание новой темы');

		return '?';
	}

	function nav_name()
	{
		if($this->post_id())
			return ec('редактирование');

		if($this->topic_id())
			return ec('ответ');

		if($this->forum_id())
			return ec('новая тема');

		return '?';
	}

	function parents()
	{
		if($this->post_id())
			return array($this->post()->url());

		if($this->topic_id())
			return array($this->topic()->url());

		if($this->forum_id())
			return array($this->forum()->url());

		return array('http://forums.balancer.ru/');
	}
}
