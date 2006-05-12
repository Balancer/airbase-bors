<?
	$regex = "!^(http://.*/news/)(\d{4})/?$!";
	hts_data_prehandler_add($regex, 'body', 	"common_archives_post_year_get_body");
	hts_data_prehandler_add($regex, 'source',	create_function('$uri, $m', 'return ec("Это виртуальная страница! Не сохраняйте значение.");'));
	hts_data_prehandler_add($regex, 'title', 	create_function('$uri, $m', 'return ec("Архив за $m[2] год");'));
	hts_data_prehandler_add($regex, 'nav_name', create_function('$uri, $m', 'return "$m[2]";'));
	hts_data_prehandler_add($regex, 'parent',	create_function('$uri, $m', 'return array($m[1]);'));
	
    function common_archives_post_year_get_body($uri, $m=array())
	{
		$path = $m[1];
		$year = $m[2];

		include_once('funcs/datetime.php');

		include_once('funcs/design/page_split.php');

        $hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);
		
		$data = array();

		$data['source'] = '';
		$data['modify_time'] = time();
		$data['caching'] = false;
		$data['archive_uri'] = $path;

		$y = $year;

		$y_arr = array();
		$y_arr['num'] = $y;
		$mons = array();
		
		for($m = 1; $m <=12; $m++)
		{
			$time0 = intval(strtotime("$y-$m-1 00:00:00"));

			$mm = $m;
			$yy = $y;
			if($mm<12)
				$mm = $mm+1;
			else
			{
				$mm=1;
				$yy++;
			}

			$time9 = intval(strtotime("$yy-$mm-1 00:00:00"))-1;

			$updates = $hts->dbh->get("
					SELECT COUNT(*)
					FROM hts_data_create_time ct
						LEFT JOIN hts_data_flags fh ON ct.id=fh.id AND fh.value = 'hidden'
						LEFT JOIN hts_data_flags fd ON ct.id=fd.id AND fd.value = 'deleted'
					WHERE ct.value>=$time0
						AND ct.value<=$time9
						AND fh.id IS NULL
						AND fd.id IS NULL
				");

			if($updates)
			{
				$mons[] = array(
					'num' => sprintf("%02d",$m),
					'name' => month_name($m),
					'updates' => $updates,
				);
			}
		}		

		$data['month'] = $mons;

		$data['title'] = ec("Архив за $year год");
		$data['year'] = $year;

		include_once("funcs/templates/assign.php");
		return template_assign_data("archives-year.htm", $data);
    }
?>
