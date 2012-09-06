<?php

class wrk_mauth_login extends bors_page
{
	function title() { return ec('Аутентификация'); }

	function template() { return 'default/simple.html'; }

	function body_data()
	{
		return array_merge(parent::body_data(), array(
			'ref' => bors()->client()->referer(),
		));
	}

	function access() { return $this; }
	function can_action() { return true; }
	function can_read() { return true; }

	static function _domains()
	{
		return array(
			'wrk.ru' => 'http://www.balancer.ru/login/',
			'balancer.ru' => 'http://www.airbase.ru/login/',
			'airbase.ru' => 'http://www.wrk.ru/login/',
		);
	}

	function on_action_auth($data)
	{
		if(empty($data['login']))
			return bors_message('Не указано имя пользователя (логин)');

		if(empty($data['password']))
			return bors_message('Не указан пароль');

		$me = balancer_board_user::do_login($data['login'], $data['password'], false);

		if(!is_object($me))
			return bors_message($me);

		// Авторизовались в этом домене успешно. Переходим к следующему.
		// Начинаем авторизацю _всегда_ с wrk.ru!

		$sig  = bors()->request()->data('sig');
		$host = bors()->server()->host();
		$ref  = bors()->request()->data('ref');
		if(!$ref)
			$ref  = bors()->client()->referer();

		if(preg_match('!^http://wrk.ru/login/!', $ref))
			$ref = NULL;

		if($host == 'wrk.ru' && !$sig)
		{
			// Это только что введённые данные формы авторизации. Начинаем цикл авториза.
			$auth = object_new_instance('wrk_mauth', array(
				'user_id' => $me->id(),
				'signature' => md5($data['login'].microtime(true)),
			));

			$sig = $auth->signature();
		}

		$domains = $this->_domains();
		$next = $domains[$host];

		return go($next.'?sig='.$sig.'&ref='.$ref);
	}

	function pre_show()
	{
		$sig = bors()->request()->data('sig');
		$host = bors()->server()->host();
		$ref  = bors()->client()->referer();

		if(preg_match('!^http://wrk.ru/login/!', $ref))
			$ref = NULL;

		// Если это вызов авторизации на одном из промежуточных доменов.
		// запоминаем реферер и идём в начало цикла.
		if($host != 'wrk.ru' && !$sig)
			return go('http://wrk.ru/login/?ref='.$ref);

		if($host == 'wrk.ru' && !$sig)
		{
			// Это начало цикла. Выводим страницу.
			return false;
		}

		$auth = bors_find_first('wrk_mauth', array(
			'signature' => $sig,
		));

		if($host == 'wrk.ru' && $sig)
		{
			// Это конец цикла. Переходим туда, откуда пришли.
			// Сперва - чистим авторизационную куку.
			if($auth)
				$auth->delete();
			return go($ref ? $ref : 'http://wrk.ru/');
		}

		// Иначе - это промежуточная авторизация на домене.

		if(!$auth)
			return bors_message(ec('Ошибка циклической авторизации в домене $host'));

		$sig = $auth->signature();
		$user_id = $auth->user_id();
		$user = bors_load('balancer_board_user', $user_id);
		$user->cookie_hash_set();

		$domains = $this->_domains();
		$next = $domains[$host];

		return go($next.'?sig='.$sig.'&ref='.$ref);
	}
}
