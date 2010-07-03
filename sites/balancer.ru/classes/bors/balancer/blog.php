<?php

class balancer_blog extends user_blog
{
	function template()
	{
		templates_noindex();
		return 'BlueLeaves';
	}
	
	function title() { return ec('Тропа Балансер\'а'); }
	function nav_name() { return ec("тропа"); }

	function parents() { return array('/'); }

	function __construct($id)
	{
		$id = 10000;
		
		$this->set_id($id);
	
		$this->user = class_load('forum_user', $id);
		parent::__construct($id);
			
		$this->add_template_data('user_id', $id);
		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->url(1)."rss.xml\" title=\"RSS блога пользователя ".addslashes($this->user->title())."\" />");
	}

	function url($page = 1)
	{	
		if($page == 0)
			return "http://balancer.ru/blog/"; 
		else
			return "http://balancer.ru/blog/$page.html"; 
	}

	function cache_static()
	{
		return 86400*14;
	}
}