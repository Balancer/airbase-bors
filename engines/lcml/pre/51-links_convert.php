<?php

function lcml_links_convert($text)
{
	// Android Market
	$text = preg_replace('!(^|\s)market://search\?q=pname:(\S+)!m', '\1[android_market]\2[/android_market]', $text);
	$text = preg_replace('!(^|\s)https://market\.android\.com/details\?id=(\S+)!m', '\1[android_market]\2[/android_market]', $text);
	return $text;
}
