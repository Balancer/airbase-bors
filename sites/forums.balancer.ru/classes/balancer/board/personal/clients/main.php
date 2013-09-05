<?php

class balancer_board_personal_clients_main extends balancer_board_page_unbox
{
	var $title_ec = 'Профили браузеров-клиентов';
	var $description_ec = 'Настройки конкретных браузеров и компьютеров пользователя';
	var $access_engine = 'balancer_board_access_personal';

	function clients()
	{
		$profiles = bors_find_all('balancer_board_user_client_profile', array(
			'user_id' => bors()->user_id(),
		));

		foreach($profiles as $p)
			$p->set_attr('is_active', $p->cookie_hash() == @$_COOKIE['client_profile_hash']);

		return $profiles;
	}

	function new_title()
	{
		return ec('Профиль ').(bors_count('balancer_board_user_client_profile', array('user_id' => bors()->user_id()))+1);
	}

	function on_action_append($data)
	{
		$profile = bors_new('balancer_board_user_client_profile', array(
			'user_id' => bors()->user_id(),
			'title' => $data['new_title'],
			'cookie_hash' => md5(rand()),
		));

		return go($profile->admin_url());
	}
}
