<?php

class balancer_board_archive_month extends base_page
{
	function nav_name() { return strtolower(month_name(date('n', $this->id()))); }
	function title() { return ec('Архив тем форума за ').$this->nav_name().' '.date('Y', $this->id()).ec(' года'); }
	function parents() { return array('/archive/'.date('Y/', $this->id())); }

	static function id_prepare($id)
	{
		if(preg_match('!^(\d+)/(\d+)$!', $id, $m))
			return strtotime("{$m[1]}-{$m[2]}-01 00:00:00");

		return intval($id);
	}

	function cache_static() { return date('Y/n', $this->id()) == date('Y/n') ? rand(3600, 7200) : rand(30*86400, 90*86400); }
}