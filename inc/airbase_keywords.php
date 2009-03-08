<?php

function airbase_keywords_linkify($keywords_string, $base_keywords = '')
{
	$result = array();
	foreach(explode(',', $keywords_string) as $key)
		$result[] = "<a href=\"http://forums.balancer.ru/tags/".
			join("/", array_map('urlencode', balancer_board_keywords_tags::keywords_explode($key.','.$base_keywords)))
		."/\">".trim($key)."</a>";
	return join(', ', $result);
}
