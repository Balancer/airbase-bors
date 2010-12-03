<?php

include_once('inc/strings.php');

class user_posts_month extends base_page
{
	function cache_static() { return config('static_forum') ? 600 : 0; }

	private $year, $month;

	function init()
	{
		list($this->year, $this->month) = explode('/', $this->args('page'));
		return parent::init();
	}

	function template()
	{
		template_noindex();
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

    function local_data()
	{
		$year	= $this->year;
		$month	= $this->month;
		$time0	= intval(strtotime("$year-$month-01 00:00:00"));
		$days	= date('t', $time0);
		$list	= array();
		for($day=1; $day<=$days; $day++)
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
