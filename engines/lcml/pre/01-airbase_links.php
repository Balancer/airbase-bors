<?php

function lcml_airbase_links($text)
{
	$text = preg_replace("!http://(forums\.airbase\.ru|balancer.ru)\S+?\d{4}/\d{1,2}/t\d+\S+?\-\-\S+?#p(\d+)(\s|$| |\n)!m", 'http://balancer.ru/g/p$2$3', $text);

	return $text;
}
