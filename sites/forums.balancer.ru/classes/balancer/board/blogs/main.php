<?php

class balancer_board_blogs_main extends bors_abstract_blog
{
	function main_class() { return 'balancer_board_blogs_record'; }
	function order() { return 'blogged_time'; }
	function on_items_load($blog_records)
	{
		bors_objects_preload($blog_records, 'id', 'balancer_board_post', 'post');
	}

	function template() { return 'forum/_header.html'; }
	function items_around_page() { return 11; }

	function pre_show()
	{
		template_noindex();
		return parent::pre_show();
	}

	function cache_static()
	{
		if(!config('static_forum'))
			return 0;

		return $this->page() >= $this->default_page()-2 ? rand(300,600) : rand(86400*14, 86400*30);
	}
}
