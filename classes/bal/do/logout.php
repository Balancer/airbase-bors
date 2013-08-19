<?php

class bal_do_logout extends balancer_board_page
{
	var $title = 'Выход';
	function parents() { return array('http://www.balancer.ru/'); }

	function body_data()
	{
		jquery::load();

		$domains = config('balancer_board_domains');
		$hactions = array();
		$ids = array();

		foreach($domains as $domain)
		{
			$ids[$domain] = 'id_'.str_replace('.', '_', $domain);

			$hactions[$domain] = bal_user_haction::add(
				bors()->user_id(),
				'bal_users_helper',
				'domain_ajax_logout',
				120,
				array(
					'domain' => $domain,
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
