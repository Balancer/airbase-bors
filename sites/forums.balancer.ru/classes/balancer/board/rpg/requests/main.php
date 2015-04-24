<?php

class balancer_board_rpg_requests_main extends balancer_board_paginated
{
	var $title = 'RPG-запросы';
	var $nav_name = 'запросы';
	var $main_class = 'balancer_board_rpg_request';

	function description()
	{
		$me = bors()->user();

		return $me ?
			"Вес Ваших баллов для голосования: ".pow(3, $me->rpg_level()).' [<a href="archive/">архив запросов</a>]'
			: "у Вас нет баллов для голосования — авторизуйтесь";
	}

	function where()
	{
		return [
			'have_score<need_score',
			'create_time>' => time() - 86400*14,
		];
	}
}
