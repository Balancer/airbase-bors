<?php

include_once('inc/strings.php');

class user_posts_month extends base_page
{
	function cache_static() { return 600; }

	private $year, $month;

	function set_page($page)
	{
		list($this->year, $this->month) = explode('/', $page);
		return parent::set_page($page);
	}

	function template()
	{
		templates_noindex();
		return 'forum/_header.html';
	}	

	private $_user = false;
	function user()
	{
		if($this->_user === false)
			$this->_user = bors_user($this->id());
		
		return $this->_user;
	}
	
	function title() { return $this->user()->title().ec(': Все сообщения за ').strtolower(month_name($this->month)).' '.$this->year.ec(' года'); }
	function nav_name() { return strtolower(month_name($this->month)); }
	function parents() { return array("http://balancer.ru/user/{$this->id()}/posts/{$this->year}/"); }

	function url() { return "http://balancer.ru/user/{$this->id()}/posts/{$this->year}/{$this->month}/"; }

    function local_template_data_set()
	{
		$year	= $this->year;
		$month	= $this->month;
		$time0	= intval(strtotime("$year-$month-01 00:00:00"));
		$days	= date('t', $time0);
		$list	= array();
		for($day=1; $day<$days; $day++)
		{
			$time9	= $time0 + 86400;
			$total = objects_count('forum_post', array('poster_id' => $this->id(), "posted BETWEEN $time0 AND $time9"));
			$time0 = $time9;

			if($total)
				$list[$day] = array(
					'url' => "http://balancer.ru/user/{$this->id()}/posts/{$this->year}/{$this->month}/{$day}/",
					'title' => $total.' '.sklon($total, ec('сообщение'), ec('сообщения'), ec('сообщений')),
			);
		}
		
		return array('year' => $year, 'month' => $month, 'list' => $list);
	}
}
