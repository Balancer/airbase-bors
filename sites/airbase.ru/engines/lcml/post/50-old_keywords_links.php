<?php

function lcml_old_keywords_links($text)
{
	if(!config('lcml_old_keywords'))
		return $text;

	static $keywords = false;
	if(!$keywords)
		$keywords = file($_SERVER['DOCUMENT_ROOT'].'/links.txt');

	$main_obj = bors()->main_object();
	if(!$main_obj)
		return $text;

	$main_url = $main_obj->url();

	$found = false;
	foreach($keywords as $s)
	{
		if(preg_match('/^\s+/', $s))
		{
			$s = trim($s);

			if($found && !preg_match('/'.preg_quote($s, '/').'/', $main_url))
			{
				$text = preg_replace("/(?<=\s|^)(".preg_quote($found, '/').")(?=\s|$|\.|;|:)/ims", "<a href=\"{$s}\">{$found}</a>", $text);
			}

			$found = false;
			continue;
		}

		$s = trim($s);
		if(strpos($text, $s) !== false)
			if(preg_match("/(?<=\s|^)(".preg_quote($s, '/').")(?=\s|$|\.|;|:)/ims", $text))
				$found = $s;

	}

	return $text;
}
