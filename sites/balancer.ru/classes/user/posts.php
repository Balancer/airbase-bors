<?php

class user_posts extends balancer_board_page
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


	function title() { return $this->user()->title().ec(': Все сообщения'); }
	function nav_name() { return ec('все сообщения'); }

	function body_data()
	{
		$years = [];
/*		$first = $this->db('AB_FORUMS')->select('posts', 'MIN(posted)', array('poster_id' => $this->id()));
		$last  = $this->db('AB_FORUMS')->select('posts', 'MAX(posted)', array('poster_id' => $this->id()));
		$y0 = strftime('%Y', $first);
		$y9 = strftime('%Y', $last);
		for($y0; $y0<=$y9; $y0++)
			$years[] = $y0;
*/
		$ynow = strftime('%Y');
		for($y=1999; $y<=$ynow; $y++)
		{
			$d0 = strtotime("$y-01-01 00:00:00");
			$d9 = strtotime("$y-12-31 23:59:59")+1;
			if($cnt = balancer_board_posts_pure::find([
				'poster_id' => $this->id(),
				'is_deleted' => false,
				"posted BETWEEN $d0 AND $d9",
			])->count())
				$years[$y] = $cnt;
		}

		return ['years' => $years];
	}
}
