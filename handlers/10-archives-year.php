<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/archives/(\d+)/?$!", 'handler_archives_year');
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/guest/archives/(\d+)/?$!", 'handler_archives_year');

    function handler_archives_year($uri, $m=array())
	{
		require_once('funcs/templates/show.php');
		include_once('funcs/design/page_split.php');

        $hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);
		
		$data = array();
		$tpl_vars = 'modify_time source title user_first_name user_last_name year';

		$year = $m[2];

		$GLOBALS['title'] = $title = "$year год";
		$caching = false;
		$modify_time = time();
		$source = '';

		$GLOBALS['page_data_preset']['nav_name']["http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/archives/"] = "архив";
		$GLOBALS['page_data_preset']['parent']["http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/archives/"] = array("http://{$GLOBALS['cms']['conferences_host']}//");

		$GLOBALS['page_data_preset']['nav_name'][$uri] = "$year";
		$GLOBALS['page_data_preset']['parent'][$uri] = array("http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/archives/");

		$us = new User;
		$user_first_name = $us->data('first_name');
		$user_last_name = $us->data('last_name');

		
		$mms = array(
			1 => 'Январь',
			2 => 'Февраль',
			3 => 'Март',
			4 => 'Апрель',
			5 => 'Май',
			6 => 'Июнь',
			7 => 'Июль',
			8 => 'Август',
			9 => 'Сентябрь',
			10 => 'Октябрь',
			11 => 'Ноябрь',
			12 => 'Декабрь');

		$y = $year;
		$year = array();

		$y_arr = array();
		$y_arr['num'] = $y;
		$mons = array();
			
		$path = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}";
		
		if(preg_match("!/guest/!", $uri))
			$path = "$path/guest";

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

			$topics = $hts->dbh->get("SELECT COUNT(*) FROM hts_data_create_time WHERE value>=$time0 && value<=$time9 AND id LIKE '$path/thread%'");
			$posts = $hts->dbh->get("SELECT COUNT(*) FROM hts_data_create_time WHERE value>=$time0 && value<=$time9 AND id LIKE '$path/post%'");
			
			if($topics)
			{
				$mons[] = array(
					'num' => sprintf("%02d",$m),
					'name' => $mms[$m],
					'topics' => $topics,
					'posts' => $posts,
				);
			}
		}		

		$y_arr['month'] = $mons;

		$year[] = $y_arr;

		foreach(split(' ', $tpl_vars) as $var)
		{
			$data[$var] = $$var;
		}

		$data['conferences_uri'] = "$path";

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/archives-main/", $data);

		return true;
    }
?>
