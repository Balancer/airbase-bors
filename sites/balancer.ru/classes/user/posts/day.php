<?php

//exit(500);

include_once('inc/strings.php');

class user_posts_day extends balancer_board_page
{
	private $year, $month, $day;

	function cache_static() { return false; } // Только так, пока показываются приватные сообщения!

	function _configure()
	{
		$page = $this->args('page');

		if($page == 'last')
		{
// Очень тормозной запрос:
// SELECT MAX(posted) FROM posts WHERE poster_id='853' AND is_deleted='' AND posted<'1388865600' LIMIT 1

//  USE KEY(poster_id)
			$max = $this->db('AB_FORUMS')->select('posts', 'posted', array(
				'poster_id' => $this->id(),
				'order' => 'posted DESC',
				'limit' => 1,
			));
			$page = date('Y/m/d', $max);
		}
		elseif($page == 'first')
		{
			$min = $this->db('AB_FORUMS')->select('posts', 'posted', array(
				'poster_id' => $this->id(),
				'order' => 'posted',
				'limit' => 1,
			));
			$page = date('Y/m/d', $min);
		}

		@list($this->year, $this->month, $this->day) = @explode('/', $page);
		if(empty($this->day))
			debug_hidden_log('__trap', 'empty day in '.$page);

		return parent::_configure();
	}

	function is_today()
	{
		return strtotime("{$this->year}-{$this->month}-{$this->day}") + 2*86400 > time();
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

	function title()
	{
		bors_function_include('time/month_name_rp');
		if(!$this->user())
			return ec("Неизвестный пользователь с ID={$this->id()}");

		return 
			$this->user()->title().ec(': Все сообщения за ')
			.$this->day.' '.strtolower(month_name_rp($this->month)).' '
			.$this->year.ec(' года'); 
	}

	function nav_name() { return $this->day; }
	function parents() { return array("http://www.balancer.ru/user/{$this->id()}/posts/{$this->year}/{$this->month}/"); }

	function url() { return "http://www.balancer.ru/user/{$this->id()}/posts/{$this->year}/{$this->month}/{$this->day}/"; }
	function url_ex($page) { return $this->url(); }

	function previous_day_link()
	{
		$prev = $this->db('AB_FORUMS')->select('posts', 'posted', array(
			'poster_id' => $this->id(), 
			'is_deleted' => false,
			'posted<' => strtotime("{$this->year}-{$this->month}-{$this->day}"),
			'order' => 'posted DESC',
			'limit' => 1,
		));

		if($prev)
			return 'http://www.balancer.ru/user/'.$this->id().'/posts/'.date('Y/m/d', $prev).'/';
		else
			return NULL;
	}

	function next_day_link()
	{
		$next = $this->db('AB_FORUMS')->select('posts', 'posted', array(
			'poster_id' => $this->id(), 
			'is_deleted' => false,
			'posted>=' => strtotime("{$this->year}-{$this->month}-{$this->day}")+86400,
			'order' => 'posted',
			'limit' => 1,
		));

		if($next)
			return 'http://www.balancer.ru/user/'.$this->id().'/posts/'.date('Y/m/d', $next).'/';
		else
			return NULL;
	}

    function body_data()
	{
		$year	= $this->year;
		$month	= $this->month;
		$time0	= intval(strtotime("$year-$month-01 00:00:00"));
		$time0d	= intval(strtotime("$year-$month-{$this->day} 00:00:00"));
		$days	= date('t', $time0);

		$posts = bors_find_all('balancer_board_post', array(
			'poster_id' => $this->id(),
			'is_deleted' => false,
			'posted BETWEEN '.$time0d.' AND '.($time0d+86400),
			'order' => 'posted',
		));
		$list	= array();

		for($day=1; $day<=$days; $day++)
		{
			$time9	= $time0 + 86400;
			$total = bors_count('balancer_board_post', array(
				'poster_id' => $this->id(),
				'is_deleted' => false,
				"posted BETWEEN $time0 AND $time9",
			));
			$time0 = $time9;

			if($total)
				$list[$day] = array(
					'url' => "http://www.balancer.ru/user/{$this->id()}/posts/{$this->year}/{$this->month}/{$day}/",
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
