<?php

class balancer_blogs_tag extends common_keyword
{
	function url() { return 'http://blogs.balancer.ru/tags/'.trim($this->title()).'/'; }

	static function linkify($keywords, $base_keywords = '', $join_char = ', ', $no_style = false, $base = 'http://blogs.balancer.ru/tags')
	{
		return parent::linkify($keywords, $base_keywords, $join_char, $no_style, $base);
	}
}
