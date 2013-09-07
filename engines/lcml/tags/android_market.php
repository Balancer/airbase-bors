<?php

function lp_android_market($market_id, $params)
{
	$market_url = "market://search?q={$market_id}";
//	$url = "https://market.android.com/details?id={$market_id}";
//	https://play.google.com/store/apps/details?id=com.tencent.research.drop
	$url = "https://play.google.com/store/apps/details?id={$market_id}";

	$data = bors_external_common::content_extract($url, array(
		// <div class="cover-container"> <img class="cover-image" src="https://lh6.ggpht.com/al1px-oC7qvC37yUdPuD533b-MHphe0fwEQ1hyCqnehyiLOEpXdUuCytRoNTzE4vKgw=w300-rw" alt="Cover art" itemprop="image"> </div>
		'default_image_regexp' => '!<img class="cover-image" src="([^"]+)"[^>]+>!',
	));
//	if(config('is_developer'))
//		var_dump($data);
	$title = preg_replace('! - [^\-]+$!', '', defval($data, 'title', $market_id));

	if(defval($params, 'display') == 'block')
	{
//		$img_url = "http://qrcode.kaywa.com/img.php?s=4&d=".urlencode($market_url);
//		$img = "<img src=\"{$img_url}\" width=\"140\" height=\"140\"/>";
		$html = lcml($data['bbshort']);
		return preg_replace("!^//(.+)$!m", "<div class=\"clear\">&nbsp;</div>\n<a href=\"{$url}\">{$img}</a></br>\n// $1", $html);
	}

	// inline

	return "<a href=\"{$url}\">{$title}</a>";
}
