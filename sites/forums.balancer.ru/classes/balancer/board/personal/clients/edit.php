<?php

class balancer_board_personal_clients_edit extends balancer_board_page
{
	var $title_ec = 'Профили браузеров-клиентов';
	var $description_ec = 'Настройки конкретных браузеров и компьютеров пользователя';

	function pre_show()
	{
		if($this->client()->user_id() != bors()->user_id())
			return go('http://forums.balancer.ru/personal/clients/');

		return parent::pre_show();
	}

	function client()
	{
		return bors_load('balancer_board_user_client_profile', $this->id());
	}
}
