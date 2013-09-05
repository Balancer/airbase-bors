<?php

class balancer_board_personal_clients_apply extends balancer_board_page
{
	function pre_show()
	{
		$domains = config('balancer_board_domains');
		$next_domain = $domains[0];

		$redirect = bors()->request()->referer();

		$haction = bal_user_haction::add(bors()->user_id(), 'bal_users_helper', 'haction_set_client_profile', 120, array(
			'domain' => $next_domain,
			'redirect' => 'http://forums.balancer.ru/personal/clients/',
			'profile_id' => $this->id(),
		));

		return go_message(ec('Настройка браузера на профиль произведена'), array(
			'go' => $haction->url($next_domain),
			'error' => false,
		));
	}
}
