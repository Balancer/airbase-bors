<?php

class balancer_board_money_needy extends balancer_board_page
{
	var $title = 'Им может быть нужна Ваша солнечная помощь';
	var $nav_name = 'нуждающиеся';

	function body_data()
	{
		return array_merge(parent::body_data(), [
			'banned' => bors_find_all('balancer_board_user', [
				'warnings>=' => 10,
			]),

			'low_money' => bors_find_all('balancer_board_user', [
				'money<=' => -2500,
				'money>=' => -3000,
				'order' => '-money',
			]),


			'ban_money' => bors_find_all('balancer_board_user', [
				'money<=' => -3000,
				'order' => '-money',
			]),

		]);
	}
}
