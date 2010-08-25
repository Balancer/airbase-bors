<?php

function lp_mp3($text, $params)
{

	$mp3_url = urlencode($text);

//	$embed = "<embed type=\"application/x-shockwave-flash\" src=\"http://www.google.com/reader/ui/3247397568-audio-player.swf?audioUrl={$mp3_url}\" width=\"400\" height=\"27\" allowscriptaccess=\"never\" quality=\"best\" bgcolor=\"#ffffff\" wmode=\"window\" flashvars=\"playerMode=embedded\" />";
/*
$embed = <<< __EOT__
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab" width="500px" height="27px" align="middle">
<param name="allowScriptAccess" value="always" />
<param name="movie" value="http://mail.google.com/mail/html/audio.swf"/>
<param name="FlashVars" value="audioUrl={$mp3_url}"/>
<param name="quality" value="best" />
<param name="bgcolor" value="#EEEEEE" />
<param name="scale" value="noScale" />
<param name="wmode" value="opaque" />
<param name="salign" value="TL" />
<embed id=Player scale="noScale" salign="TL" src="http://mail.google.com/mail/html/audio.swf?audioUrl={$mp3_url}"
wmode="opaque" quality="best" bgcolor="#EEEEEE" width="500px" height="27px" name="Player" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
__EOT__;
*/

//$embed = "<iframe src=\"http://mail.google.com/mail/html/audio.swf?audioUrl={$mp3_url}\" style=\"width: 500px; height: 25px; border: 1px solid #aaa;\"></iframe>";

	//$embed = "<embed type=\"application/x-shockwave-flash\" src=\"http://www.google.com/reader/ui/3247397568-audio-player.swf?audioUrl={$mp3_url}\" width=\"400\" height=\"27\" allowscriptaccess=\"never\" quality=\"best\" bgcolor=\"#ffffff\" wmode=\"window\" flashvars=\"playerMode=embedded\" />";

//	$embed = "<embed src=\"http://webjay.org/flash/dark_player\" width=\"400\" height=\"40\" wmode=\"transparent\" flashVars=\"playlist_url={$mp3_url}&amp;skin_color_1=-145,-89,-4,5&skin_color_2=-141,20,0,0\" type=\"application/x-shockwave-flash\" />";

//	$embed = "<script type=\"text/javascript\" src=\"http://mediaplayer.yahoo.com/js\"></script>";

	$embed = "<embed src=\"http://www.google.com/reader/ui/3523697345-audio-player.swf\" flashvars=\"audioUrl={$mp3_url}\" width=\"400\" height=\"27\" quality=\"best\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>";

	return "<a href=\"$text\">$text</a><br/>Послушать: ".$embed;
}
