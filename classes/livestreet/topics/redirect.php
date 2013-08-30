<?php

class livestreet_topics_redirect extends livestreet_topic
{
	function pre_show()
	{
		return go($this->url_ex(NULL));
	}

	function url_ex($page) { return "http://ls.balancer.ru/blog/{$this->id()}.html"; }


}
