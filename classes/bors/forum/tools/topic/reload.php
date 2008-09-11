<?php

class forum_tools_topic_reload extends base_object
{
	function can_be_empty() { return true; }

	function pre_parse($data)
	{
		$topic = object_load('forum_topic', $this->id());
		if(preg_match('!/t\d+,(\d+)!', @$_SERVER['HTTP_REFERER'], $m))
			$page = $m[1];
		else
			$page = 1;
		
		$topic->recalculate();
		return go($topic->url($page));
	}
}
