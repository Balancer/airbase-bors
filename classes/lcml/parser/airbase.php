<?php

class lcml_parser_airbase extends bors_lcml_parser
{
	function html($text)
	{
		$text = preg_replace('!(^|\s)(http://(www\.)?balancer\.ru/g/p(\d+))(\D)!mi', '$1[post original="$2"]$4[/post]$5', $text);
		$text = preg_replace('!(^|\s)(http://(forums\.airbase\.ru|.*balancer\.ru|.*wrk\.ru)/\d+/\d+/[^/]+\.html?#p(\d+))(\D)!mi', '$1[post original="$2"]$4[/post]$5', $text);
		//todo: вычистить #p отвалившиеся
		$text = preg_replace('!(^|\s)(http://(.*balancer\.ru|.*wrk\.ru)/([\w/]+/)?\d+/\d+/[^/]+\.html?#p(\d+))(\D)!mi', '$1[post original="$2"]$5[/post]$6', $text);

		$text = preg_replace('!(^|\s)(http://(.*balancer\.ru|forums\.airbase\.ru|.*wrk\.ru)\S*/\d{4}/\d{2}/t(\d+),(\d+)\S+\.html)!mi', '$1[topic page=$5 original="$2"]$4[/topic]', $text);
		$text = preg_replace('!(^|\s)(http://(.*balancer\.ru|forums\.airbase\.ru|.*wrk\.ru)\S*/\d{4}/\d{2}/t(\d+)\S+\.html)!mi', '$1[topic original="$2"]$4[/topic]', $text);

//		http://www.balancer.ru/g/p3419010
//		$text = preg_replace('!^\s*http://pleer\.com/tracks/(\S+)\s*$!mi', '[pleercom]$1[/pleercom]', $text);

		// Добавляем источник к новостям АвиаПорт'а
		$text = preg_replace('@(http://www\.aviaport\.ru/(digest|news)/\d{4}/\d{1,2}/\d{1,2}/\d+\.html(?!\?))@', '$1?airbase', $text);
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
