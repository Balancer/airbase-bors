<?php

class wrk_blogs_main extends bors_page
{
	function title() { return ec('Блоги на Balancer/W.R.K.'); }
	function nav_name() { return ec('блоги'); }

	function config_class() { return 'wrk_config'; }

	function body_data()
	{
		$blogs = bors_find_all('balancer_board_blog', array(
			'is_public' => true,
			'inner_join' => 'balancer_board_post ON balancer_board_post.id = balancer_board_blog.id',
			'(score >= 0 OR score IS NULL)', //TODO: переписать на имена полей после исправления
			'limit' => 25,
			'order' => '-blogged_time',
		));

		$post_ids = bors_field_array_extract($blogs, 'id');
		$posts = bors_find_all('balancer_board_post', array('id IN' => $post_ids, 'by_id' => true));

		foreach($blogs as $b)
			if(!empty($posts[$b->id()]))
				$b->set_post($posts[$b->id()], false);

		return array_merge(parent::body_data(), compact('blogs', 'posts'));
	}
}
