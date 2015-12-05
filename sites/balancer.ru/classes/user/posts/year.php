<?php

class user_posts_year extends balancer_board_page
{
	function can_be_empty() { return false; }
	function is_loaded() { return (bool) $this->user(); }

	function cache_static() { return config('static_forum') ? 600 : 0; }

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

	function url() { return "http://www.balancer.ru/user/{$this->id()}/posts/{$this->args('page')}/"; }
	function url_ex($page) { return $this->url(); }

	function title() { return $this->user()->title().ec(': Все сообщения за ').$this->args('page').ec(' год'); }
	function nav_name() { return $this->args('page'); }

	function body_data()
	{
		$month = [];
		$y = $this->page();

		for($m=1; $m<=12; $m++)
		{
			$d0 = strtotime("$y-$m-01 00:00:00");
			$days = date('t', $d0);
			$d9 = $d0 + 86400*$days;

			if($cnt = balancer_board_posts_pure::find([
				'poster_id' => $this->id(),
				'is_deleted' => false,
				"posted BETWEEN $d0 AND $d9",
			])->count())
				$month[$m] = $cnt;
		}

		return ['month' => $month];
	}
}
