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

function month_name($m)
{
		$mms = array(
			1 => '������',
			2 => '�������',
			3 => '����',
			4 => '������',
			5 => '���',
			6 => '����',
			7 => '����',
			8 => '������',
			9 => '��������',
			10 => '�������',
			11 => '������',
			12 => '�������');
			
		return $mms[intval($m)];
}

?>