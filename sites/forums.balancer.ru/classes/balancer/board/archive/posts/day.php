<?php

class balancer_board_archive_posts_day extends balancer_board_paginated
{
	function nav_name() { return date('d', $this->id()); }
	function title() { return ec('Архив сообщений форума за ').$this->nav_name().' '.strtolower(blib_month::name_g(date('n', $this->id()))).' '.date('Y', $this->id()).ec(' года'); }

	function parents() { return array('/archive/posts/'.date('Y/m/', $this->id())); }

	function main_class() { return 'balancer_board_post'; }

	static function id_prepare($id)
	{
		if(preg_match('!^(\d+)/(\d+)/(\d+)$!', $id, $m))
			return strtotime("{$m[1]}-{$m[2]}-{$m[3]} 00:00:00");

		return intval($id);
	}

	function order() { return 'create_time'; }

	function where()
	{
		$begin = $this->id();
		$end   = $this->id() + 86400;

		return array_merge(parent::where(), array(
			"`posts`.`posted` BETWEEN $begin AND $end",
			'inner_join' => 'balancer_board_topic ON balancer_board_topic.id = topic_id',
			"is_public" => true,
			"forum_id NOT IN" => [25,43,45,91,203],
		));
	}

	function previous_day_link()
	{
		$prev = $this->db('AB_FORUMS')->select('topics', 'MAX(posted)', array(
			'posted<' => strtotime("{$this->year}-{$this->month}-{$this->day}"),
		));

		if($prev)
			return 'http://forums.balancer.ru/archive/'.date('Y/m/d', $prev).'/';
		else
			return NULL;
	}

	function next_day_link()
	{
		$next = $this->db('AB_FORUMS')->select('topics', 'MIN(posted)', array(
			'posted>=' => strtotime("{$this->year}-{$this->month}-{$this->day}")+86400,
		));

		if($next)
			return 'http://forums.balancer.ru/archive/'.date('Y/m/d', $next).'/';
		else
			return NULL;
	}

	function body_data()
	{
		$this->year = date('Y', $this->id());
		$this->month = date('m', $this->id());
		$this->day = date('d', $this->id());

		return array_merge(parent::body_data(), array(
			'previous_day_link' => $this->previous_day_link(),
			'next_day_link' => $this->next_day_link(),
		));
	}

	function is_public_access() { return true; }
}
