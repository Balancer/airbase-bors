<?php

require_once('inc/texts.php');

class airbase_pages_ro extends bors_page
{
	var $title = 'Форумы временно недоступны';
	var $template = 'xfile:bootstrap/index.tpl';

	function pre_show()
	{
		twitter_bootstrap::load();
		return parent::pre_show();
	}

	function body_data()
	{
		$chat_messages = json_decode(blib_http::get('http://vault.balancer.ru/api/chat/last.json?limit=10'), true);
		$forum_messages = json_decode(blib_http::get('http://vault.balancer.ru/api/forum/last.json?limit=7'), true);
		return compact('chat_messages', 'forum_messages');
	}
}
