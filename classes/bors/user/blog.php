<?php

class user_blog extends base_page
{
	function _class_file() { return __FILE__; }

	function main_db_storage(){ return 'punbb'; }

	function template()
	{
		templates_noindex();
		return BORS_INCLUDE.'templates/forum/_header.html';
	}

	var $user;
	
	function title() { return $this->user->title().ec(": Блог"); }
	function nav_name() { return ec("блог"); }

	function parents() { return array("forum_user://".$this->id()); }

	private $data = NULL;
	function data_providers()
	{
		if($this->data === NULL)
			$this->data = array(
				'blog_records' => array_reverse(objects_array('forum_blog', array(
					'where' => array('owner_id=' => $this->id()),
					'order' => 'blogged_time',
					'page' => $this->page(),
					'per_page' => $this->items_per_page(),
				)))
			);
		
		return $this->data;
	}

	function default_page() { return $this->total_pages(); }

	function items_per_page() { return 20; }
	
	private $total = NULL;
	function total_items()
	{
		if($this->total == NULL)
			$this->total = intval(objects_count('forum_blog', array('where' => array('owner_id=' => $this->id()))));

		return $this->total;
	}

	private $last_post = NULL;
	function last_post()
	{
		if($this->last_post === NULL)
		{
			$data = $this->data_providers();
			$this->last_post = object_load('forum_post', $data['blog_records'][0]->id());
		}
		
		return $this->last_post;
	}

	private $first_post = NULL;
	function first_post()
	{
		if($this->first_post === NULL)
		{
			$data = $this->data_providers();
			$records = $data['blog_records'];
			$this->first_post = object_load('forum_post', $records[count($records)-1]->id());
		}
		
		return $this->first_post;
	}
	
	function create_time() { return $this->first_post()->create_time();	}
	function modify_time() { return $this->last_post()->modify_time();	}

	function total_pages() { return intval(($this->total_items()-1) / $this->items_per_page()) + 1; }

	function __construct($id)
	{
		$this->set_id($id);
	
		$this->user = class_load('forum_user', $id);
		parent::__construct($id);
			
		$this->add_template_data('user_id', $id);
		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->url(1)."rss.xml\" title=\"RSS блога пользователя ".addslashes($this->user->title())."\" />");
	}

	function url($page = 1)
	{	
		if($page == 0)
			return "http://balancer.ru/user/".$this->id()."/blog/"; 
		else
			return "http://balancer.ru/user/".$this->id()."/blog/$page.html"; 
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
		return join(" ", pages_show($this, $this->total_pages(), 16));
	}

	function num_blog() { return $this->db->get('SELECT COUNT(*) FROM topics WHERE poster_id='.$this->id()); }

	function can_be_empty() { return true; }

	function page_by_pid($pid)
	{
		$before = intval(objects_count('forum_blog', array('where' => array('owner_id=' => $this->id(), 'post_id<' => intval($pid)))));
		return intval(($before) / $this->items_per_page()) + 1;
	}

 	function cache_clean_self($pid = 0)
	{
		if($pid == 0)
			$start = $this->total_pages();
		else
			$start = $this->page_by_pid($pid - 1);

		$stop = $this->total_pages();

		for($page = $start; $page <= $stop; $page++)
			@unlink('/var/www/balancer.ru/htdocs/user/'.$this->id().'/blog/'.$page.'.html');
	}
}
