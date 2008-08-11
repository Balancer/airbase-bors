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
	
	function local_template_data_set()
	{
		return array(
			'topics' => objects_array('forum_topic', array(
				'forum_id IN' => $this->forum()->all_public_subforum_ids(),
				'order' => 'create_time',
				'page' => $this->page(),
				'per_page' => $this->items_per_page(),
			)),
		);
	}

	private $_total = false;
	function total_items()
	{
		if($this->_total === false)
			$this->_total = intval(objects_count('forum_topic', array('forum_id IN' => $this->forum()->all_public_subforum_ids())));

		return $this->_total;
	}

	function default_page() { return $this->total_pages(); }
	function items_per_page() { return 10; }
	function cache_groups_parent() { return "forum-topics"; }

	function template()
	{
//		templates_noindex();
		return 'forum/_header.html';
	}
	
	function url_engine() { return 'url_calling2'; }
	function cache_static() { return rand(100,500); }
}

