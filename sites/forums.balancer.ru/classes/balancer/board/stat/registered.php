<?php

class balancer_board_stat_registered extends balancer_board_page
{
	var $title = 'Количество регистраций новых пользователей';
	var $nav_name = 'активность регистраций';
	var $auto_map = true;
	function template() { return 'xfile:forum/_header.html'; }

	function body_data()
	{
		bors_use("http://code.highcharts.com/stock/highstock.js");
		bors_use("http://code.highcharts.com/modules/exporting.js");

		$dbh = new driver_mysql('AB_FORUMS');

		$regs_by_day = $dbh->select_array('users', 'YEAR(FROM_UNIXTIME(registered)) AS year,
				MONTH(FROM_UNIXTIME(registered)) AS month,
				DAY(FROM_UNIXTIME(registered)) AS day,
				registered AS ts,
				COUNT(*) AS count',
			array(
				'num_posts>' => 10,
				'registered>' => time() - 86400*365*3,
				'group' => 'YEAR(FROM_UNIXTIME(`registered`)), MONTH(FROM_UNIXTIME(`registered`)), DAY(FROM_UNIXTIME(`registered`))',
				'order' => 'registered',
			)
		);

		$regs_by_month = $dbh->select_array('users', 'YEAR(FROM_UNIXTIME(registered)) AS year,
				MONTH(FROM_UNIXTIME(registered)) AS month,
				registered AS ts,
				COUNT(*) AS count',
			array(
				'num_posts>' => 10,
				'registered>' => 0,
				'group' => 'YEAR(FROM_UNIXTIME(`registered`)), MONTH(FROM_UNIXTIME(`registered`))',
				'order' => 'registered',
			)
		);

		$start = $regs_by_day[0]['ts'];

		return array_merge(parent::body_data(), compact('start', 'regs_by_day', 'regs_by_month'));
	}

	function cache_static() { return rand(3600, 7200); }
}
