<?php

class balancer_board_stat_forumsActivityYear extends bors_image_png
{
	var $points = 100;

	function dt($time)
	{
		return round($this->points * ($time - $this->first)/$this->age);
	}

	function show_image()
//	function body_data()
	{
		$db = new driver_mysql('AB_FORUMS');
		$this->first = $first = $db->select('posts', 'MIN(posted)', array('posted>' => time() - 365.24*86400));
		$this->last  = $last  = $db->select('posts', 'MAX(posted)', array());
		$this->age   = $age   = $last - $first;

		$stat = array();
		$maxs = array();
		$total_max = 0;

		for($x=0; $x<$this->points; $x++)
		{
			$begin = round($first + $age*$x/$this->points);
			$end   = round($first + $age*($x+1)/$this->points)-1;

			foreach($db->select_array('posts', 'forum_id, COUNT(*) as total', array(
				'posts.posted BETWEEN' => array($begin, $end),
				'inner_join' => 'topics ON topics.id = posts.topic_id',
				'group' => 'forum_id',
			)) as $s)
			{
				extract($s);
				$stat[$x][$forum_id] = $total;
				if($total > @$maxs[$forum_id])
					$maxs[$forum_id] = $total;
				if($total > $total_max)
					$total_max = $total;
			}
		}

		arsort($maxs);
//		var_dump($maxs);

		$colors = array(
			'FF0000',
			'00FF00',
			'0000FF',
			'808000',
			'800080',
			'008080',
		);

		$direct_colors = array(
			3 => 'b8d0dF', // Авиационный
			5 => '000000', // Космический
			6 => 'C3B091', // Общевоенный
			10 => 'FF8080', // Политический
			13 => 'c0c000', // Научно-технический
			25 => '91917B', // Морской
			78 => 'E02314', // Террор
			99 => '000000', // Linux
			180 => '904020', // Абхазия, Осетия...
		);

		$color_pos = 0;

		$g_colors = array();
		$g_chd = array();
		$g_titles = array();

		$series = array();
		$series_count = 0;

		foreach($maxs as $forum_id => $max)
		{
			$data_x = array();
			$data_y = array();

			$color = @$direct_colors[$forum_id];
			if(!$color)
				$color = @$colors[$color_pos++];

			if(!$color)
				continue;

			$g_colors[] = $color;
			$g_titles[] = strip_text(bors_load('balancer_board_forum', $forum_id)->title(), 20);
			$series[$forum_id] = $series_count++;

			for($x=0; $x<$this->points; $x++)
			{
				$total = @$stat[$x][$forum_id];
				$y = $total; // round($height*$total/$total_max);

				$data_x[] = intval($x);
				$data_y[] = intval($y);
			}

//			var_dump($data_x);
			$g_chd[] = /*join(',', $data_x).'|'.*/join(',', $data_y);
		}

		$months = array();
		$chxp = array();
		$pstart = strtotime("-1 year");
		$period = time() - $pstart;
		$cm = date('m');
		$cy = date('Y');
		$py = date('Y')-1;
		for($m=12; $m>=0; $m--)
		{
			$im = $cm - $m;
			if($im>0)
				$icur = strtotime("01.$im.$cy");
			else
				$icur = strtotime("01.".($im+12).".$py");
			$pos = intval(($icur - $pstart)/$period * 100);
			if($pos >= 0)
			{
				$chxp[] = $pos;
				$months[] = date('M', strtotime("-$m month"));
			}
		}

		$chm_src = array(
			// метка, форум, дата
			array('08.08.08', 180, '08.08.2008'),
			array('9-11', 3, '11.09.2001'),
			array('взрыв метро', 10, '02.06.2004'),
			array('взрыв самолётов', 78, '24.08.2004'),
			array('взрыв метро', 78, '01.09.2004'),
			array('ПАК ФА', 3, '29.01.2010'), // авиационный
			array('Манежка', 10, '11.12.2010'), // политический
			array('взрыв в Домодедово', 78, '24.01.2011'), // террор
			array('Ливия', 10, '20.02.2011'), // политический
			array('Фукусима', 13, '11.03.2011'), // Научно-технический
			array('бин Ладен', 10, '02.05.2011'), // политический
			array('выборы в Думу', 10, '04.11.2011'), // политический
		);

		$chm = array();
		foreach($chm_src as $x)
		{
			$dt = strtotime($x[2]);
			if($dt >= $pstart)
			{
				$chm[] = 'f'.$x[0].','
					.$direct_colors[$x[1]].','
					.$series[$x[1]].','
					.$this->dt($dt).',10';
			}
		}

		$chart = array(
//			'chxr' => 'x|y|z',
			'chxl' => '0:|'.join('|', $months), // chxl=0:|0|50|100|150|200|250|300|350|400|450|500
			'chxp' => '0,'.join(',', $chxp),
			'chxt' => 'x',
			'chxtc' => '0,5',
			'chs' => '1000x300',
			'cht' => 'lc',
			'chco' => join(',', $g_colors),
			'chds' => '0,'.$total_max, //0,78011,0,68513'
			'chd' => 't:'.join('|', $g_chd), //252,3999,11071,27745,29184,43148,78011,56062,39205,32861|2,663,3998,13671,11061,18959,42779,63539,68513,49128'
			'chdl' => join('|', $g_titles),
			'chtt' => 'Активность форумов forums.balancer.ru за последний год',
//			'chem' => 
//				'y;s=bubble_text_small;d=08.08.08;ds='.$series[180].';dp='.$this->dt(strtotime('08.08.2008'))
//				.'|y;s=bubble_text_small;d=9-11;ds='.$series[10].';dp='.$this->dt(strtotime('11.09.2001'))		,

			'chm' => join('|', $chm),
		);


		file_put_contents('/var/www/forums.balancer.ru/bors-site/classes/balancer/board/stat/chart.txt', print_r($chart, true));

		$url = 'https://chart.googleapis.com/chart?chid=' . md5(uniqid(rand(), true));

		$context = stream_context_create(
			array('http' => array(
				'method' => 'POST',
				'content' => http_build_query($chart)
			))
		);

		fpassthru(@fopen($url, 'r', false, $context));
	}

	function cache_static() { return 86400; }
}
