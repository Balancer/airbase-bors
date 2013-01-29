<?php

class airbase_forum_news extends base_page
{
	function title() { return ec('Лента тем форума «').$this->forum()->title().ec('» и его подфорумов за год'); }

	private $forum = false;
	function forum()
	{
		if($this->forum !== false)
			return $this->forum;

		return $this->forum = object_load('forum_forum', $this->id());
	}

	function nav_name(){ return ec('Лента'); }
	function parents() { return array($this->forum()->internal_uri()); }

	function body_data()
	{
		return array(
			'topics' =>array_reverse(objects_array('balancer_board_topic', array(
				'forum_id IN' => $this->forum()->all_public_subforum_ids(),
				'create_time>' => floor(time() - 86400*365.24),
				'order' => 'create_time',
				'page' => $this->page(),
				'per_page' => $this->items_per_page(),
			))),
		);
	}

	function total_items()
	{
		if($this->__havefc())
			return $this->__lastc();
		return $this->__setc(intval(bors_count('balancer_board_topic', array(
			'forum_id IN' => $this->forum()->all_public_subforum_ids(),
			'create_time>' => floor(time() - 86400*365.24),
		))));
	}

	function default_page() { return $this->total_pages(); }
	function reverse_pages() { return true; }
	function items_per_page() { return 10; }
	function cache_group_parents() { return "forum-topics"; }

	function template()
	{
//		template_noindex();
		return 'forum/_header.html';
	}

	function url_engine() { return 'url_calling2'; }
	function cache_static()
	{
		if(!config('static_forum'))
			return 0;
		return $this->page() != $this->default_page() ? rand(3600, 7200) : rand(100,500);
	}

	function can_cached() { return false; }
}
