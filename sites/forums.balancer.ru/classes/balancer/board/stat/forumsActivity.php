<?php

class balancer_board_stat_forumsActivity extends bors_image_png
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
		$this->first = $first = /*strtotime('1.1.2008');/ */ $db->select('posts', 'MIN(posted)', array('posted>' => 100000));
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
//				'use_index' => 'index_for_joins',
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
			25 => '91917B', // Морской
			78 => 'E02314', // Террор
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

		$chart = array(
			'chxr' => '0,2000,'.date('Y', $last+86400*365/2),
			'chxt' => 'x',
			'chs' => '1000x300',
			'cht' => 'lc',
			'chco' => join(',', $g_colors),
			'chds' => '0,'.$total_max, //0,78011,0,68513'
			'chd' => 't:'.join('|', $g_chd), //252,3999,11071,27745,29184,43148,78011,56062,39205,32861|2,663,3998,13671,11061,18959,42779,63539,68513,49128'
			'chdl' => join('|', $g_titles),
			'chtt' => 'Активность форумов forums.balancer.ru',
//			'chem' => 
//				'y;s=bubble_text_small;d=08.08.08;ds='.$series[180].';dp='.$this->dt(strtotime('08.08.2008'))
//				.'|y;s=bubble_text_small;d=9-11;ds='.$series[10].';dp='.$this->dt(strtotime('11.09.2001'))		,
			'chm' => 'f08.08.08,'.$direct_colors[180].','.$series[180].','.$this->dt(strtotime('08.08.2008')).',10'
				.'|f9-11,'.$direct_colors[3].','.$series[3].','.$this->dt(strtotime('11.09.2001')).',10'
				.'|fвзрыв метро,'.$direct_colors[10].','.$series[10].','.$this->dt(strtotime('02.06.2004')).',10'
				.'|fвзрыв самолётов,'.$direct_colors[78].','.$series[78].','.$this->dt(strtotime('24.08.2004')).',10'
				.'|fвзрыв метро,'.$direct_colors[78].','.$series[78].','.$this->dt(strtotime('01.09.2004')).',10'
				.'|fПАК ФА,'.$direct_colors[3].','.$series[3].','.$this->dt(strtotime('29.01.2010')).',10'
				.'|fвыборы в Думу,'.$direct_colors[10].','.$series[10].','.$this->dt(strtotime('04.11.2011')).',10'
				,
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
