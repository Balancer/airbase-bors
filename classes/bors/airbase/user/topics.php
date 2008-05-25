<?php

class airbase_user_topics extends base_page
{
//	function config_class() { return 'airbase_forum_config'; }
	function main_db_storage() { return 'punbb'; }
	function template() { return 'forum/_header.html'; }

	private $ids = array();
	private function topics_ids($page)
	{
		if(empty($this->ids[$page]))
			$this->ids[$page] = $this->db()->select_array('posts', 'DISTINCT topic_id', array('poster_id=' => $this->id(), 'order' => 'topic_id', 'per_page'=>$this->items_per_page(), 'page'=>$page));

		return $this->ids[$page];
	}

	function data_providers()
	{
		$ids = $this->topics_ids($this->page());

		$data = array();
		if($ids)
			$data['topics'] = objects_array('forum_topic', array('id IN ('.join(',', $ids).')', 'order' => '-id'));

		return $data;
	}

	function pre_show()
	{
		$this->add_template_data('skip_subforums', true);
		templates_noindex();
		return false;
	}

	function url($page = 1) { return "http://balancer.ru/user/".$this->id()."/use-topics".($page == 0 ? '' : ",$page").".html"; }
	
	private $user = false;
	function user() { if($this->user === false) $this->user = object_load('bors_user', $this->id()); return $this->user; }
	function title() { return $this->user()->title().ec(': темы с участием'); }
	function nav_name() { return ec('темы с участием'); }

	function default_page() { return $this->total_pages(); }

	function items_per_page() { return 100; }

	private $total = false;
	function total_items()
	{
		if($this->total === false)
			$this->total = intval($this->db()->select('posts', 'COUNT(DISTINCT topic_id)', array('poster_id=' => $this->id())));

		return $this->total;
	}
	
	function body_template() { return 'airbase/forum/forum.html'; }

	function cache_static() { return 14*86400; }

 	function cache_clean_self($object)
	{
		$topic_id = 0;
		if($object->class_name() == 'forum_topic')
			$topic_id = $object->id();
		elseif($object->class_name() == 'forum_post')
			$topic_id = $object->topic_id();
	
		$start = 1;
		$stop = $this->total_pages();
		
		if($topic_id)
		{
			for($page=$this->total_pages(); $page > 0; $page--)
				if(in_array($topic_id, $this->topics_ids($page)))
				{
					$start = $page;
					break;
				}		

			if(objects_count('forum_post', array('topic_id=' => $topic_id, 'poster_id='=>$this->id())) > 1)
				$stop = $start;
		}
		
		for($page = $start; $page <= $stop; $page++)
			@unlink("/var/www/balancer.ru/htdocs/user/{$this->id()}/use-topics,{$page}.html");
	}
}
