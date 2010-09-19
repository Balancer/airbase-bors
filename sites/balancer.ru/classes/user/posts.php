<?php

class user_posts extends base_page
{
	function cache_static() { return 600; }

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

	function local_data()
	{
		$years = array();
/*		$first = $this->db('punbb')->select('posts', 'MIN(posted)', array('poster_id' => $this->id()));
		$last  = $this->db('punbb')->select('posts', 'MAX(posted)', array('poster_id' => $this->id()));
		$y0 = strftime('%Y', $first);
		$y9 = strftime('%Y', $last);
		for($y0; $y0<=$y9; $y0++)
			$years[] = $y0;
*/		
		$ynow = strftime('%Y');
		for($y=2000; $y<=$ynow; $y++)
		{
			$d0 = strtotime("$y-01-01 00:00:00");
			$d9 = strtotime("$y-12-31 23:59:59")+1;
			if($cnt = $this->db('punbb')->select('posts', 'count(*)', array('poster_id' => $this->id(), "posted BETWEEN $d0 AND $d9")))
				$years[$y] = $cnt;
		}
		
		return array('years' => $years);
	}
}
