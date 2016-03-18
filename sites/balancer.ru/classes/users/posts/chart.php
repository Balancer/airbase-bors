<?php

class users_posts_chart extends balancer_board_page
{
	function can_be_empty() { return false; }
	function is_loaded() { return (bool) $this->user(); }

	function cache_static() { return config('static_forum') ? 600 : 0; }

	private $_user = false;
	function user()
	{
		if($this->_user === false)
			$this->_user = bors_user($this->id());

		return $this->_user;
	}

	function url() { return "http://www.balancer.ru/users/{$this->id()}/posts/chart/"; }
	function url_ex($page) { return $this->url(); }

	function title() { return $this->user()->title().ec(': График активности '); }
	function nav_name() { return 'активность'; }

	function parents() { return ['/users/'.$this->id().'/']; }

	function body_data()
	{
		$first = balancer_board_posts_pure::find([
				'poster_id' => $this->id(),
				'is_deleted' => false,
				'create_time>' => 86400,
			])->order('create_time')->first();

		$last = balancer_board_posts_pure::find([
				'poster_id' => $this->id(),
				'is_deleted' => false,
				'create_time<=' => time(),
			])->order('-create_time')->first();


		$stat = [];

		for($y=date('Y', $first->create_time()); $y<=date('Y'); $y++)
		{
			$last_m = ($y == date('Y')) ? date('m') : 12;

			for($m=1; $m<=$last_m; $m++)
			{
				$d0 = strtotime("$y-$m-01 04:00:01");
				$days = date('t', $d0);
				$d9 = $d0 + 86400*$days;

				if($cnt = balancer_board_posts_pure::find([
					'poster_id' => $this->id(),
					'is_deleted' => false,
					"posted BETWEEN $d0 AND $d9",
				])->count())
				{
					if(date('Y-m', $d0) == date('Y-m'))
					{
						if(date('d') < 3)
							continue;

						$cnt = $cnt*$days/date('d');
					}

					$stat[$d0*1000] = $cnt;
				}
			}
		}

		return ['stat' => $stat];
	}
}
