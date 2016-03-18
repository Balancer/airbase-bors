<?php

class balancer_board_users_money extends balancer_board_page
{
	function title() { return 'Статистика по «солнышкам»'; }
	function nav_name() { return '«солнышки»'; }

	function parents()
	{
		return array("http://www.balancer.ru/users/".$this->id().'/');
	}

	function body_data()
	{
		$low_money = balancer_board_user::find([
			'money<' => 0,
			'order' => 'money'
		])->all();

		return array_merge(parent::body_data(), compact('low_money'));
	}
}
