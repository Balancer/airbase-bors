<?php

class forum_topic_ipbst extends base_page
{
	function pre_parse()
	{
		go($this->url()); 
	}
	
	function url()
	{
		$topic = object_load('forum_topic', $this->id());
		return $topic->url(intval($this->page()/25) + 1);
	}
}
