<?php

function lp_gallery($text, $params)
{
	$path = $_SERVER['REQUEST_URI'];
	$thumb_path = "/cache/$path/468x468/";
	$result = "";
	foreach(explode("\n", $text) as $img)
	{
		$img = trim($img);
		if(!$img)
			continue;

		if(preg_match('/^(\S+) (.+?)$/', $img, $m))
		{
			$img = $m[1];
			$desc = "<br/><br/><small><center>{$m[2]}</center></small>";
		}
		else
			$desc = "";

		$img_url = str_replace('.jpg', '.htm', $path.$img);
		$img_thumb = $thumb_path.$img;
		$result .= "<a href=\"{$img_url}\"><img src=\"{$img_thumb}\" /></a>{$desc}<br/><br/>\n";
	}

	return $result;
}
