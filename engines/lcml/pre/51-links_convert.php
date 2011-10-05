<?php

function lcml_links_convert($text)
{
	// Android Market
	$text = preg_replace('!^\s*https://market\.android\.com/details\?id=(\S+)\s*$!m', '\1[android_market display=block]\2[/android_market]', $text);
	$text = preg_replace('!^\s*market://search\?q=pname:(\S+)\s*$!m', '\1[android_market display=block]\2[/android_market]', $text);
	$text = preg_replace('!(^|\s)market://search\?q=pname:(\w+[\w\.]+\w+)!m', '\1[android_market display=inline]\2[/android_market]', $text);
	$text = preg_replace('!(^|\s)https://market\.android\.com/details\?id=(\w+[\w\.]+\w+)!m', '\1[android_market display=inline]\2[/android_market]', $text);

	// Android market images
	$text = preg_replace('!(^|\s)(https://ssl.gstatic.com/android/market/[^/]+/ss\S+)!m', '\1[img \2]', $text);

	return $text;
}
