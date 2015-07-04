<?php

class balancer_board_posts_worst extends balancer_board_page
{
	function title() { return ec('Сообщения форумов Balancer.ru с наибольшим числом отрицательных оценок'); }
	function nav_name() { return ec('худшие сообщения'); }

	function config_class() { return 'balancer_board_config'; }
	function template() { return 'xfile:/var/www/wrk.ru/bors-site/templates/wrk/light.html'; }

	function body_data()
	{
		$posts = bors_find_all('balancer_board_post', array(
			'posted>' => time()-86400*14,
			'score_negative_raw>' => 5,
			'order' => '`order`, posted DESC',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
		));

		return array_merge(parent::body_data(), compact('posts'));
	}

	function items_per_page() { return 25; }

	function total_items()
	{
		if($this->__havefc())
			return $this->__lastc();

		return $this->__setc(bors_count('balancer_board_post', array(
			'posted>' => time()-86400*14,
			'score_negative_raw>' => 5,
		)));
	}
}
