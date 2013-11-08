<?php

class hbr_mybb_topics_redirect extends bors_page
{
	function pre_show()
	{
		return go($this->url_ex(NULL));
	}

	function title() { return '---'; }
	function url_ex($page) { return "http://home.balancer.ru/mybb/thread-{$this->id()}.html"; }
}
