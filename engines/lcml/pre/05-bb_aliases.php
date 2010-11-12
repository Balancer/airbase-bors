<?php
    function lcml_bb_aliases($txt)
    {
		$txt = preg_replace("!\[ab=\"([^\]]+?)\"\]!is", "[ab user=\"$1\"]", $txt);
		$txt = preg_replace("!\[ab=([^\]]+?)\]!is", "[ab user=\"$1\"]", $txt);

		$txt = preg_replace("!\[flash=(\d+),(\d+),(http://static.video.yandex.ru/.+?)\]!is", '[flash $1x$2]$3[/flash]', $txt);

		return $txt;
	}
