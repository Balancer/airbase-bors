<?php

class balancer_board_rpg_main extends balancer_board_page
{
	var $title = "RPG-система форумов Balancer'а";
	var $nav_name = 'RPG';

	function body_data()
	{
		return array_merge(parent::body_data(), array(
			'top' => bors_find_all('balancer_board_user', ['order' => '-rpg_level,-reputation', 'limit' => 50]),
		));
	}
}
