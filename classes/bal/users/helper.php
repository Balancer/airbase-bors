<?php

/**
	Класс со всякими редко используемыми вещами, чтобы
	не загружать _user-класс
*/

//config_set('debug_redirect_trace', true);

class bal_users_helper extends bors_object
{
	function haction_domain_logout($attrs, $haction)
	{
		extract($attrs);

		$domains = config('balancer_board_domains');
		$domains[] = 'forums.airbase.ru';
		$domains[] = 'forums.balancer.ru';

		foreach(array('user_id', 'cookie_hash', 'isa') as $k)
		{
			SetCookie($k, NULL, 0, "/");
			SetCookie($k, NULL, 0, "/", $domain);
			SetCookie($k, NULL, 0, "/", '.'.$domain);
		}

		// Переходим к следующему домену или аллес.
		$next_domain = false;
		for($i=0; $i<count($domains); $i++)
		{
			if($domains[$i] == $domain)
			{
				if($i+1 < count($domains))
					$next_domain = $domains[$i+1];

				break;
			}
		}

		if($next_domain)
		{
			$haction->set_attr('need_save', true);
			$haction->set_actor_attributes(json_encode(array(
				'domain' => $next_domain,
				'redirect' => $redirect,
			)));

			return $haction->url_ex($next_domain);
		}

		return $redirect ? $redirect : 'http://forums.balancer.ru/';
	}

	function haction_domain_login($attrs, $haction)
	{
		extract($attrs);

		$domains = config('balancer_board_domains');

		foreach(array('user_id' => $this->id(), 'cookie_hash' => $cookie_hash, 'isa' => $is_admin) as $k => $v)
			SetCookie($k, $v, $expired, "/", '.'.$domain);

		// Переходим к следующему домену или аллес.
		$next_domain = false;
		for($i=0; $i<count($domains); $i++)
		{
			if($domains[$i] == $domain)
			{
				if($i+1 < count($domains))
					$next_domain = $domains[$i+1];

				break;
			}
		}

		if($next_domain)
		{
			$haction->set_attr('need_save', true);
			$haction->set_actor_attributes(json_encode(array(
				'domain' => $next_domain,
				'redirect' => $redirect,
				'cookie_hash' => $cookie_hash,
				'is_admin' => $is_admin,
				'expired' => $expired,
			)));

			return $haction->url_ex($next_domain);
		}

		return $redirect ? $redirect : 'http://forums.balancer.ru/';
	}

	function haction_set_client_profile($attrs, $haction)
	{
		extract($attrs);
		$domains = config('balancer_board_domains');

		$client = bors_load('balancer_board_user_client_profile', $profile_id);
		SetCookie('client_profile_hash', $client->cookie_hash(), time()+86400000, "/", '.'.$domain);

		// Переходим к следующему домену или аллес.
		$next_domain = false;
		for($i=0; $i<count($domains); $i++)
		{
			if($domains[$i] == $domain)
			{
				if($i+1 < count($domains))
					$next_domain = $domains[$i+1];

				break;
			}
		}

		if($next_domain)
		{
			$haction->set_attr('need_save', true);
			$haction->set_actor_attributes(json_encode(array(
				'domain' => $next_domain,
				'redirect' => $redirect,
				'profile_id' => $profile_id,
			)));

			return $haction->url_ex($next_domain);
		}

		return $redirect ? $redirect : 'http://forums.balancer.ru/personal/clients/';
	}

	static function all_flags($user, $where = array())
	{
		$posts = bors_find_all('forum_post2', array_merge($where, array(
			'*set' => 'COUNT(*) AS `total`',
			'poster_id' => $user->id(),
			'group' => '`poster_ip`',
			'limit' => 100,
			'order' => 'COUNT(*) DESC',
		)));

		$flags = array();
		foreach($posts as $post)
			$flags[] = bors_client::factory($post->poster_ip())->flag().'&nbsp;('.$post->total().')';

		return join(', ', $flags);
	}
}
