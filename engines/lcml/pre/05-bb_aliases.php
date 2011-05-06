<?php
    function lcml_bb_aliases($txt)
    {
		$txt = preg_replace("!\[ab=\"([^\]]+?)\"\]!is", "[ab user=\"$1\"]", $txt);
		$txt = preg_replace("!\[ab=([^\]]+?)\]!is", "[ab user=\"$1\"]", $txt);

		$txt = preg_replace("!\[flash=(\d+),(\d+),(http://static.video.yandex.ru/.+?)\]!is", '[flash $1x$2]$3[/flash]', $txt);

		$txt = preg_replace("!\[t(\d+)\]!is", "[topic]$1[/topic]", $txt);

		//TODO: жёсткий костыль для фишек, типа [http://balancer.ru/forums/viewtopic.php?id=5465&p=1|[plk.jpg]]
		$txt = preg_replace('!\[ ( [^\]\|]+? ) \| \[ ( [^\]\|]+? \.jpg ) \]\]!x', '[url=$1][img $2 nohref][/url]', $txt);

		return $txt;
	}
