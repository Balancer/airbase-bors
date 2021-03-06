<?php

class airbase_board_forum_prss extends base_rss
{
	function title() { return ec('Новые сообщения на форуме ').$this->forum()->title(); }
	function forum() { return bors_load('balancer_board_forum', $this->id()); }

	function main_url() { return $this->forum()->url(); }

	function can_be_empty() { return false; }
	function is_loaded() { return (bool) $this->forum(); }

	function pre_show()
	{
		if($this->forum()->can_read())
			return false;

		return bors_message(ec('У Вас нет доступа к этому ресурсу'));
	}

	function rss_items()
	{
//		bors_debug::syslog('rss-catch', "canr=".$this->forum()->can_read().", is_public_access=".$this->forum()->is_public_access());

		$tids = $this->db('AB_FORUMS')->select_array('topics', 'DISTINCT(id)', array(
			'last_post>' => time() - 86400,
			'forum_id' => $this->id(),
		));

		return bors_find_all('balancer_board_post', array(
			'posted>' => time() - 86400,
			'order' => '-posted',
			'topic_id IN' => $tids,
		));
	}

	function rss_url() { return $this->forum()->url(); }
	function url() { return "http://".@$_SERVER['HTTP_HOST']."/forum/{$this->id()}/posts-rss.xml"; }

	// Очистка только чисто таймаутная. А то при частых ответах на форумах
	// кеш практически не работает, всегда сбрасывается.
	function cache_static() { return $this->forum()->is_public_access() && config('static_forum') ? rand(300, 600) : 0; }
//	function cache_groups() { return "airbase-board-forum-{$this->id()}"; }
}
