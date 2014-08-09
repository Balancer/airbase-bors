<?php

class forum_tools_topic_reload extends base_object
{
	function can_be_empty() { return false; }
	function is_loaded() { return (bool) $this->topic(); }

	function pre_parse()
	{
		$topic = $this->topic();
		if(preg_match('!/t\d+,(\d+)!', @$_SERVER['HTTP_REFERER'], $m))
			$page = $m[1];
		else
			$page = 1;

		$topic->recalculate();
		return go($topic->url_ex($page));
	}

	function topic()
	{
		return bors_load('balancer_board_topic', $this->id(), array('no_load_cache' => true));
	}
}
