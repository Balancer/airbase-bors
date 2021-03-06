<?php

class forum_topic_ipbst extends bors_page
{
	function pre_parse()
	{
		go($this->url()); 
	}
	
	function url()
	{
		$topic = bors_load('balancer_board_topic', $this->id());
		if(!$topic)
		{
			bors_debug::syslog('incorrect-urls', $this->id());
			return 'http://www.balancer.ru/forums/';
		}

		return $topic->url_ex(intval($this->page()/25) + 1);
	}
}
