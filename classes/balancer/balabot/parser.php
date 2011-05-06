<?php

class balancer_balabot_parser extends bors_object
{
	var $data;
	var $from;
	var $message;

	function do_work($data)
	{
//		debug_hidden_log('users_clients_balabot_parser', print_r($data['payload'], true));

		$this->data = $data;

//		print_r($data);

//		echo "Пытаемся обработать сообщение".$this." с данными ".print_r($data, true);
		$this->from = $data['payload']['from'];
		$this->message = trim($data['payload']['body']);

		debug_hidden_log('balabot_talks', "{$this->from}: {$this->message}", false);

		if(preg_match('/^\?\s+(.+)$/s', $this->message, $m))
			return $this->do__search(trim($m[1]));

		if(preg_match('/^#(\d+)\s+(.+)$/s', $this->message, $m))
			return $this->do___answer($m[1], trim($m[2]));

		if(preg_match('/^\*\S+/s', $this->message, $m))
			return $this->do__post($this->message);

		$message_lower_case = bors_lower($this->message);

		if(preg_match('/^(\w+)$/s', $message_lower_case, $m)
				&& method_exists($this, $method = 'do__'.$m[1]))
			return $this->$method();

		// Тупой костыль для перепосылки данных без lowercase
		if(preg_match('/^(\w+)\s+(.+)$/s', $message_lower_case, $m)
				&& method_exists($this, $method = 'do__'.$m[1]))
			return $this->_reenter($method);

		$this->do__random();
	}

	// Тупой костыль для перепосылки данных без lowercase
	function _reenter($method)
	{
		if(preg_match('/^(\w+)\s+(.+)$/s', $this->message, $m)
				&& method_exists($this, $method = 'do__'.$m[1]))
			return $this->$method(trim($m[2]));

		debug_hidden_log('balabot_error', 'Incorrect reenter for '.$this->message);
		return $this->send('Внутренняя ошибка. Администратор извещён о детаях');
	}

	function user()
	{
		if(!preg_match('/^([\w\.\-]+@[\w\.\-]+)/', $this->from, $m))
		{
			$this->send('Ошибка формата JID: '.$this->from);
			debug_hidden_log('balabot_error', 'Ошибка формата JID: '.$this->from);
			return NULL;
		}

		return bors_find_first('balancer_board_user', array('jabber' => $m[1], 'order' => '-last_visit_time'));
	}

	function parse_val($val)
	{
		switch(strtolower($val))
		{
			case '1':
			case '-1':
			case 'true':
			case 'on':
				return true;
				break;
			case '0':
			case 'false':
			case 'off':
				return false;
				break;
			default:
				return $val;
				break;
		}
	}

	function send($message)
	{
		if(preg_match('/vedmed1969@livejournal.com|mixer@conference.jabber.ru/', $this->from))
			return;

		$client= new GearmanClient();
		$client->addServer();

		$data = array(
			'to' => $this->from,
			'message' => htmlspecialchars($message),
		);

		$client->doBackground('balabot.jabber.send', serialize($data));
		debug_hidden_log('balabot_talks', "BalaBOT: $message", false);
	}

	//**************************************************************************************
	//
	//      Начало обработчиков
	//
	//**************************************************************************************

	function do___answer($post_id, $message)
	{
		$me = $this->user();
		if(!$me)
			return $this->send("Не могу найти зарегистрированного пользователя с JID: {$this->from}\n\nПопробуйте сперва авторизоваться командой login <имя> <пароль>");

		$answer_post = bors_load('balancer_board_post', $post_id);
		if(!$answer_post)
			return $this->send('Не могу найти сообщение #'.$post_id);

		$topic = $answer_post->topic();

//		var_dump($this->data['payload']);

		$post = balancer_board_post::create($topic, $message, $me, '', false, array(
			'blog_source_class' => 'balancer_balabot_source_xmpp',
			'answer_to_id' => $answer_post->id(),
			'answer_to_user_id' => $answer_post->owner()->id(),
			'poster_ua' => @$this->data['payload']['id'],
			'poster_ip' => @$this->data['payload']['from'],
		));

		$x = bors_find_first('balancer_board_users_subscription', array('user_id' => $me->id(), 'topic_id' => $topic->id()));
		if(!$x)
		{
			$x = object_new_instance('balancer_board_users_subscription', array('user_id' => $me->id(), 'topic_id' => $topic->id()));
			$subs = "Также Вы были подписаны на новые ответы в этой теме";
		}
		else
			$subs = '';

		return $this->send("Ваше сообщение #{$post->id()} было размещено в теме {$post->topic()->title()} по адресу {$post->url_for_igo()}. $subs");
	}

