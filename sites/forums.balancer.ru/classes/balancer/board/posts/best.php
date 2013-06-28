<?php

class balancer_board_posts_best extends bors_page
{
	function title() { return ec('Лучшие сообщения форумов Balancer.ru'); }
	function nav_name() { return ec('лучшие сообщения'); }

	function config_class() { return 'balancer_board_config'; }
	function template() { return 'xfile:/var/www/wrk.ru/bors-site/templates/wrk/light.html'; }

	function body_data()
	{
		template_css('/_bors/css/bors/style.css');
		$cached = bors_find_all('balancer_board_posts_cached', array(
//			'mark_best_date IS NOT NULL',
			'best_page_num' => $this->page(),
//			'per_page' => $this->items_per_page(),
			'order' => 'mark_best_date',
		));

		$post_ids = bors_field_array_extract($cached, 'id');
		$posts = bors_find_all('balancer_board_post', array('id IN' => $post_ids, 'order' => '-mark_best_date'));

		balancer_board_post::posts_preload($posts);

		return array_merge(parent::body_data(), compact('posts'));
	}

	function items_per_page() { return 25; }

	function total_items()
	{
		if($this->__havefc())
			return $this->__lastc();

		$foo = bors_find_first('balancer_board_posts_cached', array(
			'order' => '-best_page_num',
		));

		return $this->__setc($foo->best_page_num()*$this->items_per_page());
	}

	function is_reversed() { return true; }
	function default_page() { return $this->total_pages(); }
	function url($page = NULL) { return '/best/'.($page == $this->default_page() || is_null($page) ? '' : "$page.html"); }
}
