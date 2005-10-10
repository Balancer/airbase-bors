<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/archives/?$!", 'handler_archives');
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/guest/archives/?$!", 'handler_archives');

    function handler_archives($uri, $m=array())
	{
		require_once('funcs/templates/show.php');
		include_once('funcs/design/page_split.php');

        $hts = new DataBaseHTS;
		$uri = $hts->normalize_uri($uri);
		
		$data = array();
		$tpl_vars = 'modify_time source title user_first_name user_last_name year';

		$GLOBALS['title'] = $title = "�����";
		
		$GLOBALS['page_data_preset']['nav_name'][$uri] = "�����";
		$GLOBALS['page_data_preset']['parent'][$uri] = array("http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/");
		
		$caching = false;
		$modify_time = time();
		$source = '';

		$us = new User;
		$user_first_name = $us->data('first_name');
		$user_last_name = $us->data('last_name');

		$last = strftime("%Y",$hts->dbh->get("SELECT MAX(0+value) FROM hts_data_create_time"));
		$first = strftime("%Y",$hts->dbh->get("SELECT MIN(0+value) FROM hts_data_create_time"));

		$year = array();
		
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

		$path = "http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}";
		
		if(preg_match("!/guest/!", $uri))
			$path = "$path/guest";

		for($y = $last; $y >= $first; $y--)
		{
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
		}

		foreach(split(' ', $tpl_vars) as $var)
		{
			$data[$var] = $$var;
		}

		$data['conferences_uri'] = "$path";

		template_assign_and_show($uri, "http://{$GLOBALS['cms']['conferences_host']}/cms/templates/archives-main/", $data);

		return true;
    }
?>
