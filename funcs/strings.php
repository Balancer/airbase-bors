<?
function remove_last_slash($s)
{
    return $s[strlen($s)-1]=='/' ? substr($s,0,-1) : $s;
}

function utf8_win($s)
{
    $s=str_replace("\xD0\xB0","‡",$s);  $s=str_replace("\xD0\x90","¿",$s);
    $s=str_replace("\xD0\xB1","·",$s);  $s=str_replace("\xD0\x91","¡",$s);
    $s=str_replace("\xD0\xB2","‚",$s);  $s=str_replace("\xD0\x92","¬",$s);
    $s=str_replace("\xD0\xB3","„",$s);  $s=str_replace("\xD0\x93","√",$s);
    $s=str_replace("\xD0\xB4","‰",$s);  $s=str_replace("\xD0\x94","ƒ",$s);
    $s=str_replace("\xD0\xB5","Â",$s);  $s=str_replace("\xD0\x95","≈",$s);
    $s=str_replace("\xD1\x91","∏",$s);  $s=str_replace("\xD0\x81","®",$s);
    $s=str_replace("\xD0\xB6","Ê",$s);  $s=str_replace("\xD0\x96","∆",$s);
    $s=str_replace("\xD0\xB7","Á",$s);  $s=str_replace("\xD0\x97","«",$s);
    $s=str_replace("\xD0\xB8","Ë",$s);  $s=str_replace("\xD0\x98","»",$s);
    $s=str_replace("\xD0\xB9","È",$s);  $s=str_replace("\xD0\x99","…",$s);
    $s=str_replace("\xD0\xBA","Í",$s);  $s=str_replace("\xD0\x9A"," ",$s);
    $s=str_replace("\xD0\xBB","Î",$s);  $s=str_replace("\xD0\x9B","À",$s);
    $s=str_replace("\xD0\xBC","Ï",$s);  $s=str_replace("\xD0\x9C","Ã",$s);
    $s=str_replace("\xD0\xBD","Ì",$s);  $s=str_replace("\xD0\x9D","Õ",$s);
    $s=str_replace("\xD0\xBE","Ó",$s);  $s=str_replace("\xD0\x9E","Œ",$s);
    $s=str_replace("\xD0\xBF","Ô",$s);  $s=str_replace("\xD0\x9F","œ",$s);
    $s=str_replace("\xD1\x80","",$s);  $s=str_replace("\xD0\xA0","–",$s);
    $s=str_replace("\xD1\x81","Ò",$s);  $s=str_replace("\xD0\xA1","—",$s);
    $s=str_replace("\xD1\x82","Ú",$s);  $s=str_replace("\xD0\xA2","“",$s);
    $s=str_replace("\xD1\x83","Û",$s);  $s=str_replace("\xD0\xA3","”",$s);
    $s=str_replace("\xD1\x84","Ù",$s);  $s=str_replace("\xD0\xA4","‘",$s);
    $s=str_replace("\xD1\x85","ı",$s);  $s=str_replace("\xD0\xA5","’",$s);
    $s=str_replace("\xD1\x86","ˆ",$s);  $s=str_replace("\xD0\xA6","÷",$s);
    $s=str_replace("\xD1\x87","˜",$s);  $s=str_replace("\xD0\xA7","◊",$s);
    $s=str_replace("\xD1\x88","¯",$s);  $s=str_replace("\xD0\xA8","ÿ",$s);
    $s=str_replace("\xD1\x89","˘",$s);  $s=str_replace("\xD0\xA9","Ÿ",$s);
    $s=str_replace("\xD1\x8A","˙",$s);  $s=str_replace("\xD0\xAA","⁄",$s);
    $s=str_replace("\xD1\x8B","˚",$s);  $s=str_replace("\xD0\xAB","€",$s);
    $s=str_replace("\xD1\x8C","¸",$s);  $s=str_replace("\xD0\xAC","‹",$s);
    $s=str_replace("\xD1\x8D","˝",$s);  $s=str_replace("\xD0\xAD","›",$s);
    $s=str_replace("\xD1\x8E","˛",$s);  $s=str_replace("\xD0\xAE","ﬁ",$s);
    $s=str_replace("\xD1\x8F","ˇ",$s);  $s=str_replace("\xD0\xAF","ﬂ",$s);
    return $s;
}

	function sklon($n, $s1, $s2, $s5) // 1 –Ω–æ–∂ 2 –Ω–æ–∂–∞ 5 –Ω–æ–∂–µ–π
	{
    	$ns=intval(substr($n,-1));
 		$n2=intval(substr($n,-2));

	    if($n2>=10 && $n2<=19) return $s5;
    	if($ns==1) return $s1;
	    if($ns>=2&&$ns<=4) return $s2;
    	if($ns==0 || $ns>=5) return $s5;
		return "–ù–µ–∏–∑–≤–µ—Å—Ç–Ω–∞—è –ø–∞—Ä–∞ '$n $s1'! –ü–æ–∂–∞–ª—É–π—Å—Ç–∞, —Å–æ–æ–±—â–∏ –æ–± —ç—Ç–æ–π –æ—à–∏–±–∫–µ –∞–¥–º–∏–Ω–∏—Å—Ç—Ä–∞—Ç–æ—Ä—É!";
	}
