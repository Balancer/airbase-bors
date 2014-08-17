<?php

class balancer_board_rpg_requests_main extends balancer_board_paginated
{
	var $title = 'RPG-запросы';
	var $nav_name = 'запросы';
	var $main_class = 'balancer_board_rpg_request';

	function where()
	{
		return [
			'have_score<need_score',
		];
	}
}
