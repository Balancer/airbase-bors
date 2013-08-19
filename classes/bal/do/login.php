<?php

class bal_do_login extends balancer_board_page
{
	var $title = 'Вход';
	function parents() { return array('http://www.balancer.ru/'); }

	function body_data()
	{
		jquery::load();

		$user = balancer_board_user::do_login(@$_GET['login'], @$_GET['password'], false, false);

		if(!is_object($user))
		{
			return array_merge(parent::body_data(), array(
				'error' => $user,
			));
		}

		$domains = config('balancer_board_domains');
		$hactions = array();
		$ids = array();

		foreach($domains as $domain)
		{
			$ids[$domain] = 'id_'.str_replace('.', '_', $domain);

			$hactions[$domain] = bal_user_haction::add(
				bors()->user_id(),
				'bal_users_helper',
				'domain_ajax_login',
				120,
				array(
					'domain' => $domain,
					'expired' => time() + 86400*30,
					'cookies' => array(
						'user_id' => $user->id(),
						'cookie_hash' => $user->cookie_hash(),
						'isa' => $user->is_admin(),
					)
				)
			);
		}

		bors()->changed_save();
		usleep(200000);

		return array_merge(parent::body_data(), array(
			'domains' => $domains,
			'hactions' => $hactions,
			'ids' => $ids,
		));
	}
}
