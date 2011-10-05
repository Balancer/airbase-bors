<?php

function lp_android_market($market_id, $params)
{
	$market_url = "market://search?q={$market_id}";
	$url = "https://market.android.com/details?id={$market_id}";

	if(defval($params, 'display') == 'block')
	{
		$img_url = "http://qrcode.kaywa.com/img.php?s=4&d=".urlencode($market_url);
		$img = "<img src=\"{$img_url}\" width=\"140\" height=\"140\"/>";
		return "<a href=\"{$url}\">{$img}</a>";
	}

	// inline

	$data = bors_external_common::content_extract($url);
//	if(config('is_developer'))
//		var_dump($data);
	$title = preg_replace('! - Android Маркет$!', '', defval($data, 'title', $market_id));
	return "<a href=\"{$url}\">{$title}</a>";
}
