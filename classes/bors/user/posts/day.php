<?php

include_once('inc/strings.php');

class user_posts_day extends base_page
{
	function cache_static() { return $this->is_today() ? 600 : 86400*60; }

	private $year, $month, $day;

	function set_page($page)
	{
		list($this->year, $this->month, $this->day) = explode('/', $page);
		return parent::set_page($page);
	}

	function is_today()
	{
		return $this->year == date('Y') && $this->month == date('n') && $this->day == date('j');
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
	
	function title() { return $this->user()->title().ec(': Все сообщения за ').$this->day.' '.strtolower(month_name_rp($this->month)).' '.$this->year.ec(' года'); }
	function nav_name() { return $this->day; }
	function parents() { return array("http://balancer.ru/user/{$this->id()}/posts/{$this->year}/{$this->month}/"); }

	function url() { return "http://balancer.ru/user/{$this->id()}/posts/{$this->year}/{$this->month}/{$this->day}/"; }

	function previous_day_link()
	{
		$day	= $this->day;
		$month	= $this->month;
		$year	= $this->year;

		while($year >= 2000)
		{
			$day--;
			if($day < 1)
			{
				$month--;
				if($month < 1)
				{
					$month = 12;
					$year --;
				}

				$day = date('t', strtotime("$year-$month-01"));
			}

			$time0	= intval(strtotime("$year-$month-$day 00:00:00"));
			$time9	= $time0 + 86400;
			if(objects_count('forum_post', array('poster_id' => $this->id(), "posted BETWEEN $time0 AND $time9")))
				return "http://balancer.ru/user/{$this->id()}/posts/$year/$month/$day/";
		}
	}
	
	function next_day_link()
	{
		$day	= $this->day;
		$month	= $this->month;
		$year	= $this->year;
		$toyear	= date('Y');

		while($year <= $toyear)
		{
			$days = date('t', strtotime("$year-$month-01"));
			$day++;
			if($day > $days)
			{
				$month++;
				if($month > 12)
				{
					$month = 1;
					$year++;
				}

				$day = 1;
			}

			$time0	= intval(strtotime("$year-$month-$day 00:00:00"));
			$time9	= $time0 + 86400;
			if(objects_count('forum_post', array('poster_id' => $this->id(), "posted BETWEEN $time0 AND $time9")))
				return "http://balancer.ru/user/{$this->id()}/posts/$year/$month/$day/";
		}
		
		return NULL;
	}

    function local_template_data_set()
	{
		$year	= $this->year;
		$month	= $this->month;
		$time0	= intval(strtotime("$year-$month-01 00:00:00"));
		$time0d	= intval(strtotime("$year-$month-{$this->day} 00:00:00"));
		$days	= date('t', $time0);

		$posts = objects_array('forum_post', array(
			'poster_id' => $this->id(),
			'posted BETWEEN '.$time0d.' AND '.($time0d+86400),
			'order' => 'posted',
		));

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
		
		return array(
			'year' => $year, 
			'month' => $month, 
			'today' => $time0d,
			'calend' => &$list,
			'posts' => &$posts,
			'previous_day_link' => $this->previous_day_link(),
			'next_day_link' => $this->next_day_link(),
		);
	}
}
