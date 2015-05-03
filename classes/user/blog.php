<?php

class user_blog extends balancer_board_page
{
	function can_be_empty() { return false; }

	function is_loaded() { return (bool) $this->user(); }

	function db_name(){ return config('punbb.database'); }

	function auto_objects()
	{
		return array(
			'user' => 'balancer_board_user(id)',
		);
	}

	function template()
	{
		template_noindex();
		return 'forum/_header.html';
	}

	function title() { return object_property($this->user(), 'title').ec(": Блог"); }
	function nav_name() { return ec("блог"); }

	function parents() { return array("balancer_board_user://".$this->id()); }

	private $xdata = array();
	function body_data()
	{
		$page_id = $this->page().','.$this->items_per_page();

		if(isset($this->xdata[$page_id]))
			return $this->xdata[$page_id];

		$this->xdata[$page_id] = array(
				'blog_records' => array_reverse(objects_array('balancer_board_blog', array(
					'owner_id' => $this->id(),
					'is_microblog' => 0,
					'order' => 'blogged_time',
//					'create_time<=' => time(),
					'page' => $this->page(),
					'per_page' => $this->items_per_page(),
					'is_public' => 1,
					'is_deleted' => false,
				)))
			);

		return $this->xdata[$page_id];
	}

	function items_per_page() { return 20; }

	private $total = false;
	function total_items()
	{
		if($this->total === false)
			$this->total = intval(bors_count('balancer_board_blog', array(
				'owner_id' => $this->id(),
//				'create_time<=' => time(),
				'is_microblog' => 0,
				'is_public' => 1,
				'is_deleted' => false,
			)));

		return $this->total;
	}

	private $last_post = false;
	function last_post()
	{
		if($this->last_post === false)
		{
			$data = $this->body_data();
			if(count(@$data['blog_records']))
				$this->last_post = bors_load('balancer_board_post', $data['blog_records'][0]->id());
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
				$this->first_post = bors_load('balancer_board_post', $records[count($records)-1]->id());
			else
				$this->first_post = NULL;
		}

		return $this->first_post;
	}

	function create_time() { return $this->first_post() ? $this->first_post()->create_time() : 0; }
	function modify_time() { return $this->last_post() ? $this->last_post()->modify_time() : 0;	}

	function total_pages() { return intval(($this->total_items()-1) / $this->items_per_page()) + 1; }

	function pre_show()
	{
		$this->add_template_data('user_id', $this->id());
		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->url()."rss.xml\" title=\"RSS блога пользователя ".htmlspecialchars($this->user()->title())."\" />");

		return false;
	}

	function url($page = NULL)
	{
		if(!$page || $page == $this->default_page())
			return "http://www.balancer.ru/user/".$this->id()."/blog/"; 
		else
			return "http://www.balancer.ru/user/".$this->id()."/blog/$page.html"; 
	}

	function cache_static()
	{
		if(!config('static_forum'))
			return 0;

		if(!$this->page() || $this->page() == $this->default_page())
			return rand(600, 7200);
		else
			return rand(3*86400, 20*86400);
	}

	function num_blog() { return $this->db()->get('SELECT COUNT(*) FROM topics WHERE poster_id='.$this->id()); }

	function page_by_pid($pid)
	{
		$before = intval(bors_count('balancer_board_blog', array('where' => array(
			'owner_id=' => $this->id(),
			'post_id<' => intval($pid),
			'is_deleted' => false,
		))));
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

	function is_reversed() { return true; }
	function default_page() { return $this->total_pages(); }
}
