<?php

bors_function_include('time/month_name_rp');

class balancer_board_archive_day extends base_page_list
{
	function nav_name() { return date('d', $this->id()); }
	function title() { return ec('Архив тем форума за ').$this->nav_name().' '.strtolower(month_name_rp(date('n', $this->id()))).' '.date('Y', $this->id()).ec(' года'); }

	function parents() { return array('/archive/'.date('Y/m/', $this->id())); }

	function main_class() { return 'balancer_board_topic'; }

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
			"posted BETWEEN $begin AND $end",
			"forum_id<>" => 16,
		));
	}

	function previous_day_link()
	{
		$prev = balancer_board_topic::find([
			'posted<' => strtotime("{$this->year}-{$this->month}-{$this->day}"),
			'order' => '-create_time',
		])->first()->create_time();

		if($prev)
			return 'http://forums.balancer.ru/archive/'.date('Y/m/d', $prev).'/';
		else
			return NULL;
	}

	function next_day_link()
	{
		$next = balancer_board_topic::find([
			'posted>=' => strtotime("{$this->year}-{$this->month}-{$this->day}")+86400,
			'order' => 'create_time',
		])->first()->create_time();

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
