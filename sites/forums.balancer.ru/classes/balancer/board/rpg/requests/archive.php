<?php

class balancer_board_rpg_requests_archive extends balancer_board_paginated
{
	var $auto_map = true;

	var $title = 'Архив RPG-запросов';
	var $nav_name = 'архив';
	var $main_class = 'balancer_board_rpg_request';

	function where()
	{
		return [
			'(have_score>=need_score OR create_time<' . (time() - 86400*14).')',
		];
	}
}
