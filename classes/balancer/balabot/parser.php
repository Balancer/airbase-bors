<?php

class balancer_balabot_parser extends bors_object
{
	var $data;
	var $from;
	var $message;

	function do_work($data)
	{
		$this->data = $data;

//		echo "Пытаемся обработать сообщение".$this." с данными ".print_r($data, true);
		$this->from = $data['payload']['from'];
		$this->message = trim($data['payload']['body']);

		if(preg_match('/^(\w+)$/', bors_lower($this->message), $m)
				&& method_exists($this, $method = 'do_'.$m[1]))
			return $this->$method($m);

		if(preg_match('/^(\w+)\s+(.+)$/s', bors_lower($this->message), $m)
				&& method_exists($this, $method = 'do_'.$m[1]))
			return $this->_reenter($method);

		$random_messages = file(BORS_LOCAL.'/data/balabot/random-responce.txt');

		$answer = $random_messages[rand(0, count($random_messages)-1)];

		$client= new GearmanClient();
		$client->addServer();

		$data = array(
			'to' => $this->from,
			'message' => $answer,
		);

		$client->doBackground('balabot.jabber.send', serialize($data));

		debug_hidden_log('balabot_talks', "{$this->from} <= $answer", false);
	}

	function _reenter($method)
	{
		if(preg_match('/^(\w+)\s+(.+)$/s', $this->message, $m)
				&& method_exists($this, $method = 'do_'.$m[1]))
			return $this->$method($m);

		debug_hidden_log('balabot_error', 'Incorrect reenter for '.$this->message);
		return $this->send('Внутренняя ошибка. Администратор извещён о детаях');
	}

	function do_help()
	{
		$help = file_get_contents(BORS_LOCAL.'/data/balabot/help.txt');
		return $this->send($help);
	}

	function do_login($match)
	{
		@list($login, $password) = @preg_split("/\s+/", trim(@$match[2]));
		if(!$login || !$password)
			return $this->send("Ошибка формата аутентификации. Нужно писать:\nlogin <имя> <пароль>\n\nЭта команда привяжет ваш текущий JID к указанному аккаунту форумов Авиабазы и позволит в будущим отправлять сообщения командой post.");

		$user = balancer_board_user::do_login($login, $password, false);
		if(!is_object($user))
			return $this->send($user);

		if(!preg_match('/^([\w\.\-]+@[\w\.\-]+)/', $this->from, $m))
			return $this->send('Ошибка формата JID: '.$this->from);

		$user->set_jabber($m[1], true);

		return $this->send('Вы успешно привязали аккаунт '.$user->title().' и JID '.$m[1]);
	}

	function do_post($match)
	{
		$message = trim(@$match[2]);
		if(!$message)
			return $this->send("Команда отсылки нового сообщения на форум. Нужно писать (в любом варианте):

post текст
post [заголовок] текст
post [заголовок] *ключевые *слова текст

Эта команда позволяет отправить сообщение на форум. Если будет указан заголовок, то будет создана новая тема в подходящем фруме. Если заголовок не указан, то сообщение будет размещено в одном из имеющихся топиков, тоже по автоматическому выбору Балабота. В теле сообщения используется обычная форумная BB-Code разметка. Понимаются также #тэги в Твиттер-стиле внутри сообщений.");

		if(!preg_match('/^([\w\.\-]+@[\w\.\-]+)/', $this->from, $m))
			return $this->send('Ошибка формата JID: '.$this->from);

		$user = bors_find_first('balancer_board_user', array('jabber' => $m[1], 'order' => '-last_visit_time'));
		if(!$user)
			return $this->send("Не могу найти зарегистрированного пользователя с JID: {$this->from}\n\nПопробуйте сперва авторизоваться командой login <имя> <пароль>");

		$message = trim($match[2]);

		if(preg_match('/^\[(.+?)\]\s+(.+)$/s', $message, $m))
		{
			$title = $m[1];
			$message  = $m[2];
		}
		else
			$title = NULL;

		$tags = array();
		if(preg_match('/^((\*\S+( |$))+)(.+?)$/ms', $message, $m))
		{
			$raw_tags = $m[1];
			$message = $m[4];
			if(preg_match_all('/\*(\S+)( |$)/', $raw_tags, $matches))
				foreach($matches[1] as $tag)
					$tags[] = $tag;
		}

		if(preg_match_all('/ #(\S+)( |\.|,|$)/m', $message, $matches))
			foreach($matches[1] as $tag)
				$tags[] = $tag;

		$message = trim($message);

		$forum_id = 12; // За жизнь

		if($title)
		{
			if($tags)
				$forum_id = common_keyword::best_forum(join(',', $tags), 0);

			if(!$forum_id)
				$forum_id = 12; // За жизнь

			$forum = bors_load('balancer_board_forum', $forum_id);

//			return $this->send("Ваше сообщение будет размещено в форуме {$forum->title()} - {$forum->url()}");

			$topic = balancer_board_topic::create($forum, $title, $message, $user, join(', ', $tags), true);
			return $this->send("Ваше сообщение было размещено в новой теме {$topic->title()} по адресу {$topic->url()} на форуме {$forum->title()} [{$forum->url()}]");
		}

		if($tags)
			$topic_id = common_keyword::best_topic(join(',', $tags), 0, true);

		if(!$topic_id)
			$topic_id = 66326; // Флуд, флейм, кто что делает...

		$topic = bors_load('balancer_board_topic', $topic_id);

//		return $this->send("Ваше сообщение будет размещено в теме {$topic->title()} - {$topic->url()}");

		$post = balancer_board_post::create($topic, $message, $user, join(', ', $tags), true);
		return $this->send("Ваше сообщение было размещено в теме {$post->topic()->title()} по адресу {$post->url_for_igo()}");
	}

	function send($message)
	{
		$client= new GearmanClient();
		$client->addServer();

		$data = array(
			'to' => $this->from,
			'message' => htmlspecialchars($message),
		);

		$client->doBackground('balabot.jabber.send', serialize($data));
		debug_hidden_log('balabot_talks', "{$this->from} <= $message", false);
	}
}

/*
Array
(
    [worker_class_name] => balancer_balabot_parser
    [payload] => Array
        (
            [to] => balabot@balancer.ru
            [from] => balancer@balancer.ru/Pidgin, PF
            [id] => purpledfbd37e5
            [type] => chat
            [xml:lang] =>
            [body] => kcmsdclkm
            [delayDesc] =>
            [chatState] => active
        )

)
*/
