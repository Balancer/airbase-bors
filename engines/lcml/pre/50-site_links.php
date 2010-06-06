<?php

function lcml_site_links($txt)
{
	$txt = preg_replace("!^(http://pda\.rbc\.ru/newsline/(\d+)\.shtml)$!ie", "lcml_site_links_get('rbc', $2, '$1');", $txt);
	$txt = preg_replace("!^(http://pda\.nr2\.ru/(\d+)\.html)$!ie", "lcml_site_links_get('nr2', $2, '$1');", $txt);
	return $txt;
}

function lp_import($url)
{
	if(preg_match("!^(http://(www\.)?lenta.ru/news/\d{4}/\d{1,2}/\d{1,2}/\w+/)$!i", $url))
		return lcml(lcml_site_links_get('lentaru', $url, $url));

	// TODO: сделать красиво
	return "<a href=\"$url\">$url</a>";
}

function lcml_site_links_get($origin, $id, $url0)
{
	require_once('inc/http.php');
	$url = call_user_func("lcml_site_links_url_{$origin}", $id);
	@list($title, $description, $content, $date, $image, $image_description) = call_user_func("lcml_site_links_get_{$origin}", bors_external_content::load($url));

	$text = array("[quote]");

	if($title)
		$text[] = "[b]".trim($title)."[/b]";

	if($description)
		$text[] = "[i]".trim($description)."[/i]";

	if($date)
		$text[] = "[small][i]".trim($date)."[/i][/small]";

	if($image)
		$text[] = "[img {$image} nohref|{$image_description}]";

	$text[] = html2bb(trim($content), $url0);
	$text[] = "// $url0";
	$text[] =" [/quote]";

	return join("\n\n", $text);
}

function lcml_site_links_url_rbc($id) { return "http://pda.rbc.ru/newsline/{$id}.shtml"; }
function lcml_site_links_get_rbc($content)
{
	if(preg_match('!<h3>(.+?)</h3>!', $content, $m))
		$title = $m[1];

	if(preg_match('!^(.+)<font color="blue">(.+?)</font>(.+)$!s', $content, $m))
	{
		$date = $m[2];
		$content = $m[1].$m[3];
	}

	if(preg_match('!<div class="newsList">(.+?)</div>!s', $content, $m))
		$text = $m[1];

	return array(@$title, '', @$text, @$date);
}

function lcml_site_links_url_nr2($id) { return "http://pda.nr2.ru/{$id}.html"; }
function lcml_site_links_get_nr2($content)
{
	if(preg_match('!^(.+)<h1 class=artit>(.+?)</h1>(.+)$!is', $content, $m))
	{
		$title = $m[2];
		$content = $m[1].$m[3];
	}

	if(preg_match('!^(.+)<h2 class=arstit>(.+?)</h2>(.+)$!is', $content, $m))
	{
		$description = $m[2];
		$content = $m[1].$m[3];
	}

	if(preg_match('!^(.+)<div class=ardat>(.+?)</div>(.+)$!is', $content, $m))
	{
		$date = $m[2];
		$content = $m[1].$m[3];
	}

	if(preg_match('!<div class=arbl>(.+?)<div class=blkbr>!is', $content, $m))
		$text = $m[1];

//	print_d($text);

	return array(@$title, @$description, @$text, @$date);
}

function lcml_site_links_url_lentaru($id) { return $id; }
function lcml_site_links_get_lentaru($content)
{
	if(preg_match('!^(.+)<h2>(.+?)</h2>(.+)$!is', $content, $m))
	{
		$title = $m[2];
		$content = $m[1].$m[3];
	}

//	if(preg_match('!^(.+)<h2 class=arstit>(.+?)</h2>(.+)$!is', $content, $m))
//	{
//		$description = $m[2];
//		$content = $m[1].$m[3];
//	}

	if(preg_match('!^(.+)<DIV class=dt>(.+?)</DIV>(.+)$!is', $content, $m))
	{
		$date = $m[2];
		$content = $m[1].$m[3];
	}

	if(preg_match('!^(.+)<TD class=zpic>.*?<img src=(\S+) (.+)$!is', $content, $m))
	{
		$image = $m[2];
		$content = $m[1].$m[3];
	}

	if(preg_match('!^(.+)<td class=zalt style="padding-top:10px">.*?<DIV class=dt>(.+?)</DIV>(.+)$!is', $content, $m))
	{
		$image_description = $m[2];
		$content = $m[1].$m[3];
	}

	if(preg_match('!></iframe>.*?</TD></TR></TABLE>(.+)<P class=links>!is', $content, $m))
	{
		$text = explode('<p>', $m[1]);
		$text = '<p>'.join('</p><p>', $text).'</p>';
	}

//	print_d($text);

	return array(@$title, @$description, @$text, @$date, @$image, @$image_description);
}
