<?php

function lcml_links_convert($text)
{
	// Android Market
	$text = preg_replace('!market://search\?q=pname:(\S+)!', '[android_market]$1[/android_market]', $text);
	$text = preg_replace('!https://market\.android\.com/details\?id=(\S+)!', '[android_market]$1[/android_market]', $text);
	return $text;
}
