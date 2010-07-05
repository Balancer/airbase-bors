<?php

class airbase_forum_news extends base_page
{
	function title() { return ec('Лента тем форума «').$this->forum()->title().ec('» и его подфорумов'); }
	
	private $forum = false;
	function forum()
	{
		if($this->forum !== false)
			return $this->forum;
		
		return $this->forum = object_load('forum_forum', $this->id());
	}
	
	function nav_name(){ return ec('Лента'); }
	function parents() { return array($this->forum()->internal_uri()); }
	
	function local_data()
	{
		return array(
			'topics' =>array_reverse(objects_array('forum_topic', array(
				'forum_id IN' => $this->forum()->all_public_subforum_ids(),
				'order' => 'create_time',
				'page' => $this->page(),
				'per_page' => $this->items_per_page(),
			))),
		);
	}

	function total_items() { return $this->__havec('total_items') ? $this->__lastc() : $this->__setc(intval(objects_count('forum_topic', array('forum_id IN' => $this->forum()->all_public_subforum_ids())))); }

	function default_page() { return $this->total_pages(); }
	function reverse_pages() { return true; }
	function items_per_page() { return 10; }
	function cache_groups_parent() { return "forum-topics"; }

	function template()
	{
//		template_noindex();
		return 'forum/_header.html';
	}
	
	function url_engine() { return 'url_calling2'; }
	function cache_static() { return $this->page() != $this->default_page() ? rand(3600, 7200) : rand(100,500); }
	function can_cached() { return false; }
}
