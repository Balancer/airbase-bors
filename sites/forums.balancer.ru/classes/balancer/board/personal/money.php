<?php

class balancer_board_personal_money extends balancer_board_page
{
	var $title = 'Личный ☼ счёт';
	var $must_be_user = true;

	function mlog()
	{
		return bors_find_all('airbase_money_log', [
			'user_id' => bors()->user_id(),
			'create_time>' => time() - 86300*7,
			'order' => '-create_time',
		]);
	}

	function mstat()
	{
		return bors_find_all('airbase_money_log', [
			'*set' => 'COUNT(*) as total, SUM(amount) AS `sum`',
			'user_id' => bors()->user_id(),
			'create_time>' => time() - 86300*7,
			'group' => 'comment, amount',
			'order' => 'SUM(amount) DESC',
		]);
	}

	function balance()
	{
		return bors_find_first('airbase_money_log', [
			'*set' => 'SUM(amount) AS `sum`',
			'user_id' => bors()->user_id(),
			'create_time>' => time() - 86300*7,
		])->sum();
	}
}
