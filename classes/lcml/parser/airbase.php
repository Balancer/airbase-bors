<?php

class lcml_parser_airbase extends bors_lcml_parser
{
	function html($text)
	{
		$text = preg_replace('!(^|\s)http://(www\.)?balancer\.ru/g/p(\d+)(\D)!mi', '$1[post]$3[/post]$4', $text);
		$text = preg_replace('!(^|\s)http://forums\.airbase\.ru/\d+/\d+/[^/]+\.html?#p(\D)!mi', '$1[post]$3[/post]$4', $text);
		$text = preg_replace('!(^|\s)http://(www\.)?balancer\.ru/(society/)?\d+/\d+/[^/]+\.html?#p(\D)!mi', '$1[post]$3[/post]$4', $text);

		return $text;
	}

	function text($text)
	{
		return $text;
	}
}
