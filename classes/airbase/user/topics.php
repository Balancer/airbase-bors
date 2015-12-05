<?php

class airbase_user_topics extends balancer_board_page
{
	function db_name() { return config('punbb.database'); }
	function template() { return 'forum/_header.html'; }

	function parents()
	{
		$p = parent::parents();
		if($this->id() == bors()->user_id())
			$p[] = 'http://forums.balancer.ru/personal/';

		return $p;
	}

	private $ids = false;
	private function topics_ids()
	{
		if($this->ids === false)
			$this->ids  = bors_field_array_extract(balancer_board_post::find([
				'poster_id=' => $this->id(),
				'posted>' => time()-86400*31,
			])->group('topic_id')->all(), 'topic_id');

		return $this->ids;
	}

	function url() { return "http://www.balancer.ru/user/".$this->id()."/use-topics.html"; }

	function body_data()
	{
		if($this->topics_ids())
		{
			$topics = bors_find_all('balancer_board_topic', array(
				'id IN' => $this->topics_ids(), 
				'last_post_create_time>' => 0,
				'order' => '-last_post',
			));

			bors_objects_preload($topics, 'forum_id', 'balancer_board_forum', 'forum');
			bors_objects_preload($topics, 'owner_id', 'balancer_board_user',  'owner');
			bors_objects_preload($topics, 'image_id', 'airbase_image');

			$images = bors_field_array_extract($topics, 'image');
			bors_objects_preload($images, 'id_96x96', 'bors_image_thumb', 'thumbnail_96x96', true);

//			$post_ids = bors_field_array_extract($topics, 'last_post_id');
			bors_objects_preload($topics, 'last_post_id', 'balancer_board_post', 'last_post');

			return array_merge(parent::body_data(), ['topics' => $topics]);
		}
		else
			return array();
	}

	function pre_show()
	{
		if(!$this->user())
			return bors_http_error(404);

		$this->add_template_data('skip_subforums', true);
		template_noindex();
		return false;
	}

	private $user = false;
	function user() { if($this->user === false) $this->user = bors_load('bors_user', $this->id()); return $this->user; }
	function title() { return object_property($this->user(), 'title').': '.ec('темы с участием за месяц'); }
	function nav_name() { return ec('темы с участием за месяц'); }

	function cache_static() { return config('static_forum') ? rand(86400*7, 14*86400) : 0; }

	function pages_links_nul($css='pages_select', $text = NULL, $delim = '', $show_current = true, $use_items_numeration = false, $around_page = NULL)
	{
		return "";
	}
}
