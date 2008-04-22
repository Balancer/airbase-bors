<?php

class user_posts extends base_page
{
	function template()
	{
		templates_noindex();
		return 'forum/_header.html';
	}	

	var $user;
	
	function title() { return $this->user->title().ec(": Все сообщения"); }
	function nav_name() { return ec("все сообщения"); }

	function parents() { return array("forum_user://".$this->id()); }

	private $data = array();
	function data_providers()
	{
		$page_id = $this->page().','.$this->items_per_page();
	
		if(isset($this->data[$page_id]))
			return $this->data[$page_id];

		$this->data[$page_id] = array(
				'posts' => objects_array('forum_post', array(
					'where' => array('poster_id=' => $this->id()),
					'order' => 'posted',
					'page' => $this->page(),
					'per_page' => $this->items_per_page(),
				))
			);
		
		return $this->data[$page_id];
	}

	function __construct($id)
	{
		$this->set_id($id);
		
		$this->user = class_load('forum_user', $id);
		parent::__construct($id);
			
		$this->add_template_data('user_id', $id);
	}

	function default_page() { return $this->total_pages(); }

	function items_per_page() { return 20; }

	private $total = NULL;
	function total_items()
	{
		if($this->total == NULL)
			$this->total = intval(objects_count('forum_post', array('where' => array('poster_id=' => $this->id()))));

		return $this->total;
	}

	function url($page = 1)
	{	
		if($page == 0)
			return "http://balancer.ru/user/".$this->id()."/posts/"; 
		else
			return "http://balancer.ru/user/".$this->id()."/posts/$page.html"; 
	}

	function cache_static()
	{
		return 86400*14;
	}
		
	function pages_links()
	{
		if($this->total_pages() < 2)
			return "";

		include_once('funcs/design/page_split.php');
		return join(" ", pages_show($this, $this->total_pages(), 10));
	}

	function can_be_empty() { return true; }

 	function cache_clean_self($pid = 0)
	{
		if($pid == 0)
			$start = $this->total_pages();
		else
			$start = $this->page_by_pid($pid - 1);

		$stop = $this->total_pages();

		for($page = $start; $page <= $stop; $page++)
			@unlink('/var/www/balancer.ru/htdocs/user/'.$this->id().'/posts/'.$page.'.html');
	}
}
