<?php

function lp_android_market($market_id, $params)
{
	$market_url = "market://search?q={$market_id}";
	$img_url = "http://qrcode.kaywa.com/img.php?s=4&d=".urlencode($market_url);
	$img = "<img src=\"{$img_url}\" width=\"140\" height=\"140\"/>";
	return "<a href=\"https://market.android.com/details?id={$market_id}\">{$img}</a>";
}
