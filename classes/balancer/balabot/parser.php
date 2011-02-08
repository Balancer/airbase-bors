<?php

class balancer_balabot_parser extends bors_object
{
	var $data;
	function do_work($data)
	{
		$this->data = $data;

//		echo "Пытаемся обработать сообщение".$this." с данными ".print_r($data, true);
		$from = $data['payload']['from'];
		$message = $data['payload']['body'];

		if(preg_match('/^(\w+)$/', bors_lower(trim($message)), $m)
				&& method_exists($this, $method = 'do_'.$m[1]))
			return $this->$method($m);

		if(preg_match('/^(\w+)\s+(.+)$/s', bors_lower(trim($message)), $m)
				&& method_exists($this, $method = 'do_'.$m[1]))
			return $this->$method($m);

		$random_messages = file(BORS_LOCAL.'/data/balabot/random-responce.txt');

		$answer = $random_messages[rand(0, count($random_messages)-1)];

		$client= new GearmanClient();
		$client->addServer();

		$data = array(
			'to' => $from,
			'message' => $answer,
		);

		$client->doBackground('balabot.jabber.send', serialize($data));

		debug_hidden_log('balabot_talks', "$from <= $answer", false);
	}

	function do_help()
	{
		$help = file_get_contents(BORS_LOCAL.'/data/balabot/help.txt');
		return $this->send($help);
	}

	function do_login($match)
	{
		@list($login, $password) = @preg_split("/\s+/", trim($match[2]));
		if(!$login || !$password)
			return $this->send("Ошибка формата аутентификации. Нужно писать:\nlogin <имя> <пароль>");

		return $this->send('Ещё не реализовано');
	}

	function send($message)
	{
		$from = $this->data['payload']['from'];

		$client= new GearmanClient();
		$client->addServer();

		$data = array(
			'to' => $from,
			'message' => htmlspecialchars($message),
		);

		$client->doBackground('balabot.jabber.send', serialize($data));
		debug_hidden_log('balabot_talks', "$from <= $message", false);
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
