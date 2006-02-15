<?if(isset($funcs_date_loaded) && $funcs_date_loaded) return; $funcs_date_loaded=1;

function jdate($jd)
{
    // Usage:  list($month,$day,$year,$weekday) = jdate($julian_day)

    $wkday = ($jd + 1) % 7;       // calculate weekday (0=Sun,6=Sat)
    $jdate_tmp = $jd - 1721119;
    $y = intval((4 * $jdate_tmp - 1)/146097);
    $jdate_tmp = 4 * $jdate_tmp - 1 - 146097 * $y;
    $d = intval($jdate_tmp/4);
    $jdate_tmp = intval((4 * $d + 3)/1461);
    $d = 4 * $d + 3 - 1461 * $jdate_tmp;
    $d = intval(($d + 4)/4);
    $m = intval((5 * $d - 3)/153);
    $d = 5 * $d - 3 - 153 * $m;
    $d = intval(($d + 5) / 5);
    $y = 100 * $y + $jdate_tmp;
    if($m < 10)
        $m += 3;
    else
    {
        $m -= 9;
        ++$y;
    }
    return Array($m, $d, $y, $wkday);
}

function full_time($time)
{
	return strftime("%d.%m.%Y %H:%M",$time);
}

function short_time($time)
{
	if(time() - $time < 86400 && strftime("%d",$time) == strftime("%d",time()))
		return strftime("%H:%M",$time);
	else
		return strftime("%d.%m.%Y",$time);
}

function is_today($time)
{
	if(time() - $time < 86400 && strftime("%d",$time) == strftime("%d",time()))
		return true;
		
//	echo "*{$GLOBALS['main_uri']}*";
	if(preg_match("!\d{4}/\d{1,2}/\d{1,2}/$!", @$GLOBALS['main_uri']))
		return true;

	return false;
}

function news_time($time)
{
	if(is_today($time))
		return strftime("%H:%M:%S",$time);
	
	if(time() - $time < 2*86400 && strftime("%d",$time) == strftime("%d",time()-86400))
		return ec("Вчера, ").strftime("%H:%M:%S",$time);
		
	return strftime("%d.%m.%Y %H:%M:%S",$time);
}

function month_name($m)
{
		$mms = array(
			1 => ec('Январь'),
			2 => ec('Февраль'),
			3 => ec('Март'),
			4 => ec('Апрель'),
			5 => ec('Май'),
			6 => ec('Июнь'),
			7 => ec('Июль'),
			8 => ec('Август'),
			9 => ec('Сентябрь'),
			10 => ec('Октябрь'),
			11 => ec('Ноябрь'),
			12 => ec('Декабрь'));
			
		return $mms[intval($m)];
}

function month_name_rp($m)
{
		$mms = array(
			1 => ec('Января'),
			2 => ec('Февраля'),
			3 => ec('Марта'),
			4 => ec('Апреля'),
			5 => ec('Мая'),
			6 => ec('Июня'),
			7 => ec('Июля'),
			8 => ec('Авгуса'),
			9 => ec('Сентября'),
			10 => ec('Октября'),
			11 => ec('Ноября'),
			12 => ec('Декабря'));
			
		return $mms[intval($m)];
}

?>
