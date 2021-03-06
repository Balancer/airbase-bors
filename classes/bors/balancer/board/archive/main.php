<?php

class balancer_board_archive_main extends balancer_board_page
{
	function title() { return ec('Архив тем форума'); }
	function nav_name() { return ec('архив'); }

	function body_data()
	{
		return array(
			'years' => driver_mysql::factory(config('punbb.database'))->select_array('topics', 
				'YEAR(FROM_UNIXTIME(posted)) AS `year`, COUNT(*) AS `topics_count`', 
				array(
					'group' => '`year`',
					'order' => '`year`',
				)),
		);
	}

	function cache_static() { return config('static_forum') ? rand(3600, 7200) : 0; }
}

