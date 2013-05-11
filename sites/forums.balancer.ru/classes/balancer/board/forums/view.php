<?php

class balancer_board_forums_view extends balancer_board_meta_main
{
	function can_be_empty() { return false; }
	function is_loaded() { return (bool) $this->forum(); }

	function forum() { return bors_load('balancer_board_forum', $this->id()); }
	function title() { return $this->forum()->title(); }
	function nav_name() { return $this->forum()->nav_name(); }

	function parents() { return $this->forum()->parents(); }

	function model_class() { return 'balancer_board_topic'; }
	function main_class() { return 'balancer_board_topic'; }

	function items_per_page() { return 50; }

	function default_order() { return '-last_post_create_time'; }
//	function order() { var_dump(parent::order()); return '-modify_time'; }

	function where()
	{
		return array(
			'num_replies>=' => 0,
			'forum_id' => $this->id(),
		);
	}

	function item_fields()
	{
		return array(
			'image_thumbnail_64' => '&nbsp;',
			'titled_link' => 'Тема',
			'num_replies' => 'Число ответов',
			'visits' => 'Число просмотров',
			'last_post_ctime()->dmy_hm()' => 'Дата последнего ответа',
			'last_post_snip' => 'Последний ответ',
		);
	}

	function sortable()
	{
		return array(
			'titled_link' => 'title',
			'num_replies' => '-num_replies',
			'visits' => '-visits',
			'last_post_ctime()->dmy_hm()' => '-last_post_create_time*',
		);
	}
}
