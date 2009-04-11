<?php

class user_blog extends base_page
{
	private $user;
	
	function main_db(){ return 'punbb'; }

	function template()
	{
		templates_noindex();
		return 'forum/_header.html';
	}
	
	function title() { return $this->user->title().ec(": Блог"); }
	function nav_name() { return ec("блог"); }

	function parents() { return array("forum_user://".$this->id()); }

	private $data = array();
	function local_data()
	{
		$page_id = $this->page().','.$this->items_per_page();
	
		if(isset($this->data[$page_id]))
			return $this->data[$page_id];

		$this->data[$page_id] = array(
				'blog_records' => array_reverse(objects_array('forum_blog', array(
					'where' => array('owner_id=' => $this->id()),
					'order' => 'blogged_time',
					'page' => $this->page(),
					'per_page' => $this->items_per_page(),
				)))
			);
		
		return $this->data[$page_id];
	}

	function default_page() { return $this->total_pages(); }

	function items_per_page() { return 20; }
	
	private $total = false;
	function total_items()
	{
		if($this->total === false)
			$this->total = intval(objects_count('forum_blog', array('where' => array('owner_id=' => $this->id()))));

		return $this->total;
	}

	private $last_post = false;
	function last_post()
	{
		if($this->last_post === false)
		{
			$data = $this->local_data();
			if(count(@$data['blog_records']))
				$this->last_post = object_load('forum_post', $data['blog_records'][0]->id());
			else
				$this->last_post = NULL;
		}
		
		return $this->last_post;
	}

	private $first_post = false;
	function first_post()
	{
		if($this->first_post === false)
		{
			$data = $this->data_providers();
			$records = @$data['blog_records'];
			if(count($records))
				$this->first_post = object_load('forum_post', $records[count($records)-1]->id());
			else
				$this->first_post = NULL;
		}
		
		return $this->first_post;
	}
	
	function create_time() { return $this->first_post() ? $this->first_post()->create_time() : 0; }
	function modify_time() { return $this->last_post() ? $this->last_post()->modify_time() : 0;	}

	function total_pages() { return intval(($this->total_items()-1) / $this->items_per_page()) + 1; }

	function __construct($id)
	{
		$this->set_id($id);
	
		$this->user = class_load('forum_user', $id);
		parent::__construct($id);
	}

	function pre_show()
	{
		$this->add_template_data('user_id', $this->id());
		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->url()."rss.xml\" title=\"RSS блога пользователя ".addslashes($this->user->title())."\" />");

		return false;
	}

	function url($page = NULL)
	{	
		if(!$page || $page == $this->default_page())
			return "http://balancer.ru/user/".$this->id()."/blog/"; 
		else
			return "http://balancer.ru/user/".$this->id()."/blog/$page.html"; 
	}

	function cache_static()
	{
		if(!$this->page() || $this->page() == $this->default_page())
			return rand(600, 7200);
		else
			return rand(3*86400, 20*86400);
	}
		
	function pages_links()
	{
		if($this->total_pages() < 2)
			return "";

		include_once('funcs/design/page_split.php');
		return join(" ", pages_show($this, $this->total_pages(), 12));
	}

	function num_blog() { return $this->db()->get('SELECT COUNT(*) FROM topics WHERE poster_id='.$this->id()); }

	function can_be_empty() { return true; }

	function page_by_pid($pid)
	{
		$before = intval(objects_count('forum_blog', array('where' => array('owner_id=' => $this->id(), 'post_id<' => intval($pid)))));
		return intval(($before) / $this->items_per_page()) + 1;
	}

 	function cache_clean_self($pid = 0)
	{
		if($pid == 0 || !is_numeric($pid))
			$start = $this->total_pages();
		else
			$start = $this->page_by_pid($pid - 1);

		$stop = $this->total_pages();

		for($page = $start; $page <= $stop; $page++)
			@unlink('/var/www/balancer.ru/htdocs/user/'.$this->id().'/blog/'.$page.'.html');
	}
}
