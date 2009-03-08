<?php
function smarty_modifier_keywords_linkify($keywords_string)
{
	$result = array();
	foreach(explode(',', $keywords_string) as $key)
		$result[] = "<a href=\"http://forums.balancer.ru/tags/".urlencode(trim($key))."/\">".trim($key)."</a>";
	return join(', ', $result);
}
