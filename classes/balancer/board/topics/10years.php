<?php

class balancer_board_topics_10years extends base_page_paged
{
	function main_class() { return 'forum_topic'; }
	function order() { return 'create_time'; }
	function template() { return 'forum/_header.html'; }
	function title() { return 'В этот день 10 лет назад...'; }
	function nav_name() { return '10 лет назад'; }
	function where()
	{
		$y = date('Y')-10;
		$m = date('m');
		$d = date('d');
		if($d == 29)
			$d = 28;

		$start = strtotime("$y-$m-$d 00:00:00");
		$stop  = strtotime("$y-$m-$d 23:59:59");

		return array("posted BETWEEN $start AND $stop", 'is_public' => 1);
	}

	function local_data()
	{
		return array_merge(parent::local_data(), array('years10' => true));
	}
}
