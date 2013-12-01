<?php

class lcml_parser_airbase extends bors_lcml_parser
{
	function html($text)
	{
		$text = preg_replace('!(^|\s)http://(www\.)?balancer\.ru/g/p(\d+)(\D)!mi', '$1[post]$3[/post]$4', $text);
		$text = preg_replace('!(^|\s)http://forums\.airbase\.ru/\d+/\d+/[^/]+\.html?#p(\D)!mi', '$1[post]$3[/post]$4', $text);
		$text = preg_replace('!(^|\s)http://(www\.)?balancer\.ru/(society/)?\d+/\d+/[^/]+\.html?#p(\D)!mi', '$1[post]$3[/post]$4', $text);

		$text = preg_replace('!(^|\s)http://(www\.)?(balancer\.ru|forums\.airbase\.ru|wrk\/ru)\S*/\d{4}/\d{2}/t(\d+),(\d+)\S+\.html!mi', '$1[topic page=$5]$4[/topic]', $text);
		$text = preg_replace('!(^|\s)http://(www\.)?(balancer\.ru|forums\.airbase\.ru|wrk\/ru)\S*/\d{4}/\d{2}/t(\d+)\S+\.html!mi', '$1[topic]$4[/topic]', $text);
		return $text;
	}

	function text($text)
	{
		return $text;
	}
}