	function do__help()
	{
		$help = file_get_contents(BORS_LOCAL.'/data/balabot/help.txt');
		return $this->send($help);
	}

	function do__login($message)
	{
		@list($login, $password) = @preg_split("/\s+/", $message);
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

	function do__p($message) { $this->do__post($message); }
	function do__post($message)
	{

		if(!$message)
			return $this->send("Команда отсылки нового сообщения на форум. Нужно писать (в любом варианте):

post текст
post [заголовок] текст
post [заголовок] *ключевые *слова текст

Эта команда позволяет отправить сообщение на форум. Если будет указан заголовок, то будет создана новая тема в подходящем фруме. Если заголовок не указан, то сообщение будет размещено в одном из имеющихся топиков, тоже по автоматическому выбору Балабота. В теле сообщения используется обычная форумная BB-Code разметка. Понимаются также #тэги в Твиттер-стиле внутри сообщений.

Возможно также сокращённое имя команды, просто «p»:

P Всем привет!
");

		if(!preg_match('/^([\w\.\-]+@[\w\.\-]+)/', $this->from, $m))
			return $this->send('Ошибка формата JID: '.$this->from);

		$user = $this->user();
		if(!$user)
			return $this->send("Не могу найти зарегистрированного пользователя с JID: {$this->from}\n\nПопробуйте сперва авторизоваться командой login <имя> <пароль>");

		if(preg_match('/^\[(.+?)\]\s+(.+)$/s', $message, $m))
		{
			$title = $m[1];
			$message  = $m[2];
		}
		else
			$title = NULL;


		$tags = array();
		if(preg_match('/^((\*\S+( |\n))+)(.+?)$/s', $message, $m))
		{
			$raw_tags = $m[1];
			$message = $m[4];
			if(preg_match_all('/\*(\S+)( |$)/', $raw_tags, $matches))
				foreach($matches[1] as $tag)
					$tags[] = str_replace('_', ' ', $tag);
		}

//		return $this->send("Test: post $message");

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

			$topic = balancer_board_topic::create($forum, $title, $message, $user, join(', ', $tags), false, array(
				'poster_ua' => @$this->data['payload']['id'],
				'poster_ip' => @$this->data['payload']['from'],
			));

			$post = $topic->first_post();
			return $this->send("Ваше сообщение #{$post->id()} было размещено в новой теме {$topic->title()} по адресу {$topic->url()} на форуме {$forum->title()} [{$forum->url()}]");
		}

		$topic_id = 66326; // Флуд, флейм, кто что делает...
		if($tags)
			$topic_id = common_keyword::best_topic(join(',', $tags), 66326, false);

		$topic = bors_load('balancer_board_topic', $topic_id);

//		return $this->send("Ваше сообщение будет размещено в теме {$topic->title()} - {$topic->url()}");

		$post = balancer_board_post::create($topic, $message, $user, join(', ', $tags), true, array(
			'blog_source_class' => 'balancer_balabot_source_xmpp',
			'poster_ua' => @$this->data['payload']['id'],
			'poster_ip' => @$this->data['payload']['from'],
		));

		return $this->send("Ваше сообщение #{$post->id()} было размещено в теме {$post->topic()->title()} по адресу {$post->url_for_igo()}");
	}

	function do__random()
	{
		$random_messages = file(BORS_LOCAL.'/data/balabot/random-responce.txt');
		$answer = $random_messages[rand(0, count($random_messages)-1)];
		return $this->send($answer);
	}

	function do__s($friend_name)
	{
		$me = $this->user();
		if(!$me)
			return $this->send("Не могу найти зарегистрированного пользователя с JID: {$this->from}\n\nПопробуйте сперва авторизоваться командой login <имя> <пароль>");

		if(preg_match('/^#(\d+)$/', $friend_name, $m))
		{
			// Это подписка на тему
			$post_id = $m[1];

			$post = bors_load('balancer_board_post', $post_id);
			if(!$post)
				return $this->send('Не могу найти сообщение #'.$post_id);

			$topic = $post->topic();
			$x = bors_find_first('balancer_board_users_subscription', array('user_id' => $me->id(), 'topic_id' => $topic->id()));
			if($x)
				return $this->send("Вы уже были ранее подписаны на тему {$topic->title()} // {$topic->url()}");

			$x = object_new_instance('balancer_board_users_subscription', array('user_id' => $me->id(), 'topic_id' => $topic->id()));
			return $this->send("Вы подписались на тему {$topic->title()}. Теперь Вы будете получать все ответы в эту тему // {$topic->url()}");
		}

		$friend = balancer_board_user::find_by_all_names($friend_name);
		if(!$friend)
			return $this->send("Не могу найти зарегистрированного пользователя с именем '{$friend_name}'");

		$x = object_new_instance('balancer_board_users_friend', array('user_id' => $me->id(), 'friend_id' => $friend->id()));

		$this->send("Вы записали пользователя {$friend->title()} в свои друзья. Теперь Вы будете получать его блоговые сообщения");
	}

	function do__search($query)
	{
		$topics = bors_find_all('balancer_board_topic', array('subject LIKE "%'.addslashes($query).'%"', 'order' => '-last_post_create_time', 'limit' => 20));
		$text = array();
		foreach($topics as $t)
			$text[] = "[".date('Y-m-d H:i', $t->modify_time())."] ".trim($t->title())." // %{$t->id()} ".wrk_go::make_short_url($t);

		if(empty($text))
			$text[] = 'Ничего не найдено';

		return $this->send("Найденные по запросу подстроки '$query' темы:\n".join("\n", $text));

		common_keyword::keyword_search_reindex($query, true);
		$kw = common_keyword::loader($query);
		$bindings = bors_find_all('common_keyword_bind', array(
			'keyword_id' => $kw->id(),
			'target_class_name IN' => array('balancer_board_blog', 'forum_blog'),
			'order' => 'target_create_time',
			'limit' => 25,
		));

		if(!$bindings)
			return $this->send($query.' не найдено');

		$text = array();
		foreach($bindings as $b)
		{
			if(!($x = $b->target()))
				continue;
			if($post = $x->get('post'))
				$x = $post;

			$text[] = strip_text($x->source(), 256, '…', true).' // #'.$x->id().' '.$x->url_in_container();
		}

		$this->send(join("\n\n", $text));
	}

	function do__set($expr)
	{
		if(preg_match('/^(\w+)\s+(.+)$/', $expr, $m))
		{
			$var = $m[1];
			$val = $this->parse_val($m[2]);
		}
		elseif(preg_match('/^(\w+)$/', $expr, $m))
		{
			$var = $expr;
			$val = true;
		}
		else
			return $this->send('Непонятный формат команды set');

		$me = $this->user();
		if(!$me)
			return $this->send("Не могу найти зарегистрированного пользователя с JID: {$this->from}\n\nПопробуйте сперва авторизоваться командой login <имя> <пароль>");

		switch($var)
		{
			case 'xmpp_notify':
				$me->set_xmpp_notify_enabled($val, true);
				break;
			case 'xmpp_notify_new':
				$me->set_xmpp_notify_new($val, true);
				break;
			case 'xmpp_notify_score':
				$me->set_xmpp_notify_score($val, true);
				break;
			case 'xmpp_notify_best':
				$me->set_xmpp_notify_best($val, true);
				break;
			case 'xmpp_notify_reputation':
				$me->set_xmpp_notify_reputation($val, true);
				break;
			default:
				return $this->send('Незвестный параметр '.$var);
		}

		$this->send("Параметр $var изменён в значение ".($val ? 'on' : 'off'));
	}

	function do__test($message)
	{
		$user = $this->user();
		if(!$user)
			return $this->do__random();

		$this->send("Тест\n=============\n");

		$this->send('text: "'.$this->data.'"');
	}

	function do__u($friend_name)
	{
		$me = $this->user();
		if(!$me)
			return $this->send("Не могу найти зарегистрированного пользователя с JID: {$this->from}\n\nПопробуйте сперва авторизоваться командой login <имя> <пароль>");

		if(preg_match('/^#(\d+)$/', $friend_name, $m))
		{
			// Это отписка от темы
			$post_id = $m[1];

			$post = bors_load('balancer_board_post', $post_id);
			if(!$post)
				return $this->send('Не могу найти сообщение #'.$post_id);

			$topic = $post->topic();
			$x = bors_find_first('balancer_board_users_subscription', array('user_id' => $me->id(), 'topic_id' => $topic->id()));
			if($x)
			{
				$x->delete();
				return $this->send("Вы отписались от темы {$topic->title()}. // {$topic->url()}");
			}

			return $this->send("Вы не были подписаны на тему {$topic->title()}. // {$topic->url()}");
		}


		$friend = balancer_board_user::find_by_all_names($friend_name);
		if(!$friend)
			return $this->send("Не могу найти зарегистрированного пользователя с именем '{$friend_name}'");

		$x = bors_find_first('balancer_board_users_friend', array('user_id' => $me->id(), 'friend_id' => $friend->id()));
		if($x)
			$x->delete();

		$this->send("Вы исключили пользователя {$friend->title()} из своих друзей.");
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
