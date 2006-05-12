<?
	$regex = "!^(http://.*/news/)(\d{4})/(\d{2})/?$!";
	hts_data_prehandler_add($regex, 'body', 	"common_archives_month_get_body");
	hts_data_prehandler_add($regex, 'source',	create_function('$uri, $m', 'return ec("Это виртуальная страница! Не сохраняйте значение.");'));
	hts_data_prehandler_add($regex, 'title', 	create_function('$uri, $m', 'include_once("funcs/datetime.php"); return ec("Архив за ").strtolower(month_name($m[3]))." $m[2] ".ec("года");'));
	hts_data_prehandler_add($regex, 'nav_name', create_function('$uri, $m', 'include_once("funcs/datetime.php"); return month_name($m[3]);'));
	hts_data_prehandler_add($regex, 'parent',	create_function('$uri, $m', 'return array("{$m[1]}/{$m[2]}/");'));
	hts_data_prehandler_add($regex, 'child',	"common_archives_month_get_child");
	
    function common_archives_month_get_body($uri, $m=array())
	{
		$year  = $m[2];
		$month = $m[3];

		$hts = new DataBaseHTS;

		$time0 = intval(strtotime("$year-$month-1 00:00:00"));
		$mm = $month; $yy = $year;
		if($mm<12)	$mm = $mm+1;
		else {	$mm=1;	$yy++;}
		$time9 = intval(strtotime("$yy-$mm-1 00:00:00"))-1;

		$days = strftime("%d",$time9);
		$wd1  = strftime("%u",$time0);
		
		$week = array();

		$show_day = 0;
		while($show_day <= $days)
		{
			$day = array();
			for($wd=1; $wd<=7; $wd++)
			{
				if($show_day == 0 && $wd == $wd1)
					$show_day = 1;
					
				if($show_day == 0 || $show_day > $days)
					$day[] = array();
				else
				{
					$time0 = intval(strtotime("$year-$month-$show_day 00:00:00"));
					$mm = $month; $yy = $year; $dd = $show_day;
					if($dd<$days) $dd++;
					else
					{
						$dd=1;
						if($mm<12)	$mm = $mm+1;
						else { $mm=1; $yy++;}
					}
					$time9 = intval(strtotime("$yy-$mm-$dd 00:00:00"))-1;

					$curi = $uri.sprintf("%02d", $show_day)."/";

					if(!$hts->get_data($curi, 'create_time'))
						$curi = "";

					$day[] = array('uri'=>$curi, 'day'=>$show_day);
					$show_day++;
				}
			}
			$week[] = $day;
		}

		include_once("funcs/datetime.php");
		$data['month_name'] = strtolower(month_name($month));
		$data['year'] = $year;
		$data['week'] = $week;

		include_once("funcs/templates/assign.php");
		return template_assign_data("archives-month.htm", $data);
	}

    function common_archives_month_get_child($uri, $m=array())
	{
		$year  = $m[2];
		$month = $m[3];

		$hts = new DataBaseHTS;

		$time0 = intval(strtotime("$year-$month-1 00:00:00"));
		$mm = $month; $yy = $year;
		if($mm<12)	$mm = $mm+1;
		else {	$mm=1;	$yy++;}
		$time9 = intval(strtotime("$yy-$mm-1 00:00:00"))-1;

		return $hts->get_array_ex($uri, 'child', array(
			'start_time' => $time0,
			'stop_time'  => $time9,
			));

	}
?>
