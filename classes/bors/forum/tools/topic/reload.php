<?php

class forum_tools_topic_reload extends base_object
{
	function can_be_empty() { return true; }

	function preParseProcess($data)
	{
		$topic = object_load('forum_topic', $this->id());
		$topic->recalculate();
		return go($topic->url());
	}
}
