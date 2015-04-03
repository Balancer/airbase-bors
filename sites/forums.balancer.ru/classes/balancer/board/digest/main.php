<?php

class balancer_board_digest_main extends balancer_board_posts_best2
{
	var $auto_map = true;
	var $main_class = 'balancer_board_posts_calculated';

	function title() { return ec('Дайджест форумов Balancer.ru'); }
	function nav_name() { return ec('дайджест'); }

	function cache_static0()
	{
		$diff_p = abs($this->default_page() - $this->page());
		return rand(600, 3600) + $diff_p*rand(36000,72000);
	}

	function on_items_load(&$items)
	{
		$items = bors_find_all('balancer_board_post', array(
			'id IN' => array_keys($items),
			'order' => '-best10_ts',
		));
	}

	function where()
	{
		return array(
			'best10_ts IS NOT NULL',
			'forum_id NOT IN' => [212, 25, 10, 206, 80, 203, 78, 91],
			'(answer_to_post_id IS NULL OR answer_to_post_id=0)',
		);
	}

	function inner_join()
	{
		return [
			'balancer_board_post  ON balancer_board_post.id = balancer_board_posts_calculated.id',
			'balancer_board_topic ON balancer_board_post.topic_id = balancer_board_topic.id',
			'balancer_board_forum ON balancer_board_topic.forum_id = balancer_board_forum.id',
		];
	}

	function left_join()
	{
		return [
			'balancer_board_category ON balancer_board_forum.category_id = balancer_board_category.id',
		];
	}

	function order() { return 'best10_ts'; }

	function config_class() { return 'balancer_board_config'; }
	function template() { return 'xfile:/var/www/wrk.ru/bors-site/templates/wrk/light.html'; }

	function items_per_page() { return 25; }

	function is_reversed() { return true; }
	function default_page() { return $this->total_pages(); }
}
