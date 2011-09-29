<?php

class balancer_board_new_topics extends balancer_board_paginated
{
	function main_class() { return 'balancer_board_topic'; }
	function order() { return '-create_time'; }
//	function template() { return 'forum/_header.html'; }
	function title() { return 'Новые темы за месяц'; }
	function nav_name() { return 'новые темы'; }
	function where() { return array('posted>' => time()-86400*31, 'is_public' => 1); }
}
