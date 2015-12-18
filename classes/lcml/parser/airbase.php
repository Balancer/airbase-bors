<?php

class lcml_parser_airbase extends bors_lcml_parser
{
	function html($text)
	{
		$text = preg_replace('!(^| |\s)(http://(www\.)?balancer\.ru/g/p(\d+))(\D)!mi', '$1[post original="$2"]$4[/post]$5', $text);
		$text = preg_replace('!(^| |\s)(http://(forums\.airbase\.ru|.*balancer\.ru|.*wrk\.ru)/\d+/\d+/[^/]+\.html?\?r=\d+#p(\d+))(\D)!mi', '$1[post original="$2"]$4[/post]$5', $text);
		$text = preg_replace('!(^| |\s)(http://(forums\.airbase\.ru|.*balancer\.ru|.*wrk\.ru)/\d+/\d+/[^/]+\.html?\?#p(\d+))(\D)!mi', '$1[post original="$2"]$4[/post]$5', $text);
		$text = preg_replace('!(^| |\s)(http://(forums\.airbase\.ru|.*balancer\.ru|.*wrk\.ru)/\d+/\d+/[^/]+\.html?#p(\d+))(\D)!mi', '$1[post original="$2"]$4[/post]$5', $text);
		//todo: вычистить #p отвалившиеся
		$text = preg_replace('!(^| |\s)(http://(forums\.airbase\.ru|.*balancer\.ru|.*wrk\.ru)/([\w/]+/)?\d+/\d+/[^/]+\.html?\?r=\d+#p(\d+))(\D)!mi', '$1[post original="$2"]$5[/post]$6', $text);
		$text = preg_replace('!(^| |\s)(http://(forums\.airbase\.ru|.*balancer\.ru|.*wrk\.ru)/([\w/]+/)?\d+/\d+/[^/]+\.html?\?#p(\d+))(\D)!mi', '$1[post original="$2"]$5[/post]$6', $text);
		$text = preg_replace('!(^| |\s)(http://(forums\.airbase\.ru|.*balancer\.ru|.*wrk\.ru)/([\w/]+/)?\d+/\d+/[^/]+\.html?#p(\d+))(\D)!mi', '$1[post original="$2"]$5[/post]$6', $text);

		$text = preg_replace('!(^| |\s)(http://(.*balancer\.ru|forums\.airbase\.ru|.*wrk\.ru)\S*/\d{4}/\d{2}/t(\d+),(\w+)\S+\.html)\?r=\d+!mi', '$1[topic page=$5 original="$2"]$4[/topic]', $text);
		$text = preg_replace('!(^| |\s)(http://(.*balancer\.ru|forums\.airbase\.ru|.*wrk\.ru)\S*/\d{4}/\d{2}/t(\d+),(\w+)\S+\.html)!mi', '$1[topic page=$5 original="$2"]$4[/topic]', $text);

		$text = preg_replace('!(^| |\s)(http://(.*balancer\.ru|forums\.airbase\.ru|.*wrk\.ru)\S*/\d{4}/\d{2}/t(\d+)\S+\.html\?r=\d+)!mi', '$1[topic original="$2"]$4[/topic]', $text);
		$text = preg_replace('!(^| |\s)(http://(.*balancer\.ru|forums\.airbase\.ru|.*wrk\.ru)\S*/\d{4}/\d{2}/t(\d+)\S+\.html)!mi', '$1[topic original="$2"]$4[/topic]', $text);

//		http://www.balancer.ru/g/p3419010
//		$text = preg_replace('!^\s*http://pleer\.com/tracks/(\S+)\s*$!mi', '[pleercom]$1[/pleercom]', $text);

		// Добавляем источник к новостям АвиаПорт'а
		$text = preg_replace('@(http://www\.aviaport\.ru/(digest|news)/\d{4}/\d{1,2}/\d{1,2}/\d+\.html(?!\?))@', '$1?airbase', $text);

		// Умершие ссылки
//		$text = preg_replace_callback("!(https?://(pic\.ipicture\.ru|ipicture\.ru|www\.uralweb\.ru)[^\"\s]+)!", function($m) { return save_format($m[1]); }, $text);

		$text = $this->warez_mask($text);

		return $text;
	}

	function text($text)
	{
		return $text;
	}

	function warez_mask($text)
	{
		$topic = $this->lcml->params('container');

		// Меняем torrents.ru на rutracker.org
		$text = preg_replace("!(https?://(www\.)?)torrents\.ru/([^\s\"]+)!", '$1rutracker.org/$3', $text);

		// Если топик не публичный, то ничего не маскируем.
		if($topic && !$topic->get('is_public', true))
			return $text;

		if($this->lcml->params('airbase-warez-enabled'))
			return $text;

		$text = preg_replace_callback("@(?<!=|\")(https?://(www\.)?(depositfiles\.com|lostfilm\.tv)/[^\s\"]+)@", [$this, 'warez_do_spoiler'], $text);
		$text = preg_replace_callback("!(https?://(www\.)?flibusta\.\w+/[^\s\"]+)!", [$this, 'warez_do_spoiler'], $text);
		$text = preg_replace_callback("!(https?://(www\.)?ifile.it/[^\s\"]+)!", [$this, 'warez_do_spoiler'], $text);
		$text = preg_replace_callback("!(https?://(www\.)?kinozal\.tv/[^\s\"]+)!", [$this, 'warez_do_spoiler'], $text);
		$text = preg_replace_callback("!(https?://(www\.)?rapidshare\.(com|ru)/[^\s\"]+)!", [$this, 'warez_do_spoiler'], $text);
		$text = preg_replace_callback("!(https?://(www\.)?rutor\.org/[^\s\"]+)!", [$this, 'warez_do_spoiler'], $text);
		$text = preg_replace_callback("!(https?://(www\.)?rghost\.ru/[^\s\"]+)!", [$this, 'warez_do_spoiler'], $text);
		$text = preg_replace_callback("!(https?://(www\.)?(rutracker\.org|film\.arjlover\.net)/[^\s\"]+)!", [$this, 'warez_do_spoiler'], $text);
		$text = preg_replace_callback("!(https?://(www\.)?tfile\.ru/[^\s\"]+)!", [$this, 'warez_do_spoiler'], $text);

		return $text;
	}

	function warez_do_spoiler($m, $pos=1)
	{
		$link = $m[$pos];
		if(preg_match('/\.jpg/', $link))
			return save_format($this->lcml($link, ['airbase-warez-enabled' => true]));

		return "[spoiler|Ссылка запрещена по требованию]".save_format($this->lcml($link, ['airbase-warez-enabled' => true]))."[/spoiler]";
	}

	function warez_do_spoiler2($m)
	{
		return $this->warez_do_spoiler($m, 2);
	}

	function __unit_test($suite)
	{
	}
}
