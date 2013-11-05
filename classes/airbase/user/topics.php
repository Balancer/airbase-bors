<?php

class airbase_user_topics extends balancer_board_page
{
//	function config_class() { return 'airbase_forum_config'; }
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
			$this->ids  = $this->db()->select_array('posts', 'DISTINCT topic_id', array(
				'poster_id=' => $this->id(),
				'posted > ' => time()-86400*31,
			));

		return $this->ids;
	}

	function url() { return "http://www.balancer.ru/user/".$this->id()."/use-topics.html"; }

	function local_data()
	{
		if($this->topics_ids())
			return array('topics' => objects_array('balancer_board_topic', array(
					'id IN' => $this->topics_ids(), 
					'last_post_create_time>' => 0,
					'order' => '-last_post',
			)));
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
	function user() { if($this->user === false) $this->user = object_load('bors_user', $this->id()); return $this->user; }
	function title() { return object_property($this->user(), 'title').ec(': темы с участием за месяц'); }
	function nav_name() { return ec('темы с участием за месяц'); }

	function body_template() { return 'xfile:airbase/forum/forum.html'; }

	function cache_static() { return config('static_forum') ? rand(86400*7, 14*86400) : 0; }

	function pages_links_nul($css='pages_select', $text = NULL, $delim = '', $show_current = true, $use_items_numeration = false, $around_page = NULL)
	{
		return "";
	}
}
