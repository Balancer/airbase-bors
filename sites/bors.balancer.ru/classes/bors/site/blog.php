<?php

class bors_site_blog extends base_page_paged
{
	function title() { return ec('Блог проекта'); }
	function nav_name() { return ec('блог'); }
	function description() { return ec('Если вы верите, что сначала было слово, то попробуйте побудить словом к делу какой-нибудь форум.'); }

	function main_class() { return 'balancer_board_topic'; }
	function where() { return array('forum_id' => 60); }
//	function order() { return 'create_time'; }
	function on_items_load($topics) { bors_objects_preload($topics, 'first_post_id', 'balancer_board_post', 'post'); }

	function is_auto_url_mapped_class() { return true; }
//	function is_reversed() { return true; }

	function items_around_page() { return 11; }
	function items_per_page() { return 10; }

	function pre_show()
	{
		templates_noindex();
		return parent::pre_show();
	}

//	function cache_static() { return $this->page() >= $this->default_page()-2 ? rand(300,600) : rand(86400*14, 86400*30); }
}
