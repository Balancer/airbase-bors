<?php

// Нужно для модификатора в шаблоне
require_once('inc/clients/geoip-place.php');

class balancer_board_admin_users_twinks extends balancer_board_admin_page
{
	var $title = 'Обнаруженные твинки пользователей';
	function config_class() { return 'balancer_board_admin_config'; }
	function can_read() { template_noindex(); return ($me=bors()->user()) ? $me->is_coordinator() : false; }
	function template() { return 'xfile:forum/page.html'; }

	function body_data()
	{
		$utmxs = driver_mysql::factory(config('punbb.database'))->select_array('users', 'utmx',
			[
				'utmx<>' => '',
				'group' => 'utmx',
				'having' => 'COUNT(*) > 1',
				'order' => 'MAX(`registered`) DESC',
			]
		);

		$users = array();
		foreach($utmxs as $utmx)
		{
			$us = bors_find_all('balancer_board_user', ['utmx' => $utmx, 'order' => '-create_time']);
			$users[$utmx] = [
				'list' => $us,
				'first_name' => $us[count($us)-1]->title(),
			];
		}

		return compact('users');
	}
}
