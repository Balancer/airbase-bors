<?php

bors_function_include('time/month_name_rp');

class balancer_board_archive_day extends base_page_list
{
	function nav_name() { return date('d', $this->id()); }
	function title() { return ec('Архив тем форума за ').$this->nav_name().' '.strtolower(month_name_rp(date('n', $this->id()))).' '.date('Y', $this->id()).ec(' года'); }

	function parents() { return array('/archive/'.date('Y/m/', $this->id())); }

	function main_class() { return 'forum_topic'; }
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
		));
	}
}
