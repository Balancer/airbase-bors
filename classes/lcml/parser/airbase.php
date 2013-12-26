<?php

class lcml_parser_airbase extends bors_lcml_parser
{
	function html($text)
	{
		$text = preg_replace('!(^|\s)(http://(www\.)?balancer\.ru/g/p(\d+))(\D)!mi', '$1[post original="$2"]$4[/post]$5', $text);
		$text = preg_replace('!(^|\s)(http://(forums\.airbase\.ru|.*balancer\.ru|.*wrk\.ru)/\d+/\d+/[^/]+\.html?#p(\d+))(\D)!mi', '$1[post original="$2"]$4[/post]$5', $text);
		$text = preg_replace('!(^|\s)(http://(.*balancer\.ru|.*wrk\.ru)/(society/)?\d+/\d+/[^/]+\.html?#p(\d+))(\D)!mi', '$1[post original="$2"]$5[/post]$6', $text);

		$text = preg_replace('!(^|\s)(http://(.*balancer\.ru|forums\.airbase\.ru|.*wrk\.ru)\S*/\d{4}/\d{2}/t(\d+),(\d+)\S+\.html)!mi', '$1[topic page=$5 original="$2"]$4[/topic]', $text);
		$text = preg_replace('!(^|\s)(http://(.*balancer\.ru|forums\.airbase\.ru|.*wrk\.ru)\S*/\d{4}/\d{2}/t(\d+)\S+\.html)!mi', '$1[topic original="$2"]$4[/topic]', $text);
		return $text;
	}

	function text($text)
	{
		return $text;
	}

	function __unit_test($suite)
	{
	}
}
